<?php 
include('../php/conexion.php');
date_default_timezone_set('America/Mexico_City');
$Id_pago = $conn->real_escape_string($_POST['valorIdPago']);
$Cotejado = $conn->real_escape_string($_POST['valorAtendido']);
$Fecha_corte = $conn->real_escape_string($_POST['valorCorte']);

//Variable vacía (para evitar los E_NOTICE)
$mensaje = "";

	$sql = "UPDATE pagos SET Cotejado = '$Cotejado', Corte_tel = '$Fecha_corte' WHERE id_pago = $Id_pago";
	if(mysqli_query($conn, $sql)){
		$mensaje = '<script>M.toast({html:"Pago actualizado correctamente.", classes: "rounded"})</script>';
		echo '<script>function recargar3() {
						    setTimeout("location.href="../views/tel.php"", 1000);
						  }</script>';
					echo '<script>recargar3()</script>';
	}else{
		$mensaje = '<script>M.toast({html:"Por favor llenen todos los campos.", classes: "rounded"})</script>';	
	}

echo $mensaje;
//echo mysqli_error($conn);
mysqli_close($conn);
?>