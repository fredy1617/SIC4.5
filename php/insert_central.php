<?php 
include('../php/conexion.php');
include('is_logged.php');

$id_user = $_SESSION['user_id'];
$Nombres = $conn->real_escape_string($_POST['valorNombres']);
$Telefono = $conn->real_escape_string($_POST['valorTelefono']);
$Comunidad = $conn->real_escape_string($_POST['valorComunidad']);
$Direccion = $conn->real_escape_string($_POST['valorDireccion']);
$Coordenada = $conn->real_escape_string($_POST['valorCoordenada']);
$Descripcion = $conn->real_escape_string($_POST['valorDescripcion']);

if(mysqli_num_rows(mysqli_query($conn, "SELECT * FROM centrales WHERE nombre='$Nombres' AND telefono='$Telefono' AND comunidad='$Comunidad' AND direccion='$Direccion' AND coordenadas='$Coordenada'"))>0){
	echo '<script >M.toast({html:"Ya se encuentra una central con los mismos datos registrados.", classes: "rounded"})</script>';
}else{
	$sql = "INSERT INTO centrales (nombre, telefono, comunidad, direccion, coordenadas, descripcion_gral) VALUES('$Nombres', '$Telefono', '$Comunidad', '$Direccion', '$Coordenada', '$Descripcion')";
	if(mysqli_query($conn, $sql)){
		echo '<script >M.toast({html:"La central se dió de alta satisfactoriamente.", classes: "rounded"})</script>';	
		?>
		<script>
			var a = document.createElement("a");
			a.href = "../views/centrales.php";
			a.click();   
		</script>
		<?php
	}else{
		echo '<script >M.toast({html:"Ha ocurrido un error.", classes: "rounded"})</script>';	
	}
}
mysqli_close($conn);
?>
