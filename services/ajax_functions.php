<?php
require_once '../config.php';
require_once '../helpers/AppManager.php';
require_once '../models/User.php';
require_once '../models/Doctor.php';
require_once '../models/Appointment.php';
require_once '../models/Payment.php';
require_once '../models/Treatment.php';


// Define target directory
$target_dir = "../assets/uploads/";

//Delete by treatment id
if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['id']) && isset($_GET['action']) && $_GET['action'] == 'delete_treatment') {
    try {

        $id = $_GET['id'];
        $treatmentModel = new Treatment();
        $treatmentDeleted = $treatmentModel->deleteTreatment($id);

        if ($treatmentDeleted) {
            echo json_encode(['success' => true, 'message' => 'Treatment deleted successfully!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to delete treatment.']);
        }
    } catch (PDOException $e) {
        // Handle database connection errors
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])  && isset($_POST['action']) && $_POST['action'] === 'update_treatment') {

    $id = $_POST['id'];
    $name = $_POST['name'];
    $description = $_POST['description'];
    $registration_fee = $_POST['registration_fee'];
    $treatment_fee = $_POST['treatment_fee'];
    $is_active = $_POST['is_active'];

    $treatment = new Treatment();
    $treatment->id = $id;
    $treatment->name = $name;
    $treatment->description = $description;
    $treatment->registration_fee = $registration_fee;
    $treatment->treatment_fee = $treatment_fee;
    $treatment->is_active = $is_active;
    $treatment->save();

    if ($treatment) {
        echo json_encode(['success' => true, 'message' => 'Treatment create successfully!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to create treatment.']);
    }
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'create_treatment') {


    $name = $_POST['name'];
    $description = $_POST['description'];
    $registration_fee = $_POST['registration_fee'];
    $treatment_fee = $_POST['treatment_fee'];
    $is_active = $_POST['is_active'];


    $treatment = new Treatment();
    $treatment->name = $name;
    $treatment->description = $description;
    $treatment->registration_fee = $registration_fee;
    $treatment->treatment_fee = $treatment_fee;
    $treatment->is_active = $is_active;
    $treatment->save();

    if ($treatment) {
        echo json_encode(['success' => true, 'message' => 'Treatment create successfully!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to create treatment.']);
    }
    exit;
}

