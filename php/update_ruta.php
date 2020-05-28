<?php 
session_start();
include('../php/conexion.php');
include('../php/admin.php');
date_default_timezone_set('America/Mexico_City');
$IdRuta = $conn->real_escape_string($_POST['valorIdRuta']);
$Material = $conn->real_escape_string($_POST['valorMaterial']);

$sql = "UPDATE rutas SET material='$Material' WHERE id_ruta='$IdRuta'";
if(mysqli_query($conn, $sql)){
	echo '<script>M.toast({html:"La ruta se actualizó correctamente.", classes: "rounded"})</script>';
}else{
	echo '<script>M.toast({html:"Ha ocurrido un error.", classes: "rounded"})</script>';	
}

$sql = mysqli_query($conn,"SELECT * FROM rutas WHERE id_ruta=$IdRuta");
$datos = mysqli_fetch_array($sql);
?>
<div id="update"><b>Material: </b><?php echo $datos['material']; ?><br></div>