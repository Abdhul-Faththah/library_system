<?php
require_once('../layouts/header.php');
require_once __DIR__ . '/../../models/DoctorAvailability.php';
require_once __DIR__ . '/../../models/Appointment.php';
require_once __DIR__ . '/../../helpers/AppointmentScheduler.php';

$weekInputValue = isset($_GET['week']) ? htmlspecialchars($_GET['week']) : '';
$doctorId = isset($_GET['doctor_id']) ? (int)$_GET['doctor_id'] : 0;

if (empty($doctorId) || empty($weekInputValue)) {
    header("Location: available_channelings.php");
    exit;
}

$days = getDays();
$today = new DateTime();

$appointmentModel = new Appointment();
$existingAppointments = $appointmentModel->getAllByColumnValue('doctor_id', $doctorId);

$doctorAvailabilityModel = new DoctorAvailability();
$availableSlots = $doctorAvailabilityModel->getAllActiveByDoctorId($doctorId);

$doctorModel = new Doctor();
$doctor = $doctorModel->getById($doctorId);

$treatmentModel = new Treatment();
$treatments = $treatmentModel->getAllActive();

$scheduler = new AppointmentScheduler($doctorId, $weekInputValue, $days, $availableSlots, $today, $existingAppointments, SLOT_DURATION);
$scheduler->displaySchedule();
?>