//Get treatmnet by id
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id']) && isset($_GET['action']) &&  $_GET['action'] == 'get_treatment') {

    try {
        $id = $_GET['id'];
        $treatment = new Treatment();
        $treatment = $treatment->getById($id);
        if ($treatment) {
            echo json_encode(['success' => true, 'message' => "Treatment created successfully!", 'data' => $treatment]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to create user. May be user already exist!']);
        }
    } catch (PDOException $e) {
        // Handle database connection errors
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
    exit;
}
//create user
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'create_user') {

    try {
        $username = $_POST['user_name'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $permission = $_POST['permission'];

        $doctor_name = $_POST['doctor_name'] ?? null;
        $about_doctor = $_POST['about_doctor'] ?? null;

        // Get file information
        $image = $_FILES["image"] ?? null;
        $imageFileName = null;

        // Check if file is uploaded
        if (isset($image) && !empty($image)) {
            // Check if there are errors
            if ($image["error"] > 0) {
                echo json_encode(['success' => false, 'message' => "Error uploading file: " . $image["error"]]);
                exit;
            } else {
                // Check if file is an image
                if (getimagesize($image["tmp_name"]) !== false) {
                    // Check file size (optional)
                    if ($image["size"] < 500000) { // 500kb limit
                        // Generate unique filename
                        $new_filename = uniqid() . "." . pathinfo($image["name"])["extension"];

                        // Move uploaded file to target directory
                        if (move_uploaded_file($image["tmp_name"], $target_dir . $new_filename)) {
                            $imageFileName = $new_filename;
                        } else {
                            echo json_encode(['success' => false, 'message' => "Error moving uploaded file."]);
                            exit;
                        }
                    } else {
                        echo json_encode(['success' => false, 'message' => "File size is too large."]);
                        exit;
                    }
                } else {
                    echo json_encode(['success' => false, 'message' => "Uploaded file is not an image."]);
                    exit;
                }
            }
        }

        $userModel = new User();
        $created =  $userModel->createUser($username, $password, $permission, $email);
        if ($created) {

            if ($permission == 'doctor') {
                $user_id = $userModel->getLastInsertedUserId();

                $doctorModel = new Doctor();
                $doctorCreated =  $doctorModel->createDoctor($doctor_name,  $about_doctor, $user_id, $imageFileName);
            }

            echo json_encode(['success' => true, 'message' => "User created successfully!"]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to create user. May be user already exist!']);
        }
    } catch (PDOException $e) {
        // Handle database connection errors
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
    exit;
}

//Get user by id
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['user_id']) && isset($_GET['action']) &&  $_GET['action'] == 'get_user') {

    try {
        $user_id = $_GET['user_id'];
        $userModel = new User();
        $user = $userModel->getUserWithDoctorById($user_id);
        if ($user) {
            echo json_encode(['success' => true, 'message' => "User created successfully!", 'data' => $user]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to create user. May be user already exist!']);
        }
    } catch (PDOException $e) {
        // Handle database connection errors
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
    exit;
}

//Delete by user id
if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['user_id']) && isset($_GET['action']) && $_GET['action'] == 'delete_user') {
    try {
        $user_id = $_GET['user_id'];
        $permission = $_GET['permission'];

        $userModel = new User();
        $doctorModel = new Doctor();

        // Check permission and delete doctor if necessary
        if ($permission == 'doctor') {
            $doctorDeleted = $doctorModel->deleteDoctorByUserId($user_id);
            if ($doctorDeleted === false) {
                echo json_encode(['success' => false, 'message' => 'Doctor has appointments and cannot be deleted.']);
                exit;
            }
        }

        // Proceed to delete the user if doctor deletion was successful or not needed
        $userDeleted = $userModel->deleteUser($user_id);

        if ($userDeleted) {
            echo json_encode(['success' => true, 'message' => 'User deleted successfully!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to delete user.']);
        }
    } catch (PDOException $e) {
        // Handle database connection errors
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
    exit;
}

//update user
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_user') {

    try {
        $username = $_POST['user_name'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? "";
        $cpassword = $_POST['confirm_password'] ?? "";
        $permission = $_POST['permission'] ?? 'doctor';
        $is_active = $_POST['is_active'] == 1 ? 1 : 0;
        $id = $_POST['id'];

        // Validate inputs
        if (empty($username) || empty($email) || empty($password) || empty($cpassword)) {
            echo json_encode(['success' => false, 'message' => 'Required fields are missing!']);
            exit;
        }

        // Validate inputs
        if (($password) != $cpassword) {
            echo json_encode(['success' => false, 'message' => 'Passwords do not match..!']);
            exit;
        }

        // Validate email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo json_encode(['success' => false, 'message' => 'Invalid email address']);
            exit;
        }

        $userModel = new User();
        $updated =  $userModel->updateUser($id, $username, $password, $permission, $email, $is_active);
        if ($updated) {
            echo json_encode(['success' => true, 'message' => "User updated successfully!"]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update user. May be user already exist!']);
        }
    } catch (PDOException $e) {
        // Handle database connection errors
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
    exit;
}

//book_appointment
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'book_appointment') {

    try {
        $appointment = new Appointment();

        if (isset($_POST['id'])) {
            $appointment = $appointment->getById($_POST['id']);
        }

        $appointment->appointment_no = $_POST['appointment_no'] ?? '';
        $appointment->doctor_id = $_POST['doctor_id'] ?? null;
        $appointment->patient_name = $_POST['patient_name'] ?? null;
        $appointment->address = $_POST['address'] ?? null;
        $appointment->telephone = $_POST['telephone'] ?? null;
        $appointment->email = $_POST['email'] ?? null;
        $appointment->nic = $_POST['nic'] ?? null;
        $appointment->treatment_id = $_POST['treatment_id'] ?? null;
        $appointment->time_slot_from = $_POST['time_slot_from'] ?? null;
        $appointment->time_slot_to = $_POST['time_slot_to'] ?? null;
        $appointment->appointment_date = $_POST['appointment_date'] ?? null;

        $insertedId = $appointment->save();
        $treatment = new Treatment();
        $appointmentTreatment = $treatment->getById($appointment->treatment_id);

        if (isset($insertedId) && isset($appointmentTreatment)) {

            $payment = new Payment();
            $payment->appointment_id = $insertedId;
            $payment->registration_fee = $appointmentTreatment['registration_fee'] ?? 0;
            $payment->registration_fee_paid = 1;
            $payment->treatment_fee = $appointmentTreatment['treatment_fee'] ?? 0;
            $payment->quantity = 1;
            $payment->treatment_fee_paid = 0;
            $payment->save();

            // Response to send back
            echo json_encode(['success' => true, 'message' => 'Appointment booked successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Appointment booking have an error!']);
        }
    } catch (PDOException $e) {
        // Handle database connection errors
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
    exit;
}

//payment-save
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'payment-save') {

    try {

        $payment_id = $_POST['payment_id'] ?? null;
        $treatment_fee_paid = $_POST['treatment_fee_paid'] ? 1 : 0;
        $quantity = $_POST['quantity'] ?? 1;

        $payment = new Payment();
        $paymentData = $payment->getById($payment_id);

        if (isset($paymentData)) {
            $payment->id = $payment_id;
            $payment->treatment_fee_paid = $treatment_fee_paid ?? 0;
            $payment->quantity = $quantity ?? 0;
            $udpated = $payment->save();

            // Response to send back
            echo json_encode(['success' => true, 'message' => 'Payment udpated successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Payment have an error!']);
        }
    } catch (PDOException $e) {
        // Handle database connection errors
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
    exit;
}

//update appointment
if (
    $_SERVER['REQUEST_METHOD'] === 'POST'
    && isset($_POST['action'])
    && $_POST['action'] === 'appointment-update'
) {
    try {
        $appointment_id = $_POST['appointment_id'] ?? null;
        $patient_name = $_POST['patient_name'] ?? "";
        $address = $_POST['address'] ?? "";
        $telephone = $_POST['telephone'] ?? "";
        $email = $_POST['email'] ?? "";
        $nic = $_POST['nic'] ?? "";

        $appointment = new Appointment();
        $appointmentData = $appointment->getById($appointment_id);

        if (!empty($appointmentData)) {
            $appointment->id = $appointment_id;
            $appointment->patient_name = $patient_name;
            $appointment->address = $address;
            $appointment->telephone = $telephone;
            $appointment->address = $address;
            $appointment->email = $email;
            $appointment->nic = $nic;
            $appointment->save();


            // Response to send back
            echo json_encode(['success' => true, 'message' => 'Appointment udpated successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Appointment have an error!']);
        }
    } catch (PDOException $e) {
        // Handle database connection errors
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
    exit;
}

dd('Access denied..!');
