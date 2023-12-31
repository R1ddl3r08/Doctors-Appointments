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
    
        return ($dayOfWeek == 6 || $dayOfWeek == 0);
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
        } elseif (!($this->isAvailable($date, $startTime, $endTime, $doctor))) {
            $errors['endTime'] = "This appointment is not available";
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

    public function getAllStatus()
    {
        $sql = "SELECT * FROM appointment_status";

        $stmt = $this->connection->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function updateAppointment($appointmentId, $patientName, $patientTel, $doctor, $service, $date, $startTime, $endTime, $message, $status)
    {
        $sql = "UPDATE appointments 
                SET patient_name = :patientName, 
                    patient_tel = :patientTel, 
                    doctor_id = :doctor, 
                    doctor_service_id = :service, 
                    date = :date, 
                    start_time = :startTime,
                    end_time = :endTime,
                    message = :message,
                    appointment_status_id = :status
                WHERE id = :appointmentId";

        $data = [
            'patientName' => $patientName,
            'patientTel' => $patientTel,
            'doctor' => $doctor,
            'service' => $service,
            'date' => $date,
            'startTime' => $startTime,
            'endTime' => $endTime,
            'message' => $message,
            'status' => $status,
            'appointmentId' => $appointmentId
        ];

        $stmt = $this->connection->prepare($sql);
        $stmt->execute($data);
    }

    public function deleteAppointment($id)
    {
        $sql = "DELETE FROM appointments WHERE id=:id";

        $stmt = $this->connection->prepare($sql);
        $stmt->bindParam(':id', $id, \PDO::PARAM_INT);
        
        return $stmt->execute();
    }

    public function isAvailable($date, $startTime, $endTime, $doctor)
    {
        if (empty($date) || empty($startTime) || empty($endTime)) {
            return false;
        }

        if ($startTime === false || $endTime === false) {
            return false;
        }

        $sql = "SELECT * FROM appointments WHERE date = :date AND doctor_id = :doctor";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindParam(':date', $date, \PDO::PARAM_STR);
        $stmt->bindParam(':doctor', $doctor, \PDO::PARAM_INT);
        $stmt->execute();

        $existingAppointments = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        foreach ($existingAppointments as $appointment) {
            $existingStartTime = strtotime($appointment['start_time']);
            $existingEndTime = strtotime($appointment['end_time']);

            if ($startTime < $existingEndTime && $endTime > $existingStartTime) {
                return false;
            }
        }
        return true;
    }


}

    


?>