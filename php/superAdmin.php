<?php 
include('conexion.php');
$id = $_SESSION['user_id'];
$area = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM users WHERE user_id=$id"));

if($area['area']=="Administrador" and ($area['user_id'] == 10 or $area['user_id']==49 or $area['user_id']==56 or $area['user_id']==59)){
}else{
	echo '<script>M.toast({html:"Permiso denegado. Direccionando a la página principal.", classes: "rounded"})</script>';
  	echo '<script>admin();</script>';
	mysqli_close($conn);
	exit;
}
?>