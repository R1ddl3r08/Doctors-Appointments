<?php

require_once('autoload.php');

if($_SERVER['REQUEST_METHOD'] == 'GET'){
    $service = new Database\Service();

    $allServices = $service->getAllServices();

    $response = ['allServices' => $allServices];

    header("Content-Type: application/json");
    echo json_encode($response);
}

?>