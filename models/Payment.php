<?php
require_once 'BaseModel.php';

class Payment extends BaseModel
{
    public $appointment_id;
    public $registration_fee;
    public $registration_fee_paid;
    public $treatment_fee;
    public $quantity;
    public $treatment_fee_paid;

    protected function getTableName()
    {
        return "payments";
    }

    protected function addNewRec()
    {
        $params = array(
            ':appointment_id' => $this->appointment_id,
            ':registration_fee' => $this->registration_fee,
            ':registration_fee_paid' => $this->registration_fee_paid,
            ':treatment_fee' => $this->treatment_fee,
            ':quantity' => $this->quantity,
            ':treatment_fee_paid' => $this->treatment_fee_paid
        );

        $result = $this->pm->run(
            "INSERT INTO 
                payments(
                    appointment_id, 
                    registration_fee, 
                    registration_fee_paid, 
                    treatment_fee,
                    quantity,
                    treatment_fee_paid
                )
            VALUES(
                :appointment_id, 
                :registration_fee, 
                :registration_fee_paid, 
                :treatment_fee, 
                :quantity, 
                :treatment_fee_paid
                )",
            $params
        );

        // Check the result and return success or failure accordingly
        return $result ? true : false;
    }

    protected function updateRec()
    {
        $params = array(
            ':quantity' => $this->quantity,
            ':treatment_fee_paid' => $this->treatment_fee_paid,
            ':id' => $this->id
        );

        $result = $this->pm->run(
            "UPDATE 
            payments 
            SET 
                quantity = :quantity, 
                treatment_fee_paid = :treatment_fee_paid
            WHERE id = :id",
            $params
        );

        // Check the result and return success or failure accordingly
        return $result ? true : false;
    }

    public function getAllWithTreatmentAndAppointment()
    {
        return $this->pm->run("SELECT pmt.*, tmt.name AS treatment_name,tmt.id AS treatment_id, apt.appointment_no AS appointment_no FROM payments AS pmt INNER JOIN appointments AS apt ON apt.id = pmt.appointment_id INNER JOIN treatments AS tmt ON tmt.id = apt.treatment_id");
    }
}
