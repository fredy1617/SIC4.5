<?php 
include('../php/conexion.php');
$IdComunidad = $conn->real_escape_string($_POST['valorIdComunidad']);
$Nombre = $conn->real_escape_string($_POST['valorNombre']);
$Instalacion = $conn->real_escape_string($_POST['valorInstalacion']);
$Servidor = $conn->real_escape_string($_POST['valorServidor']);
$Municipio = $conn->real_escape_string($_POST['valorMunicipio']);

//o $consultaBusqueda sea igual a nombre + (espacio) + apellido
$sql = "UPDATE comunidades SET nombre='$Nombre', municipio='$Municipio', instalacion='$Instalacion', servidor='$Servidor' WHERE id_comunidad='$IdComunidad'";
if(mysqli_query($conn, $sql)){
	echo '<script>M.toast({html:"La comunidad se actualizó correctamente.", classes: "rounded"})</script>';
	?>
	<script>
	    setTimeout("location.href='../views/comunidades.php'", 800);
	</script>
	<?php
}else{
	echo '<script>M.toast({html:"Ha ocurrido un error.", classes: "rounded"})</script>';	
}

mysqli_close($conn);
?>