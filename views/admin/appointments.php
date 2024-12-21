<?php
require_once('../layouts/header.php');
require_once __DIR__ . '/../../models/Appointment.php';

$appointmentModel = new Appointment();
if ($permission == 'operator') {
    $appointments = $appointmentModel->getAllWithDoctorAndTreatment();
} elseif ($permission == 'doctor') {
    $appointments = $appointmentModel->getAllWithDoctorAndTreatmentByUserId($userId);
} else {
    header('location: views/auth/login.php');
}

?>

<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Dashboard /</span> Appointments </h4>

    <div class="card mb-5">
        <div class="row m-3">
            <div class="col-6">
                <div class="d-flex align-items-center m-3">
                    <i class="bx bx-search fs-4 lh-0"></i>
                    <input type="text" id="searchInput" class="form-control border-0 shadow-none" placeholder="Search by Appointment No " aria-label="Search..." />
                </div>
            </div>
            <div class="col-4">
                <div class="form-group my-3">
                    <input type="date" id="datePicker" class="form-control" />
                </div>
            </div>
            <div class="col-2">
                <div class="form-group my-3">
                    <button class="btn btn-primary d-inline" id="clear">Clear</button>
                </div>
            </div>
        </div>
        <hr>
        <!-- /.card-header -->
        <div class="card-body p-0 table-responsive">
            <table class="table table-striped mb-4">
                <thead>
                    <tr>
                        <th></th>
                        <th class="text-nowrap">Date</th>
                        <th class="text-nowrap">Appointment No</th>
                        <th class="text-nowrap">Treatment</th>
                        <?php if ($permission == 'operator') : ?>
                            <th class="text-nowrap">Doctor</th>
                        <?php endif; ?>
                        <th class="text-nowrap">Patient Name</th>
                        <th class="text-nowrap">Time Slot From</th>
                        <th class="text-nowrap">Time Slot To</th>
                        <th class="text-nowrap">Address</th>
                        <th class="text-nowrap">Telephone</th>
                        <th class="text-nowrap">Email</th>
                        <th class="text-nowrap">NIC</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (isset($appointments)) {
                        foreach ($appointments as $c) {
                            $time_slot_from = new DateTime($c['time_slot_from']);
                            $timeSlotFrom = $time_slot_from->format('h:i A');

                            $time_slot_to = new DateTime($c['time_slot_to']);
                            $timeSlotTo = $time_slot_to->format('h:i A');
                    ?>
                            <tr>
                                <td>
                                    <div>
                                        <a class="btn btn-sm btn-info m-2" href="<?= url('views/admin/appointment_edit.php?id=' . $c['id'] ?? '') ?>"><i class="bx bx-edit-alt"></i></a>
                                        <!-- <a class="btn btn-sm btn-danger m-2" href="#" onclick="confirmDelete(<?= $c['id']; ?>)"><i class="bx bx-trash"></i></a> -->
                                    </div>
                                </td>
                                <td class="text-nowrap appointment_date"><?= $c['appointment_date'] ?? ""; ?></td>
                                <td class="text-nowrap">#<?= $c['id'] ?? ""; ?> - <?= $c['appointment_no'] ?? ""; ?> </td>
                                <td class="text-nowrap"> <?= $c['treatment_name'] ?? ""; ?> </td>
                                <?php if ($permission == 'operator') : ?>
                                    <td class="text-nowrap"> <?= $c['doctor_name'] ?? ""; ?> </td>
                                <?php endif; ?>
                                <td class="text-nowrap"> <?= $c['patient_name'] ?? ""; ?> </td>
                                <td class="text-nowrap"> <?= $timeSlotFrom ?? ""; ?> </td>
                                <td class="text-nowrap"> <?= $timeSlotTo ?? ""; ?> </td>
                                <td class="text-nowrap"> <?= $c['address'] ?? ""; ?> </td>
                                <td class="text-nowrap"> <?= $c['telephone'] ?? ""; ?> </td>
                                <td class="text-nowrap"> <?= $c['email'] ?? ""; ?> </td>
                                <td class="text-nowrap"> <?= $c['nic'] ?? ""; ?> </td>

                            </tr>
                    <?php
                        }
                    }
                    ?>
                </tbody>
            </table>
        </div>
        <!-- /.card-body -->
    </div>
</div>

<?php require_once('../layouts/footer.php'); ?>
<script>
    $(document).ready(function() {
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
            console.log("selectedDate Date:", selectedDate); // Log each appointment date for debugging


            // Loop through each row in the table body
            $('tbody tr').each(function() {
                var appointmentDate = $(this).find('.appointment_date').text().trim();
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
            alert(selectedDate);
            filterAppointmentsByDate(selectedDate);
        });

    });
</script>