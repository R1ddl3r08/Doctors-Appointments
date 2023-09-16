<?php

require_once('autoload.php');

if($_SERVER['REQUEST_METHOD'] == 'GET'){
    $doctor = new Database\Doctor();

    $allDoctors = $doctor->getAllDoctors();

    $response = ['allDoctors' => $allDoctors];

    header("Content-Type: application/json");
    echo json_encode($response);
}

?>