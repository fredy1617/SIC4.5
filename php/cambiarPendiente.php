<?php 
include('../php/conexion.php');
include('is_logged.php');
date_default_timezone_set('America/Mexico_City');
$Hoy = date('Y-m-d');

$IdDispositivo = $conn->real_escape_string($_POST['valorId']);
$Nota = $conn->real_escape_string($_POST['valorNota']);
$falla = mysqli_fetch_array(mysqli_query($conn, "SELECT falla FROM dispositivos WHERE id_dispositivo = '$IdDispositivo'"));
$NFalla = $Nota.' ('.$falla['falla'].')';

if (mysqli_query ($conn, "UPDATE dispositivos SET  estatus='Pendiente', falla = '$NFalla', fecha = '$Hoy' WHERE id_dispositivo ='$IdDispositivo'")){
	echo '<script >M.toast({html:"El folio se envio a Pendientes: '.$IdDispositivo.'.", classes: "rounded"})</script>';
	?>
	<script>
	  var a = document.createElement("a");
		a.href = "../views/pendientes.php";
		a.click();   
	</script>
	<?php
}else{
	echo '<script >M.toast({html:"Ocurrio un error.", classes: "rounded"})</script>';
}