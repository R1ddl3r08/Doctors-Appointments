<?php

require_once('autoload.php');

if($_SERVER['REQUEST_METHOD'] == 'GET'){
    $appointment = new Database\Appointment();

    $allStatus = $appointment->getAllStatus();

    $response = ['allStatus' => $allStatus];

    header("Content-Type: application/json");
    echo json_encode($response);
}

?>