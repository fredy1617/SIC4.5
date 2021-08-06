<?php 
include('conexion.php');
$id = $_SESSION['user_id'];
$area = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM users WHERE user_id=$id"));

if($area['area']=="Administrador" AND ($area['user_id'] == 10 OR $area['user_id']==49 OR $area['user_id']==70 OR $area['user_id']==25  OR $area['user_id']==28 OR $area['user_id']==75 OR $area['user_id']==83)){
}else{
	echo '<script>M.toast({html:"Permiso denegado. Direccionando a la página principal.", classes: "rounded"})</script>';
  	echo '<script>admin();</script>';
	mysqli_close($conn);
	exit;
}
?>