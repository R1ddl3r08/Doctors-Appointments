<?php

require_once('autoload.php');

if($_SERVER['REQUEST_METHOD'] == 'GET'){
    if(isset($_GET['id'])){
        $id = $_GET['id'];

        $appointmentObj = new Database\Appointment();

        $appointment = $appointmentObj->getAppointment($id);

        if(!empty($appointment)){
            $exists = true;
        } else {
            $exists = false;
        }
    
        $response = ['exists' => $exists, 'appointment' => $appointment];
    
        header("Content-Type: application/json");
        echo json_encode($response);
    }

}


?>