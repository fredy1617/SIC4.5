<?php
#INCLUIMOS EL ARCHIVO CON LA CONEXION A LA BASE DE DATOS
include('../php/conexion.php');
include_once("../php/password_compatibility_library.php");

$usu_usuario=$_POST['usuario'];//$usu_usuario='Juanito';
$usu_password=$_POST['password'];//$usu_password='12345678';

$sql = "SELECT user_id, user_name, user_email, user_password_hash
        FROM users
        WHERE user_name = '" . $usu_usuario . "' OR user_email = '" . $usu_usuario . "';";
$result_of_login_check = $this->conn->query($sql);
if ($result_of_login_check->num_rows == 1) {
    // get result row (as an object)
    $result_row = $result_of_login_check->fetch_object();

    // using PHP 5.5's password_verify() function to check if the provided password fits
    // the hash of that user's password
    if (password_verify($usu_password, $result_row->user_password_hash)) {
      // write user data into PHP SESSION (a file on your server)
      
    } else {
      echo "Usuario y/o contraseña no coinciden.";
    }
} else {
      echo "Usuario y/o contraseña no coinciden.";
}
#$valorUserPassword_hash = password_hash($usu_password, PASSWORD_DEFAULT);
$sentencia->close();
$conn->close();
?>