<?php 
session_start();
include('../php/conexion.php');
date_default_timezone_set('America/Mexico_City');

$id_user = $_SESSION['user_id'];
$Hoy = date('Y-m-d'); 

$sql_repetido = "SELECT * FROM int_cortes WHERE fecha = '$Hoy' AND usuario = '$id_user'";
if(mysqli_num_rows(mysqli_query($conn, $sql_repetido))>0){
    echo '<script>M.toast({html :"Ya se encuentra un corte con lo mismos datos.", classes: "rounded"})</script>';
}else{
	$sql = "INSERT INTO int_cortes (fecha, usuario) VALUES('$Hoy', '$id_user')";
	if(mysqli_query($conn, $sql)){
		echo '<script>M.toast({html :"El corte se creó satisfactoriamente.", classes: "rounded"})</script>';
	}else{
		echo '<script>M.toast({html :"Ha ocurrido un error.", classes: "rounded"})</script>';	
	}
}
?>