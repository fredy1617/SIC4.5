<?php 
include('../php/conexion.php');
$IdComunidad = $conn->real_escape_string($_POST['valorIdComunidad']);
$Nombre = $conn->real_escape_string($_POST['valorNombre']);
$Instalacion = $conn->real_escape_string($_POST['valorInstalacion']);
$Servidor = $conn->real_escape_string($_POST['valorServidor']);

//Variable vacía (para evitar los E_NOTICE)
$mensaje = "";

//o $consultaBusqueda sea igual a nombre + (espacio) + apellido
$sql = "UPDATE comunidades SET nombre='$Nombre', instalacion='$Instalacion', servidor='$Servidor' WHERE id_comunidad='$IdComunidad'";
if(mysqli_query($conn, $sql)){
	$mensaje = '<script>M.toast({html:"La comunidad se actualizó correctamente.", classes: "rounded"})</script>';
	?>
	<script>
	  var a = document.createElement("a");
		a.href = "../views/comunidades.php";
		a.click();   
	</script>
	<?php
}else{
	$mensaje = '<script>M.toast({html:"Ha ocurrido un error.", classes: "rounded"})</script>';	
}

echo $mensaje;
mysqli_close($conn);
?>