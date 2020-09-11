<?php 
#INCLUIMOS EL ARCHIVO CON LA CONEXION A LA BASE DE DATOS
include('conexion.php');
#TOMAMOS EL ID DEL USUARIO CON LA SESSION INICIADA
$id = $_SESSION['user_id'];
#TOMAMOS LA INFORMACION DEL USUARIO (PARA SABER A QUE AREA PERTENECE)
$area = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM users WHERE user_id=$id"));
#COMPARAMOS SI SU AREA ES DE UN ADMINISTRADOR O SI ES IGUAL AL AREA DE REDES
if($area['area'] == "Administrador" OR $area['area'] == "Redes"){
	#SI PERTENECEN LES DA LA BIENVENIDA Y LOS DEJA ENTRAR A LA PAGINA
	echo '<script>M.toast({html:"Bienvenido !", classes: "rounded"})</script>';
}else{
	#SI NO ES NI ADMINISTRADOR NI ES DE REDES LE MUESTRA MENSAJE DE NEGACION Y REDIRECCIONA A LA PAGINA PRINCIPAL
	echo '<script>M.toast({html:"Permiso denegado. Direccionando a la página principal.", classes: "rounded"})</script>';
	#LLAMAR LA FUNCION admin() DEFINIDA EN EL ARCHIVO MODALS PARA REDIRECCIONAR
  	echo '<script>admin();</script>';
  	#CERRAR LA CONEXION A LA BASE DE DATOS
	mysqli_close($conn);
	exit;
}
?>