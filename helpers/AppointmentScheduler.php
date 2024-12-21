<?php
require_once __DIR__ . '/../models/DoctorAvailability.php';
require_once __DIR__ . '/../models/Appointment.php';
require_once __DIR__ . '/../models/Doctor.php';
require_once __DIR__ . '/../models/Treatment.php';

/**
 * Class AppointmentScheduler
 * 
 * This class is responsible for scheduling and managing appointments for a doctor.
 */
class AppointmentScheduler
{
    // Properties to store various parameters and data needed for appointment scheduling.
    private $doctorId;
    private $weekInputValue;
    private $firstDayOfWeek;
    public $days;
    private $availableSlots;
    private $today;
    private $existingAppointments;
    private $slotDuration;

    /**
     * Constructor for the AppointmentScheduler class.
     * 
     * @param int        $doctorId              The ID of the doctor.
     * @param string     $weekInputValue        The input value representing the week in "YYYY-WW" format.
     * @param array      $days                  Array of days in the week.
     * @param array      $availableSlots        Array of available time slots.
     * @param DateTime   $today                 Current date and time.
     * @param array      $existingAppointments  Array of existing appointments.
     * @param string     $slotDuration          Duration of each time slot.
     */
    public function __construct($doctorId, $weekInputValue, $days, $availableSlots, $today, $existingAppointments, $slotDuration)
    {
        // Assign provided values to class properties.
        $this->doctorId = $doctorId;
        $this->weekInputValue = $weekInputValue;
        $this->days = $days;
        $this->availableSlots = $availableSlots;
        $this->today = $today;
        $this->existingAppointments = $existingAppointments;
        $this->slotDuration = new DateInterval($slotDuration); // Convert slot duration to DateInterval.

        // Initialize the object.
        $this->initialize();
    }

    /**
     * Initialize method to set up the first day of the week based on the provided week input value.
     */
    private function initialize()
    {
        // Extract year and week from the input value in "YYYY-WW" format.
        list($year, $week) = sscanf($this->weekInputValue, "%d-W%d");

        // Set the first day of the week using ISO week date.
        $this->firstDayOfWeek = new DateTime();
        $this->firstDayOfWeek->setISODate($year, $week, 1);
    }

    /**
     * Display the schedule of available appointments for a doctor.
     */
    public function displaySchedule()
    {
        // Create a Doctor model instance and fetch the doctor details by ID.
        $doctorModel = new Doctor();
        $doctor = $doctorModel->getById($this->doctorId);

        echo $this->renderDoctorHeader($doctor);

        // Output the appointment schedule section.
        echo '<section class="content m-3">';
        echo '<div class="container-fluid">';

        if (!empty($this->availableSlots)) {
            // Loop through available slots and check if they match the current day.

            // Loop through each day in the week.
            foreach ($this->days as $dayCount => $day) {

                // Calculate the current date based on the first day of the week and the day count.
                $currentDate = clone $this->firstDayOfWeek;
                $currentDate->modify("+$dayCount days");

                $currentDateFullString = $currentDate->format("l, F j, Y");
                $currentDateString = $currentDate->format("Y-m-d");


                foreach ($this->availableSlots as $slot) {
                    $slotDay = $slot['day'] ?? "";

                    if ($slotDay == $day) {
                        $sessionFrom = new DateTime($slot['session_from']);
                        $sessionTo = new DateTime($slot['session_to']);
                        $currentSlot = clone $sessionFrom;

                        // Output a row for each day with available slots.
                        echo '<div class="row my-5 border rounded border-secondary"><h3 class="mt-4 text-capitalize">' . $currentDateFullString . '</h3>';

                        // Loop through time slots for the current day.
                        while ($currentSlot < $sessionTo) {
                            $time_slot_from = $currentSlot->format('h:i A');
                            $time_slot_to = $currentSlot->add($this->slotDuration)->format('h:i A');

                            echo '<div class="col-3">';
                            echo '<div class="card m-3 mb-5">';
                            echo '<div class="card-body">';
                            echo '<h5 class="card-title">Appointment Slot</h5>';
                            echo '<p class="card-text">' . $time_slot_from . ' to ' . $time_slot_to . '</p>';

                            $currentSlot->sub($this->slotDuration);
                            $timeSlotFrom = $currentSlot->format('H:i:s');
                            $timeSlotTo = $currentSlot->add($this->slotDuration)->format('H:i:s');

                            // Check the availability of the time slot.
                            if ($this->today > $currentDate) {
                                echo '<button disabled class="btn btn-warning d-flex">Expired</button>';
                            } else if ($this->isTimeSlotAvailable($timeSlotFrom, $this->existingAppointments, $currentDateString)) {
                                echo '<button type="button" data-appointment-date="' . $currentDateString . '" data-time-slot-to="' . $timeSlotTo . '" data-time-slot-from="' . $timeSlotFrom . '" class="btn btn-primary active book-modal" data-bs-toggle="modal" data-bs-target="#appointmentModal">Available</button>';
                            } else {
                                echo '<button disabled class="btn btn-danger text-center">Booked</button>';
                            }

                            echo '</div>';
                            echo '</div>';
                            echo '</div>';
                        }

                        echo '</div>';
                    }
                }
            }
        } else {
            echo '<h1 class="card p-3 text-danger text-center">No doctor availabilities.</h1>';
        }

        // Close the HTML container tags.
        echo '</div>';
        echo '</section>';
        echo '</div>';
    }

    /**
     * Renders the doctor header with their details.
     *
     * @param array $doctor The doctor details.
     * 
     * @return string HTML output for the doctor header.
     */
    private function renderDoctorHeader($doctor)
    {
        return '<div class="container">
                    <div class="row">
                        <div class="col-8">
                            <h2 class="mx- mt-5">' . ($doctor['name'] ?? "") . '</h2>
                            <h5 class="mx- mb-3">' . ($doctor['about'] ?? "") . '</h5>
                        </div>
                        <div class="col-4">
                            <div class="form-group d-flex justify-content-end mx-5 my-5">
                                <a href="' . url('views/admin/available_channelings.php') . '" class="btn btn-dark active">Back</a>
                            </div>
                        </div>
                    </div>
                </div>';
    }



    /**
     * Check if a given time slot is available based on existing appointments.
     *
     * @param string $timeSlot              The time slot to check.
     * @param array  $existingAppointments  Array of existing appointments.
     * @param string $currentDateString     The current date string.
     *
     * @return bool                         Returns true if the time slot is available; false otherwise.
     */
    private function isTimeSlotAvailable($timeSlot, $existingAppointments, $currentDateString)
    {
        // Convert the provided time slot to DateTime objects
        $slotFrom = new DateTime($timeSlot);
        $slotTo = clone $slotFrom;
        $slotTo->add(($this->slotDuration)); // Add the slot duration to get the end time

        // Iterate through existing appointments to check for conflicts
        foreach ($existingAppointments as $appointment) {
            // Convert existing appointment time slots to DateTime objects
            $appointmentFrom = new DateTime($appointment['time_slot_from']);
            $appointmentTo = new DateTime($appointment['time_slot_to']);
            $appointmentDate = $appointment['appointment_date'] ?? null;

            // Check for time slot conflicts
            if (!($slotTo <= $appointmentFrom || $slotFrom >= $appointmentTo) && $currentDateString == $appointmentDate) {
                // If there is a conflict, the time slot is not available
                return false;
            }
        }

        // If no conflicts were found, the time slot is available
        return true;
    }
}
