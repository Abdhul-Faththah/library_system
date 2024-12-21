<?php
require_once 'BaseModel.php';

class Doctor extends BaseModel
{
    public $name;
    public $about;
    public $photo;
    public $is_active;
    public $user_id;

    protected function getTableName()
    {
        return "doctors";
    }

    protected function addNewRec()
    {
        $param = array(
            ':name' => $this->name,
            ':about' => $this->about,
            ':photo' => $this->photo,
            ':is_active' => $this->is_active,
            ':user_id' => $this->user_id
        );

        return $this->pm->run("INSERT INTO doctors(name, about, photo, is_active, user_id) VALUES (:name, :about, :photo, :is_active, :user_id)", $param);
    }

    protected function updateRec()
    {
        $param = array(
            ':name' => $this->name,
            ':about' => $this->about,
            ':photo' => $this->photo,
            ':is_active' => $this->is_active,
            ':user_id' => $this->user_id,
            ':id' => $this->id
        );

        return $this->pm->run("UPDATE doctors SET name = :name, about = :about, photo = :photo, is_active = :is_active, user_id = :user_id WHERE id = :id", $param);
    }


    function createDoctor($name, $about, $user_id, $photo = null, $is_active = 1)
    {
        $doctorModel = new Doctor();
        $doctorModel->name = $name;
        $doctorModel->about = $about;
        $doctorModel->user_id = $user_id;
        $doctorModel->is_active = $is_active;
        $doctorModel->photo = $photo;
        $doctorModel->save();

        if ($doctorModel) {
            return true; // Doctor created successfully
        } else {
            return false; // Doctor creation failed (likely due to database error)
        }
    }

    function updateDoctor($id, $name = null, $about = null, $user_id = null, $photo = null, $is_active = null)
    {
        // Initialize the Doctor model
        $doctorModel = new Doctor();

        // Retrieve the doctor by ID
        $existingDoctor = $doctorModel->getById($id); // Assuming findById method exists

        if (!$existingDoctor) {
            return false; // Doctor not found
        }

        // Update properties only if values are provided
        if ($name !== null) {
            $existingDoctor->name = $name;
        }
        if ($about !== null) {
            $existingDoctor->about = $about;
        }
        if ($user_id !== null) {
            $existingDoctor->user_id = $user_id;
        }
        if ($photo !== null) {
            $existingDoctor->photo = $photo;
        }
        if ($is_active !== null) {
            $existingDoctor->is_active = $is_active;
        }

        // Save the changes
        $updated = $existingDoctor->save(); // Assuming save method exists

        return $updated ? true : false;
    }

    function deleteDoctorByUserId($userId)
    {
        // Find the doctor associated with the user ID
        $doctor = $this->getDoctorByUserId($userId);
        if (!$doctor) {
            return true; // No doctor found for the user, proceed with user deletion
        }

        if (empty($doctor['id']))  return false;
        $doctorId = $doctor['id'];

        // Check if doctor has appointments
        if ($this->hasAppointments($doctorId)) {
            // If doctor has appointments, return false to prevent deletion
            return false;
        }

        // Delete doctor record
        return $this->deleteRec($doctorId);
    }

    public function hasAppointments($doctorId)
    {
        // Check if the doctor has any appointments
        $param = array(':doctor_id' => $doctorId);
        $appointments = $this->pm->run("SELECT COUNT(*) AS appointment_count FROM appointments WHERE doctor_id = :doctor_id", $param);

        return $appointments[0]['appointment_count'] > 0;
    }

    public function getDoctorByUserId($id)
    {
        $param = array(':id' => $id);
        return $this->pm->run("SELECT * FROM " . $this->getTableName() . " WHERE user_id = :id", $param, true);
    }
}
