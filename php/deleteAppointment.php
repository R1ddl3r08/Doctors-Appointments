<?php

    require_once('autoload.php');

    if($_SERVER['REQUEST_METHOD'] == 'POST'){
        $appointmentId = $_POST['appointmentId'];

        if($appointmentId){
            $appointment = new Database\Appointment();

            $appointment->deleteAppointment($appointmentId);
            $response['success'] = true;
        } else {
            $response['success'] = false;
        }

        header("Content-Type: application/json");
        echo json_encode($response);

    }

?>