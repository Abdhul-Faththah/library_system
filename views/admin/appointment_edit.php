<?php
require_once('../layouts/header.php');
include BASE_PATH . '/models/Appointment.php';

$id = $_GET['id'] ?? null;
$appointmentModel = new Appointment();
$appointment = $appointmentModel->getById($id);

$time_slot_from = new DateTime($appointment['time_slot_from']);
$timeSlotFrom = $time_slot_from->format('h:i A');

$time_slot_to = new DateTime($appointment['time_slot_to']);
$timeSlotTo = $time_slot_to->format('h:i A');

?>


<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-8">
            <h4 class=""><span class="text-muted fw-light">Dashboard /</span>Edit Appointments </h4>
        </div>
        <div class="col-4">
            <div class="form-group d-flex justify-content-end ">
                <a href="<?= url('views/admin/appointments.php') ?>" class="btn btn-dark active" id="courier_service_modal_btn">
                    Back
                </a>
            </div>
        </div>
    </div>

    <div class="card m-3 p-5">

        <!-- /.card-header -->
        <div class="container">
            <form id="appointment-form" action="<?= url('services/ajax_functions.php') ?>">
                <div class="row">
                    <div class="col-12">
                        <div id="alert-container"></div>
                    </div>
                    <!-- Include hidden field for appointment ID -->
                    <input type="hidden" name="appointment_id" value="<?= $appointment['id']; ?>">
                    <input type="hidden" name="action" value="appointment-update">

                    <div class="mb-3 col-6">
                        <label for="appointment_date" class="form-label">Appointment Date:</label>
                        <input type="text" class="form-control" value="<?= $appointment['appointment_date']; ?>" readonly>
                    </div>

                    <div class="mb-3 col-6">
                        <label for="appointment_no" class="form-label">Appointment No:</label>
                        <input type="text" class="form-control" value="<?= $appointment['appointment_no']; ?>" readonly>
                    </div>

                    <div class="mb-3 col-6">
                        <label for="time_slot_from" class="form-label">Time Slot From:</label>
                        <input type="text" class="form-control" value="<?= $timeSlotFrom ?? ""; ?>" readonly>
                    </div>

                    <div class="mb-3 col-6">
                        <label for="time_slot_to" class="form-label">Time Slot To:</label>
                        <input type="text" class="form-control" value="<?= $timeSlotTo ?? ""; ?>" readonly>
                    </div>

                    <div class="mb-3 col-6">
                        <label for="doctor_name" class="form-label">Doctor:</label>
                        <input type="text" class="form-control" value="<?= $appointment['doctor_name'] ?? ""; ?>" readonly>
                    </div>

                    <div class="mb-3 col-6">
                        <label for="treatment_name" class="form-label">Treatment:</label>
                        <input type="text" class="form-control" value="<?= $appointment['treatment_name'] ?? ""; ?>" readonly>
                    </div>

                    <div class="mb-3 col-6">
                        <label for="patient_name" class="form-label">Patient Name:</label>
                        <input type="text" class="form-control" id="patient_name" name="patient_name" value="<?= $appointment['patient_name']; ?>">
                    </div>


                    <div class="mb-3 col-6">
                        <label for="address" class="form-label">Address:</label>
                        <input type="text" class="form-control" id="address" name="address" value="<?= $appointment['address']; ?>">
                    </div>

                    <div class="mb-3 col-6">
                        <label for="telephone" class="form-label">Telephone:</label>
                        <input type="text" class="form-control" id="telephone" name="telephone" value="<?= $appointment['telephone']; ?>">
                    </div>

                    <div class="mb-3 col-6">
                        <label for="email" class="form-label">Email:</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?= $appointment['email']; ?>">
                    </div>

                    <div class="mb-3 col-6">
                        <label for="nic" class="form-label">NIC:</label>
                        <input type="text" class="form-control" id="nic" name="nic" value="<?= $appointment['nic']; ?>">
                    </div>

                    <div class="mt-4 col-6 text-end">
                        <button type="button" class="btn rounded-pill btn-success" id="update-appointment">Update</button>
                    </div>


                </div>
            </form>
        </div>
        <!-- /.card-body -->
    </div>
</div>

</div>
<?php require_once('../layouts/footer.php'); ?>
<script>
    $(document).ready(function() {
        // Handle modal button click
        $('#update-appointment').on('click', function(e) {
            e.preventDefault();

            // Get the form element
            var form = $('#appointment-form')[0];
            $('#appointment-form')[0].reportValidity();

            // Check form validity
            if (form.checkValidity()) {

                // Serialize the form data
                var formData = $('#appointment-form').serialize();
                var formAction = $('#appointment-form').attr('action');

                // Perform AJAX request
                $.ajax({
                    url: formAction,
                    type: 'POST',
                    data: formData, // Form data
                    dataType: 'json',
                    success: function(response) {
                        showAlert(response.message, response.success ? 'success' : 'danger');
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
        $("#searchInput").on("input", function() {
            var searchTerm = $(this).val().toLowerCase();

            // Loop through each row in the table body
            $("tbody tr").filter(function() {
                // Toggle the visibility based on the search term
                $(this).toggle($(this).text().toLowerCase().indexOf(searchTerm) > -1);
            });
        });

        // Initial setup for the date picker
        $('#datePicker').val(getFormattedDate(new Date()));

        // Function to format date as YYYY-MM-DD
        function getFormattedDate(date) {
            var year = date.getFullYear();
            var month = (date.getMonth() + 1).toString().padStart(2, '0');
            var day = date.getDate().toString().padStart(2, '0');
            return `${year}-${month}-${day}`;
        }

        // Function to update table rows based on the selected date
        function filterAppointmentsByDate(selectedDate) {
            // Loop through each row in the table body
            $('tbody tr').each(function() {
                var appointmentDate = $(this).find('.appointment_date').text(); // Assuming date is in the 12th column
                $(this).toggle(appointmentDate === selectedDate);
            });
        }

        // Event handler for the "Filter" button
        $('#clear').on('click', function() {
            location.reload();
        });

        // Event handler for date picker change
        $('#datePicker').on('change', function() {
            var selectedDate = $(this).val();
            filterAppointmentsByDate(selectedDate);
        });

    });
</script>