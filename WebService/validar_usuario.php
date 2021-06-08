<?php
#INCLUIMOS EL ARCHIVO CON LA CONEXION A LA BASE DE DATOS
include('../php/conexion.php');
include_once("../php/password_compatibility_library.php");

$usu_usuario=$_POST['usuario'];//$usu_usuario='Juanito';
$usu_password=$_POST['password'];//$usu_password='12345678';0
$valorUserPassword_hash = password_hash($valorUserPassword, PASSWORD_DEFAULT);

$sentencia=$conn->prepare("SELECT * FROM users WHERE user_name=? AND user_password_hash=?");
$sentencia->bind_param('ss',$usu_usuario,$valorUserPassword_hash);
$sentencia->execute();

$resultado = $sentencia->get_result();
if ($fila = $resultado->fetch_assoc()) {
  echo json_encode($fila,JSON_UNESCAPED_UNICODE);
}
$sentencia->close();
$conn->close();
?>
