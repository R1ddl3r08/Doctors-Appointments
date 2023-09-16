<?php

namespace Database;

require_once('Doctor.php');

class Appointment
{
    protected \PDO $connection;

    public function __construct()
    {
        $this->connection = Database::connect();
    }

    public function isWeekend($date) 
    {
        $timestamp = strtotime($date);
        $dayOfWeek = date('w', $timestamp);
    
        return ($dayOfWeek >= 6 && $dayOfWeek <= 7);
    }

    public function validateAppointment($patientName, $patientTel, $doctor, $service, $date, $startTime, $endTime, $message)
    {
        $doctorObj = new Doctor();

        $errors = [];

        if(empty($patientName)){
            $errors['patientName'] = "The patient name field is required";
        } elseif (!preg_match('/^[A-Za-z\s]+$/', $patientName)) {
            $errors['patientName'] = "The patient name must only contain letters";
        }

        if(empty($patientTel)){
            $errors['patientTel'] = "The patient tel field is required";
        } elseif (!preg_match('/^(\+)?\d+$/', $patientTel)) {
            $errors['patientTel'] = "Invalid telephone number";
        }

        if($doctor == 0){
            $errors['doctor'] = "The doctor option is required";
        } elseif(!($doctorObj->doctorExists($doctor))){
            $errors['doctor'] = "Doctor doesn't exist";
        } else {
            $doctorData = $doctorObj->getDoctor($doctor);
        }

        if($service == 0){
            $errors['service'] = "The service option is required";
        }

        if(empty($date)){
            $errors['date'] = "The date field is required";
        } elseif (strtotime($date) === false) {
            $errors['date'] = "The date is invalid";
        } elseif($this->isWeekend($date)){
            $errors["date"] = "Doctor isn't working that day";
        }

        if(empty($startTime)){
            $errors['startTime'] = "The start time field is required";
        }

        if(empty($endTime)){
            $errors['endTime'] = "The end time field is required";
        } elseif (
            $startTime < strtotime($doctorData['start_time']) || 
            $startTime > strtotime($doctorData['end_time']) || 
            $endTime < strtotime($doctorData['start_time']) || 
            $endTime > strtotime($doctorData['end_time'])){
            $errors['endTime'] = "Doctor doesn't work at that time";
        } elseif ($endTime < $startTime){
            $errors['endTime'] = "End time can't be before start time";
        }

        return $errors;
    }

    public function setAppointment($patientName, $patientTel, $doctor, $service, $date, $startTime, $endTime, $message)
    {
        $sql = "INSERT INTO appointments (patient_name, patient_tel, doctor_id, doctor_service_id, date, start_time, end_time, message) VALUES (:patientName, :patientTel, :doctor, :service, :date, :startTime, :endTime, :message)";

        $data = [
            'patientName' => $patientName,
            'patientTel' => $patientTel,
            'doctor' => $doctor,
            'service' => $service,
            'date' => $date,
            'startTime' => $startTime,
            'endTime' => $endTime,
            'message' => $message
        ];

        $stmt = $this->connection->prepare($sql);
        $stmt->execute($data);
    }

    public function getAllAppointments()
    {
        $sql = "SELECT a.*, d.first_name AS doctor_first_name, d.last_name AS doctor_last_name, ds.service AS service_name, aps.status AS appointment_status
                FROM appointments AS a
                LEFT JOIN doctors AS d ON a.doctor_id = d.id
                LEFT JOIN doctor_services AS ds ON a.doctor_service_id = ds.id
                LEFT JOIN appointment_status AS aps ON a.appointment_status_id = aps.id";

        $stmt = $this->connection->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getAppointment($id)
    {
        $sql = "SELECT a.*, d.first_name AS doctor_first_name, d.last_name AS doctor_last_name, ds.service AS service_name, aps.status AS appointment_status
        FROM appointments AS a
        LEFT JOIN doctors AS d ON a.doctor_id = d.id
        LEFT JOIN doctor_services AS ds ON a.doctor_service_id = ds.id
        LEFT JOIN appointment_status AS aps ON a.appointment_status_id = aps.id
        WHERE a.id = :id
        ";

        $stmt = $this->connection->prepare($sql);
        $stmt->bindParam(':id', $id, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

}

    


?>