<?php
    
    
    $json = file_get_contents('php://input');

	$data = json_decode($json);
	
    $datos = $data->enviar;

    $latitude = $datos->latitude;
    $longitude = $datos->longitude;
    $id_cliente = $datos->id_cliente;
    $Coordenadas = $latitude.', '.$longitude;

include 'php/conexion.php';
$sql = "UPDATE clientes SET  coordenadas = '$Coordenadas' WHERE id_cliente = $id_cliente";
$insertar="INSERT INTO coordenadas(latitude, longitude, id_cliente) VALUES('$latitude','$longitude','$id_cliente')";
if(mysqli_query($conn, $sql))
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