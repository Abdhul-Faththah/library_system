<?php
require_once 'BaseModel.php';

class DoctorAvailability extends BaseModel
{
    public $day;
    public $session_from;
    public $session_to;
    public $doctor_id;
    public $is_active;

    protected function getTableName()
    {
        return "doctor_availability";
    }

    public function getAll()
    {
        $tableName = $this->getTableName();
        $doctorsTableName = "doctors";
        $doctorIdField = "doctor_id";

        // Construct the SQL query with the join:
        $sql = "SELECT $tableName.*, doctors.name as doctor_name FROM $tableName JOIN $doctorsTableName ON $tableName.$doctorIdField = $doctorsTableName.id";

        return $this->pm->run($sql);
    }


    public function getAllActiveByDoctorId($doctor_id)
    {
        $param = array(':doctor_id' => $doctor_id);
        return $this->pm->run("SELECT * FROM " . $this->getTableName() . " WHERE is_active = 1 AND doctor_id = :doctor_id", $param);
    }

    protected function addNewRec()
    {
        $param = array(
            ':day' => $this->day,
            ':session_from' => $this->session_from,
            ':session_to' => $this->session_to,
            ':doctor_id' => $this->doctor_id,
            ':is_active' => $this->is_active
        );

        // Update the query to set the correct columns
        return $this->pm->run("INSERT INTO doctor_availability(day, session_from, session_to, doctor_id, is_active) VALUES (:day, :session_from, :session_to, :doctor_id, :is_active)", $param);
    }

    protected function updateRec()
    {
        $param = array(
            ':id' => $this->id,
            ':day' => $this->day,
            ':session_from' => $this->session_from,
            ':session_to' => $this->session_to,
            ':doctor_id' => $this->doctor_id,
            ':is_active' => $this->is_active
        );

        // Update the query to set the correct columns
        return $this->pm->run("UPDATE doctor_availability SET day = :day, session_from = :session_from, session_to = :session_to, doctor_id = :doctor_id, is_active = :is_active WHERE id = :id", $param);
    }
}
