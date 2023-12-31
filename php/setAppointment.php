<?php

    require_once('autoload.php');

    if($_SERVER['REQUEST_METHOD'] == 'POST'){

        $patientName = $_POST['patientName'];
        $patientTel = $_POST['patientTel'];
        $doctor = $_POST['doctor'];
        $service = $_POST['service'];
        $date = $_POST['date'];
        $startTime = $_POST['startTime'];
        $endTime = $_POST['endTime'];
        $message = $_POST['message'];

        $startTimeStr = strtotime($startTime);
        $endTimeStr = strtotime($endTime);

        $appointment = new Database\Appointment();

        if(!(empty($appointment->validateAppointment($patientName, $patientTel, $doctor, $service, $date, $startTimeStr, $endTimeStr, $message)))){
            $response['success'] = false;
            $response['errors'] = $appointment->validateAppointment($patientName, $patientTel, $doctor, $service, $date, $startTimeStr, $endTimeStr, $message);
        } else {
            $appointment->setAppointment($patientName, $patientTel, $doctor, $service, $date, $startTime, $endTime, $message);
            $response['success'] = true;
        }

        header("Content-Type: application/json");
        echo json_encode($response);

    }

?>