<?php
#INCLUIMOS EL ARCHIVO CON LA CONEXION A LA BASE DE DATOS era (Buscar)
include('../php/conexion.php');

$codigo=$_GET['codigo'];// RECIBIMOS EL ID DE LA RUTA POR GET

#CONSULTAMOS TODAS LAS INSTALACIONES QUE ALLA DE ESTA RUTA
$consulta="SELECT * FROM tmp_reportes WHERE ruta ='$codigo'";
$resultado = $conexion->query($consulta);

#RECORREMOS CADA INTSLACION CON UN CICLO Y LO VACIAMOS EN UN ARRAY 
while($fila=$resultado -> fetch_array()){
    $producto[] = array_map('utf8_encode', $fila);
}

echo json_encode($producto);
$resultado -> close();
?>
