<?php

require_once 'BaseModel.php';

class Appointment extends BaseModel
{
    public $appointment_no;
    public $doctor_id;
    public $patient_name;
    public $address;
    public $telephone;
    public $email;
    public $nic;
    public $treatment_id;
    public $time_slot_from;
    public $time_slot_to;
    public $appointment_date;

    protected function getTableName()
    {
        return "appointments";
    }

    public function getById($id)
    {
        $param = array(':id' => $id);
        return $this->pm->run(
            "SELECT *, tm.name AS treatment_name, dr.name AS doctor_name, ap.id AS id FROM appointments AS ap 
            JOIN treatments AS tm ON tm.id = ap.treatment_id 
            JOIN doctors AS dr ON dr.id = ap.doctor_id 
            WHERE ap.id = :id",
            $param,
            true
        );
    }

    // Method to retrieve a record by its ID or appointment_no from the associated table
    public function getByIdOrAppointmentNo($id, $appointmentNo)
    {
        // Check if either $id or $appointmentNo is provided
        if (!empty($id)) {
            $condition = "id = :id";
            $param = array(':id' => $id);
        } elseif (!empty($appointmentNo)) {
            $condition = "appointment_no = :appointment_no";
            $param = array(':appointment_no' => $appointmentNo);
        } else {
            // Both $id and $appointmentNo are empty, return null or handle it accordingly
            return null;
        }

        // Build and execute the SQL query
        return $this->pm->run("SELECT * FROM " . $this->getTableName() . " WHERE $condition", $param, true);
    }

    // Method to retrieve a record by both ID and appointment_no from the associated table
    public function getByIdAndAppointmentNo($id, $appointmentNo)
    {
        // Check if both $id and $appointmentNo are provided
        if (!empty($id) && !empty($appointmentNo)) {
            $condition = "id = :id AND appointment_no = :appointment_no";
            $param = array(':id' => $id, ':appointment_no' => $appointmentNo);
        } else {
            // Either $id or $appointmentNo is missing, return null or handle it accordingly
            return null;
        }

        // Build and execute the SQL query
        return $this->pm->run("SELECT * FROM " . $this->getTableName() . " WHERE $condition", $param, true);
    }

    protected function addNewRec()
    {
        $params = array(
            ':appointment_no' => $this->appointment_no,
            ':doctor_id' => $this->doctor_id,
            ':patient_name' => $this->patient_name,
            ':address' => $this->address,
            ':telephone' => $this->telephone,
            ':email' => $this->email,
            ':nic' => $this->nic,
            ':treatment_id' => $this->treatment_id,
            ':time_slot_from' => $this->time_slot_from,
            ':time_slot_to' => $this->time_slot_to,
            ':appointment_date' => $this->appointment_date
        );

        $result = $this->pm->insertAndGetLastRowId("INSERT INTO appointments(appointment_no,doctor_id, patient_name, address, telephone, email, nic, treatment_id, time_slot_from, time_slot_to, appointment_date)
            VALUES(:appointment_no, :doctor_id, :patient_name, :address, :telephone, :email, :nic, :treatment_id, :time_slot_from, :time_slot_to, :appointment_date)", $params);

        // Check the result and return success or failure accordingly
        return $result;
    }

    protected function updateRec()
    {
        $params = array(
            ':patient_name' => $this->patient_name,
            ':address' => $this->address,
            ':telephone' => $this->telephone,
            ':email' => $this->email,
            ':nic' => $this->nic,
            ':id' => $this->id
        );

        return $this->pm->run(
            "UPDATE appointments
            SET 
                patient_name = :patient_name, 
                address = :address, 
                telephone = :telephone, 
                email = :email, 
                nic = :nic
            WHERE id = :id",
            $params
        );
    }

    public function getAllWithDoctorAndTreatment()
    {
        return $this->pm->run("SELECT apt.*, tmt.name AS treatment_name, dr.name AS doctor_name FROM appointments AS apt INNER JOIN  doctors AS dr ON apt.doctor_id = dr.id INNER JOIN treatments AS tmt ON tmt.id = apt.treatment_id order by id desc");
    }

    public function getAllWithDoctorAndTreatmentByUserId($user_id)
    {
        $param = array(':user_id' => $user_id);
        return $this->pm->run(
            "SELECT apt.*, tmt.name AS treatment_name, dr.name AS doctor_name FROM appointments AS apt INNER JOIN  doctors AS dr ON apt.doctor_id = dr.id INNER JOIN treatments AS tmt ON tmt.id = apt.treatment_id where dr.user_id = :user_id order by id desc",
            $param
        );
    }
}
