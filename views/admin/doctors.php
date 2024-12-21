<?php
require_once('../layouts/header.php');
include BASE_PATH . '/models/Doctor.php';

$doctorModel = new Doctor();
$data = $doctorModel->getAll();

if ($permission != 'operator') dd('Access Denied...!');

?>

<!-- Content -->

<div class="container-xxl flex-grow-1 container-p-y">

    <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Dashboard /</span> Doctors

    </h4>

    <!-- Basic Bootstrap Table -->
    <div class="card">
        <h5 class="card-header">Doctors</h5>
        <div class="table-responsive text-nowrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>About</th>
                        <th>Image</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    <?php
                    foreach ($data as $key => $doctor) {
                    ?>
                        <tr>
                            <td><i class="fab fa-angular fa-lg text-danger me-3"></i> <strong><?= $doctor['name'] ?? '' ?></strong></td>
                            <td><?= $doctor['about'] ?? '' ?></td>
                            <td>
                                <?php if (isset($doctor['photo']) || !empty($doctor['photo'])) : ?>
                                    <img src="<?= asset('assets/uploads/' . $doctor['photo']) ?>" alt="user-avatar" class="d-block rounded m-3" width="80" id="uploadedAvatar">
                                <?php else : ?>
                                    <img src="<?= asset('assets/img/avatars/1.png') ?>" alt="user-avatar" class="d-block rounded m-3" width="80" id="uploadedAvatar">
                                <?php endif; ?>
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
<div class="modal fade" id="modalCenter" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <form id="create-form" action="<?= url('services/ajax_functions.php') ?>" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalCenterTitle">Add New User</h5>
                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="action" value="create_user">
                    <div class="row">
                        <div class="col mb-3">
                            <label for="nameWithTitle" class="form-label">User Name</label>
                            <input
                                type="text"
                                required
                                id="nameWithTitle"
                                name="user_name"
                                class="form-control"
                                placeholder="Enter Name" />
                        </div>
                    </div>
                    <div class="row ">
                        <div class="col mb-3">
                            <label for="emailWithTitle" class="form-label">Email</label>
                            <input
                                required
                                type="text"
                                name="email"
                                id="emailWithTitle"
                                class="form-control"
                                placeholder="xxxx@xxx.xx" />
                        </div>
                    </div>


                    <div class="row gy-2">
                        <div class="col orm-password-toggle">
                            <label class="form-label" for="basic-default-password1">Password</label>
                            <div class="input-group">
                                <input
                                    type="password"
                                    required
                                    name="password"
                                    class="form-control"
                                    id="passwordInput"
                                    placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                                    aria-describedby="basic-default-password1" />
                                <span id="basic-default-password1" class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
                            </div>
                        </div>
                        <div class="col form-password-toggle">
                            <label class="form-label" for="basic-default-password2">Confirm Password</label>
                            <div class="input-group">
                                <input
                                    type="password"
                                    required
                                    name="confirm_password"
                                    class="form-control"
                                    id="confirmPasswordInput"
                                    placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                                    aria-describedby="basic-default-password2" />
                                <span id="basic-default-password2" class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
                            </div>
                        </div>
                    </div>
                    <div class="row ">
                        <div class="mb-3">
                            <label for="exampleFormControlSelect1" class="form-label">Role</label>
                            <select class="form-select" id="permission" aria-label="Default select example" name="permission" required>
                                <option value="operator">Operator</option>
                                <option value="doctor">Doctor</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3 mt-3">
                        <div id="alert-container"></div>
                    </div>
                    <div class="mb-3 mt-3">
                        <div id="additional-fields">

                        </div>
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
<div class="modal fade" id="edit-user-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <form id="update-form" action="<?= url('services/ajax_functions.php') ?>" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalCenterTitle">Update User</h5>
                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="action" value="update_user">
                    <input type="hidden" id="user_id" name="id" value="">
                    <div class="row">
                        <div class="col mb-3">
                            <label for="nameWithTitle" class="form-label">User Name</label>
                            <input
                                type="text"
                                required
                                id="user-name"
                                name="user_name"
                                class="form-control"
                                placeholder="Enter Name" />
                        </div>
                    </div>
                    <div class="row ">
                        <div class="col mb-3">
                            <label for="emailWithTitle" class="form-label">Email</label>
                            <input
                                required
                                type="text"
                                name="email"
                                id="email"
                                class="form-control"
                                placeholder="xxxx@xxx.xx" />
                        </div>
                    </div>


                    <div class="row gy-2">
                        <div class="col orm-password-toggle">
                            <label class="form-label" for="basic-default-password1">Password</label>
                            <div class="input-group">
                                <input
                                    type="password"
                                    required
                                    name="password"
                                    class="form-control"
                                    id="password"
                                    placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                                    aria-describedby="basic-default-password1" />
                                <span id="basic-default-password1" class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
                            </div>
                        </div>
                        <div class="col form-password-toggle">
                            <label class="form-label" for="basic-default-password2">Confirm Password</label>
                            <div class="input-group">
                                <input
                                    type="password"
                                    required
                                    name="confirm_password"
                                    class="form-control"
                                    id="confirm-password"
                                    placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                                    aria-describedby="basic-default-password2" />
                                <span id="basic-default-password2" class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
                            </div>
                        </div>
                    </div>
                    <div class="row ">
                        <div class="mb-3">
                            <label for="exampleFormControlSelect1" class="form-label">Role</label>
                            <select class="form-select" id="edit_permission" aria-label="Default select example" name="permission" required>
                                <option value="operator">Operator</option>
                                <option value="doctor">Doctor</option>
                            </select>
                        </div>
                    </div>
                    <div class="row ">
                        <div class="mb-3">
                            <label for="exampleFormControlSelect1" class="form-label">Status</label>
                            <select class="form-select" id="is_active" aria-label="Default select example" id="is_active" name="is_active" required>
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
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
                    <button type="button" class="btn btn-primary" id="update-user">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
require_once('../layouts/footer.php');
?>
<script src="<?= asset('assets/forms-js/users.js') ?>"></script>