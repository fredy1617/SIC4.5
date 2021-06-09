<?php
#INCLUIMOS EL ARCHIVO CON LA CONEXION A LA BASE DE DATOS
include('../php/conexion.php');

$sql = "SELECT * FROM clientes";
$query = $conn->query($sql);

$datos = array();

while($resultado = $query->fetch_assoc()) {
    $datos[] = $resultado;
}

//echo json_encode($datos);
echo json_encode(array("Usuarios" => $datos));
?>