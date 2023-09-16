<?php

    require_once('autoload.php');

    if($_SERVER['REQUEST_METHOD'] == 'POST'){

        $patientName = $_POST['patientName'];
        $patientTel = $_POST['patientTel'];
        $doctor = $_POST['doctor'];
        $service = $_POST['service'];
        $date = $_POST['date'];
        $startTime = strtotime($_POST['startTime']);
        $endTime = strtotime($_POST['endTime']);
        $message = $_POST['message'];

        $appointment = new Database\Appointment();

        if(!(empty($appointment->validateAppointment($patientName, $patientTel, $doctor, $service, $date, $startTime, $endTime, $message)))){
            $response['success'] = false;
            $response['errors'] = $appointment->validateAppointment($patientName, $patientTel, $doctor, $service, $date, $startTime, $endTime, $message);
        } else {
            $appointment->setAppointment($patientName, $patientTel, $doctor, $service, $date, $startTime, $endTime, $message);
            $response['success'] = true;
        }

        header("Content-Type: application/json");
        echo json_encode($response);

    }

?>