<?php 
include('conexion.php');
$id = $_SESSION['user_id'];
$area = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM users WHERE user_id=$id"));

if($area['area']=="Cobrador"){
	echo '<script>M.toast({html:"Permiso denegado. Direccionando a la página principal.", classes: "rounded"})</script>';
  	echo '<script>admin();</script>';
	mysqli_close($conn);
	exit;
}
?>