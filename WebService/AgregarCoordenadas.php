<?php
#INCLUIMOS EL ARCHIVO CON LA CONEXION A LA BASE DE DATOS era (AgregarCoo)
include('../php/conexion.php');
$id_cliente=$_POST['id_cliente'];
$coordenadas=$_POST['coordenadas'];

//ACTUALIZAMOS LA COORDENADA DE LA TABLA DE CLIENTES
mysqli_query($conn, "UPDATE clientes SET coordenadas='$coordenadas' WHERE id_cliente='$id_cliente'") or die (mysqli_error());
mysqli_close($conn);
?>
