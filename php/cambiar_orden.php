<?php 
session_start();
include('../php/conexion.php');

$Id = $conn->real_escape_string($_POST['valorIdOrden']);
$Estatus = $conn->real_escape_string($_POST['valorEstatus']);

$sql = "UPDATE orden_servicios SET estatus = '$Estatus' WHERE id = $Id";
if(mysqli_query($conn, $sql)){
	echo '<script>M.toast({html:"Orden actualizada correctamente .", classes: "rounded"})</script>';
	
}else{
	echo '<script>M.toast({html:"Ocurrio un error vuelva a intentar...", classes: "rounded"})</script>';	
}
//echo mysqli_error($conn);
mysqli_close($conn);
?>
<script>
	var a = document.createElement("a");
		a.href = "../views/ordenes_pendientes.php";
		a.click();
</script>
