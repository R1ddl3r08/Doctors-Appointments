<?php

    require_once('autoload.php');

    if($_SERVER['REQUEST_METHOD'] == 'POST'){

        $appointmentId = intval($_POST['appointmentId']);
        $patientName = $_POST['patientName'];
        $patientTel = $_POST['patientTel'];
        $doctor = $_POST['doctor'];
        $service = $_POST['service'];
        $date = $_POST['date'];
        $startTime = $_POST['startTime'];
        $endTime = $_POST['endTime'];
        $message = $_POST['message'];
        $appointmentStatus = $_POST['appointmentStatus'];

        $startTimeStr = strtotime($startTime);
        $endTimeStr = strtotime($endTime);

        $appointment = new Database\Appointment();

        if(!(empty($appointment->validateAppointment($patientName, $patientTel, $doctor, $service, $date, $startTimeStr, $endTimeStr, $message)))){
            $response['success'] = false;
            $response['errors'] = $appointment->validateAppointment($patientName, $patientTel, $doctor, $service, $date, $startTimeStr, $endTimeStr, $message);
        } else {
            $appointment->updateAppointment($appointmentId, $patientName, $patientTel, $doctor, $service, $date, $startTime, $endTime, $message, $appointmentStatus);
            $response['success'] = true;
        }

        header("Content-Type: application/json");
        echo json_encode($response);

    }

?>