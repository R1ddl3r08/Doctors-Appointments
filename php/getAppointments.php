<?php

require_once('autoload.php');

if($_SERVER['REQUEST_METHOD'] == 'GET'){
    $appointment = new Database\Appointment();

    $allAppointments = $appointment->getAllAppointments();

    $response = ['allAppointments' => $allAppointments];

    header("Content-Type: application/json");
    echo json_encode($response);
}

?>