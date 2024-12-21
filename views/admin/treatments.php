<?php
require_once('../layouts/header.php');
include BASE_PATH . '/models/Treatment.php';

$TreatmentModel = new Treatment();
$data = $TreatmentModel->getAll();

if ($permission != 'operator') dd('Access Denied...!');

?>

<!-- Content -->

<div class="container-xxl flex-grow-1 container-p-y">

    <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Dashboard /</span> Treatments
        <!-- Button trigger modal -->
        <button
            type="button"
            class="btn btn-primary float-end"
            data-bs-toggle="modal"
            data-bs-target="#createTreatment">
            Add New Treatment
        </button>
    </h4>

    <!-- Basic Bootstrap Table -->
    <div class="card">
        <h5 class="card-header">Treatments</h5>
        <div class="table-responsive text-nowrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Treatment Fee</th>
                        <th>Registration Fee</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    <?php
                    foreach ($data as $key => $t) {
                    ?>
                        <tr>
                            <td><i class="fab fa-angular fa-lg text-danger me-3"></i> <strong><?= $t['name'] ?? '' ?></strong></td>
                            <td><?= $t['description'] ?? '' ?></td>
                            <td class="text-right">LKR <?= number_format($t['treatment_fee'], 2) ?? 0; ?> </td>
                            <td class="text-right">LKR <?= number_format($t['registration_fee'], 2) ?? 0; ?> </td>
                            <td>
                                <?php if ($t['is_active'] == 1) { ?>
                                    <span class="badge bg-success">Active</span>
                                <?php } else { ?>
                                    <span class="badge bg-danger">In Active</span>
                                <?php } ?>
                            </td>
                            <td>
                                <div class="dropdown">
                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                        <i class="bx bx-dots-vertical-rounded"></i>
                                    </button>
                                    <div class="dropdown-menu">

                                        <a class="dropdown-item edit-treatment-btn" data-id="<?= $t['id']; ?>"><i class="bx bx-edit-alt me-1"></i> Edit</a>
                                        <a class="dropdown-item delete-treatment-btn" data-id="<?= $t['id']; ?>"><i class="bx bx-trash me-1"></i> Delete</a>

                                    </div>
                                </div>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
    <!--/ Basic Bootstrap Table -->

    <hr class="my-5" />


</div>

<!-- / Content -->

<!-- Modal -->
<div class="modal fade" id="createTreatment" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <form id="create-form" action="<?= url('services/ajax_functions.php') ?>" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalCenterTitle">Add New Treatment</h5>
                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="action" value="create_treatment">
                    <div class="row">
                        <div class="col mb-3">
                            <label for="nameWithTitle" class="form-label">Name</label>
                            <input
                                type="text"
                                required
                                id="name"
                                name="name"
                                class="form-control"
                                placeholder="Enter Name" />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col mb-3">
                            <label for="nameWithTitle" class="form-label">Description</label>
                            <input
                                type="text"
                                required
                                id="description"
                                name="description"
                                class="form-control"
                                placeholder="Enter Name" />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col mb-3">
                            <label for="nameWithTitle" class="form-label">Treatment Fee</label>
                            <input
                                type="number"
                                required
                                id="treatment_fee"
                                name="treatment_fee"
                                class="form-control"
                                placeholder="Enter Name" />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col mb-3">
                            <label for="nameWithTitle" class="form-label">Registration Fee</label>
                            <input
                                type="number"
                                required
                                id="registration_fee"
                                name="registration_fee"
                                class="form-control"
                                placeholder="Enter Name" />
                        </div>
                    </div>
                    <div class="row ">
                        <div class="mb-3">
                            <label for="exampleFormControlSelect1" class="form-label">Status</label>
                            <select class="form-select" id="is_active" aria-label="Default select example" name="is_active" required>
                                <option value="1">Active</option>
                                <option value="0">In Active</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3 mt-3">
                        <div id="alert-container"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        Close
                    </button>
                    <button type="button" class="btn btn-primary" id="create">Create</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Udpate Modal -->
<div class="modal fade" id="edit-treatment-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <form id="update-form" action="<?= url('services/ajax_functions.php') ?>" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalCenterTitle">Update Treatment</h5>
                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Close">
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="action" value="update_treatment">
                    <input type="hidden" name="id" value="" id="id">
                    <div class="row">
                        <div class="col mb-3">
                            <label for="nameWithTitle" class="form-label">Name</label>
                            <input
                                type="text"
                                required
                                id="edit_name"
                                name="name"
                                class="form-control"
                                placeholder="Enter Name" />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col mb-3">
                            <label for="nameWithTitle" class="form-label">Description</label>
                            <input
                                type="text"
                                required
                                id="edit_description"
                                name="description"
                                class="form-control"
                                placeholder="Enter Name" />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col mb-3">
                            <label for="nameWithTitle" class="form-label">Treatment Fee</label>
                            <input
                                type="number"
                                required
                                id="edit_treatment_fee"
                                name="treatment_fee"
                                class="form-control"
                                placeholder="Enter Name" />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col mb-3">
                            <label for="nameWithTitle" class="form-label">Registration Fee</label>
                            <input
                                type="number"
                                required
                                id="edit_registration_fee"
                                name="registration_fee"
                                class="form-control"
                                placeholder="Enter Name" />
                        </div>
                    </div>
                    <div class="row ">
                        <div class="mb-3">
                            <label for="exampleFormControlSelect1" class="form-label">Status</label>
                            <select class="form-select" id="edit_is_active" aria-label="Default select example" name="is_active" required>
                                <option value="1">Active</option>
                                <option value="0">In Active</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3 mt-3">
                        <div id="edit-alert-container"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        Close
                    </button>
                    <button type="button" class="btn btn-primary" id="update-treatment">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
require_once('../layouts/footer.php');
?>
<script src="<?= asset('assets/forms-js/treatments.js') ?>"></script>