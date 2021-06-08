<?php
#INCLUIMOS EL ARCHIVO CON LA CONEXION A LA BASE DE DATOS era (consulta)
include('../php/conexion.php');

#BUSCAMOS TODAS LAS RUTAS QUE ESTEN PENDIENTES AUN
$query = $conn->query("SELECT * FROM rutas WHERE estatus = 0");

$datos = array();

#VACIAMOS CADA RUTA EN UNA ARRAY PARA MOSTRAR EN LA APP
while($resultado = $query->fetch_assoc()) {
    $datos[] = $resultado;
}

//echo json_encode($datos);
echo json_encode(array("Usuarios" => $datos));
?>