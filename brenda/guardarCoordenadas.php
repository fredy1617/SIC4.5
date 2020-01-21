<?php
    
    
    $json = file_get_contents('php://input');

	$data = json_decode($json);
	
    $datos = $data->enviar;

    $latitude = $datos->latitude;
    $longitude = $datos->longitude;
    $last_update_time = $datos->last_update_time;


include '../php/conexion.php';
$insertar="INSERT INTO coordenadas(latitude, longitude, last_update_time) VALUES('$latitude','$longitude','$last_update_time')";
if(mysqli_query($conn, $insertar))
{
   $json = array();
   $json ["answer"]= array("code"=>"200", "message"=>"successful");            
   echo json_encode($json); 
}else
{
   $json = array();
   $json ["answer"]= array("code"=>"100", "message"=>"error");            
   echo json_encode($json);    
}


//consulta sql

mysqli_close($conn);
?>