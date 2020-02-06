<?php 
include('../php/conexion.php');
include('is_logged.php');

$IdCentral = $conn->real_escape_string($_POST['valorIdCentral']);
$Nombres = $conn->real_escape_string($_POST['valorNombres']);
$Telefono = $conn->real_escape_string($_POST['valorTelefono']);
$Comunidad = $conn->real_escape_string($_POST['valorComunidad']);
$Direccion = $conn->real_escape_string($_POST['valorDireccion']);
$Descripcion = $conn->real_escape_string($_POST['valorDescripcion']);
$Coordenada = $conn->real_escape_string($_POST['valorCoordenada']);



	$sql = "UPDATE centrales SET nombre = '$Nombres', telefono = '$Telefono', comunidad = '$Comunidad', direccion = '$Direccion', coordenadas = '$Coordenada', descripcion_gral = '$Descripcion' WHERE id = '$IdCentral'";
	if(mysqli_query($conn, $sql)){
		echo '<script >M.toast({html:"La central se actualizo satisfactoriamente.", classes: "rounded"})</script>';
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
mysqli_close($conn);
?>
