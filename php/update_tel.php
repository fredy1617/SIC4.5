<?php 
session_start();
include('../php/conexion.php');
date_default_timezone_set('America/Mexico_City');
$Fecha = date('Y-m-d');
$Hora = date('H:i:s');
$id_user = $_SESSION['user_id'];
	
$Id_pago = $conn->real_escape_string($_POST['valorIdPago']);
$Cotejado = $conn->real_escape_string($_POST['valorAtendido']);

$sql = "UPDATE pagos SET Cotejado = '$Cotejado' WHERE id_pago = $Id_pago";
if(mysqli_query($conn, $sql)){
	mysqli_query($conn,"INSERT INTO fecha_cotejo (id_pago, fecha, hora, usuario) VALUES('$Id_pago', '$Fecha', '$Hora','$id_user')");
	echo '<script>M.toast({html:"Pago actualizado correctamente.", classes: "rounded"})</script>';
	echo '<script>recargar3()</script>';
}else{
	echo '<script>M.toast({html:"Por favor llenen todos los campos.", classes: "rounded"})</script>';	
}
//echo mysqli_error($conn);
mysqli_close($conn);
?>