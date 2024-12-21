<?php

require_once 'BaseModel.php';

class Treatment extends BaseModel
{
    public $name;
    public $description;
    public $registration_fee;
    public $treatment_fee;
    public $is_active;

    protected function getTableName()
    {
        return "treatments";
    }

    protected function addNewRec()
    {
        $param = array(
            ':name' => $this->name,
            ':description' => $this->description,
            ':treatment_fee' => $this->treatment_fee,
            ':registration_fee' => $this->registration_fee,
            ':is_active' => $this->is_active
        );
        return $this->pm->run(
            "INSERT INTO 
            treatments(name, description,treatment_fee, registration_fee, is_active) 
            values(:name, :description, :treatment_fee, :registration_fee, :is_active)",
            $param
        );
    }

    protected function updateRec()
    {
        $param = array(
            ':name' => $this->name,
            ':description' => $this->description,
            ':treatment_fee' => $this->treatment_fee,
            ':registration_fee' => $this->registration_fee,
            ':is_active' => $this->is_active,
            ':id' => $this->id
        );

        return $this->pm->run(
            "UPDATE 
            treatments 
            SET 
                name = :name, 
                description = :description,
                treatment_fee = :treatment_fee,
                registration_fee = :registration_fee,
                is_active = :is_active 
            WHERE id = :id",
            $param
        );
    }

    function deleteTreatment($id)
    {
        $user = new Treatment();
        $user->deleteRec($id);

        if ($user) {
            return true; // User udapted successfully
        } else {
            return false; // User update failed (likely due to database error)
        }
    }
}
