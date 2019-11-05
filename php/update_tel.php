<?php 
include('../php/conexion.php');
date_default_timezone_set('America/Mexico_City');
$Id_pago = $conn->real_escape_string($_POST['valorIdPago']);
$Cotejado = $conn->real_escape_string($_POST['valorAtendido']);

$sql = "UPDATE pagos SET Cotejado = '$Cotejado' WHERE id_pago = $Id_pago";
if(mysqli_query($conn, $sql)){
	echo '<script>M.toast({html:"Pago actualizado correctamente.", classes: "rounded"})</script>';
	echo '<script>recargar3()</script>';
}else{
	echo '<script>M.toast({html:"Por favor llenen todos los campos.", classes: "rounded"})</script>';	
}
//echo mysqli_error($conn);
mysqli_close($conn);
?>