<?php
require_once('../layouts/header.php');
require_once __DIR__ . '/../../models/Doctor.php';

$doctorModel = new Doctor();
$doctors = $doctorModel->getAllActive();

?>
<div class="container">
    <h4 class="fw-bold py-3 my-4"><span class="text-muted fw-light">Dashboard /</span> Appointment Booking </h4>
    <section class="content m-3">
        <div class="container-fluid">
            <div class="row">
                <?php

                // Generate appointment slots
                foreach ($doctors as $doctor) {
                    $name = ($doctor['name'] ?? "");
                    $about = ($doctor['about'] ?? "");
                    $doctor_id = ($doctor['id'] ?? "");

                ?>

                    <div class="col-md-6 col-lg-4">
                        <div class="card my-3">
                            <div class="card-header"></div>
                            <div class="card-body">
                                <div class="col-md-7 mx-auto">
                                    <?php if (isset($doctor['photo']) || !empty($doctor['photo'])) : ?>
                                        <img src="<?= asset('assets/uploads/' . $doctor['photo']) ?>" alt="user-avatar" class="d-block rounded m-3" width="150" id="uploadedAvatar">
                                    <?php else : ?>
                                        <img src="<?= asset('assets/img/avatars/1.png') ?>" alt="user-avatar" class="d-block rounded m-3" width="150" id="uploadedAvatar">
                                    <?php endif; ?>
                                </div>
                                <h5 class="card-title"><?= $name ?></h5>
                                <p class="card-text">
                                    <?= $about ?>
                                </p>
                                <div class="col-md-12">
                                    <input class="form-control" type="week" name="week" id="week_date_<?= $doctor_id ?>" required>
                                </div>
                                <div class="col-md-12 mt-2 text-right">
                                    <a href="javascript:void(0)" class="btn btn-primary bookNowBtn" data-doctor-id="<?= $doctor_id ?>">Book Now</a>
                                </div>
                            </div>
                        </div>
                    </div>

                <?php
                }
                ?>
            </div>
        </div>

    </section>
</div>

<?php require_once('../layouts/footer.php'); ?>

<script>
    $(document).ready(function() {
        $(".bookNowBtn").on("click", function() {
            var doctorId = $(this).data('doctor-id');
            var selectedWeek = $("#week_date_" + doctorId).val();

            // Check if the selected week is not null
            if (selectedWeek !== null && selectedWeek !== "") {
                // Validate if the selected week is not in the past
                if (validateSelectedWeek(selectedWeek)) {
                    // Redirect with the selected week as a parameter
                    window.location.href = "available_channelings_by_doctor.php?week=" + selectedWeek + "&doctor_id=" + doctorId;
                } else {
                    // Show an error message or take appropriate action for invalid selection
                    alert("Invalid selection. Please choose a future week.");
                }
            } else {
                // Show an error message for null or empty selection
                alert("Please select a week before booking.");
            }
        });

        function validateSelectedWeek(selectedWeek) {
            // Extract the year and week number
            var match = selectedWeek.match(/^(\d{4})-W(\d{2})$/);
            if (!match) {
                return false; // Invalid format
            }

            var year = parseInt(match[1]);
            var week = parseInt(match[2]);

            // Get the first day (Monday) of the selected week
            var selectedDate = new Date(year, 0, (week) * 7 + 1);

            // Check if the selected week is in the future
            return selectedDate >= new Date();
        }
    });
</script>