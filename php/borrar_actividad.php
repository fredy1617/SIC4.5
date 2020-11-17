<?php
include('../php/is_logged.php');
include('../php/conexion.php');
include('../php/superAdmin.php');

$Id = $conn->real_escape_string($_POST['valorId']);

if(mysqli_query($conn, "DELETE FROM actividades_calendario WHERE id = $Id")){
    echo '<script >M.toast({html:"Actividad Borrada..", classes: "rounded"})</script>';
}else{
    echo '<script >M.toast({html:"Ocurrio un error...", classes: "rounded"})</script>';
}
include ('../php/tabla_A.php');
?>