<!-- Modal -->
<div class="modal fade" id="appointmentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalCenterTitle">Book Appointment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="appointment-book" action="<?= url('services/ajax_functions.php') ?>">
                    <div class="row g-2">
                        <div class="col mb-4 form-group">
                            <label for="doctorId">Doctor</label>
                            <input type="text" class="form-control" id="doctorId" value="<?= $doctor['name'] ?? "" ?>" disabled>
                        </div>
                        <div class="col mb-4 form-group">
                            <label for="appointmentNo">Appointment Number</label>
                            <input type="text" class="form-control appointmentNo" id="appointmentNo" name="appointment_no" readonly>
                        </div>
                    </div>
                    <div class="row g-1">
                        <div class="col mb-4 form-group">
                            <label for="patientName">Patient Name</label>
                            <input type="text" class="form-control" id="patientName" name="patient_name" required>
                        </div>
                    </div>
                    <div class="row g-2">
                        <div class="col mb-4 form-group">
                            <label for="address">Address</label>
                            <input type="text" class="form-control" id="address" name="address" required>
                        </div>

                        <div class="col mb-4 form-group">
                            <label for="telephone">Telephone</label>
                            <input type="tel" class="form-control" id="telephone" name="telephone" required>
                        </div>
                    </div>
                    <div class="row g-2">
                        <div class="col mb-4 form-group">
                            <label for="email">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>

                        <div class="col mb-4 form-group">
                            <label for="nic">NIC</label>
                            <input type="text" class="form-control" id="nic" name="nic" required>
                        </div>
                    </div>
                    <div class="row g-1">
                        <div class="col mb-4 form-group">
                            <label for="treatmentId">Book Treatment</label>
                            <select class="form-select" id="treatment_id" aria-label="treatmentId" name="treatment_id" required>
                                <option selected="">Select Treatment</option>
                                <?php
                                foreach ($treatments as $treatment) {
                                    echo '<option value="' . ($treatment['id'] ?? '') . '" data-treatment-fees="' . ($treatment['treatment_fee'] ?? 0) . '" data-registration-fees="' . ($treatment['registration_fee'] ?? 0) . '">' . $treatment['name'] ?? "" . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="row g-1">
                        <div class="col mb-4 form-group text-right">
                            <div id="fees" class="fees card p-3" style="display:none">
                            </div>
                        </div>
                    </div>

                    <input type="hidden" id="timeSlotFrom" name="time_slot_from">
                    <input type="hidden" id="timeSlotTo" name="time_slot_to">
                    <input type="hidden" id="appointmentDate" name="appointment_date">
                    <!-- <input type="hidden" id="appointmentNo" name="appointment_no"> -->
                    <input type="hidden" name="action" value="book_appointment">
                    <input type="hidden" id="treatment_fee" name="treatment_fee">
                    <input type="hidden" id="registration_fee" name="registration_fee">
                    <input type="hidden" id="registration_fee_paid" name="registration_fee_paid" value="1">
                    <input type="hidden" id="doctor_id" name="doctor_id" value="<?= $doctorId ?>">

                    <div class="mb-3 mt-3">
                        <div id="alert-container"></div>
                    </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    Close
                </button>
                <button type="button" id="book-now" class="btn btn-primary">Book Now</button>
            </div>
            </form>
        </div>
    </div>
</div>
<?php require_once('../layouts/footer.php'); ?>

<script>
    $(document).ready(function() {

        $('#treatment_id').change(function() {
            $('#fees').hide();

            var selectedPresetId = $(this).val();
            if (selectedPresetId) {
                var presetElement = $('#treatment_id option[value="' + selectedPresetId + '"]');

                var treatmentFees = presetElement.data('treatment-fees');
                var registrationFees = presetElement.data('registration-fees');
                var total = treatmentFees + registrationFees;

                $('#fees').show();
                $('#fees').empty();

                // Set the content of the span elements
                if (registrationFees) $('#fees').append("<span class='d-block'><i class='text-muted '>Registration Fee:</i> LKR " + registrationFees + "</span>");
                if (treatmentFees) $('#fees').append("<span class='d-block'><i class='text-muted '>Treatment Fee:</i> LKR " + treatmentFees + "</span>");
                if (total) $('#fees').append("<span class='d-block'><i class='text-muted '><b>Total</b>:</i> LKR " + total + "</span>");

            } else {
                $('#fees').hide();
            }
        });

        // Function to generate a unique appointment ID
        function generateAppointmentId() {
            var timestamp = Math.floor(Date.now() / 1000); // Current timestamp in seconds
            // var randomNum = Math.floor(Math.random() * 1000); // Random number between 0 and 999
            var appointmentId = timestamp;
            return appointmentId;
        }

        // Handle modal button click
        $('.book-modal').on('click', function(e) {
            e.preventDefault();

            var dataId = $(this).data('id');
            var appointmentId = generateAppointmentId();

            // Get data attributes
            var dataId = $(this).data('id');
            var appointmentDate = $(this).data('appointment-date');
            var timeSlotFrom = $(this).data('time-slot-from');
            var timeSlotTo = $(this).data('time-slot-to');

            // Set values to hidden input fields in the modal form
            $('#dataId').val(dataId);
            $('#appointmentDate').val(appointmentDate);
            $('#timeSlotFrom').val(timeSlotFrom);
            $('#timeSlotTo').val(timeSlotTo);
            $('.appointmentNo').val(appointmentId);

            // Open the modal
            $('#appointmentModal').modal('show');
        });

        // Handle modal button click
        $('#book-now').on('click', function() {

            // Get the form element
            var form = $('#appointment-book')[0];
            $('#appointment-book')[0].reportValidity();

            // Check form validity
            if (form.checkValidity()) {
                // Serialize the form data
                var formData = $('#appointment-book').serialize();
                var formAction = $('#appointment-book').attr('action');

                // Perform AJAX request
                $.ajax({
                    url: formAction,
                    type: 'POST',
                    data: formData, // Form data
                    dataType: 'json',
                    success: function(response) {
                        showAlert(response.message, response.success ? 'primary' : 'danger');
                        $('.book-modal').modal('hide');
                        setTimeout(function() {
                            location.reload();
                        }, 1000);
                    },
                    error: function(error) {
                        // Handle the error
                        console.error('Error submitting the form:', error);
                    },
                    complete: function(response) {
                        // This will be executed regardless of success or error
                        console.log('Request complete:', response);
                    }
                });
            } else {
                var message = ('Form is not valid. Please check your inputs.');
                showAlert(message, 'danger');
            }


        });

    });
</script>