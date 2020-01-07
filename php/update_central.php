<?php 
include('../php/conexion.php');
include('is_logged.php');

$IdCentral = $conn->real_escape_string($_POST['valorIdCentral']);
$Nombres = $conn->real_escape_string($_POST['valorNombres']);
$Telefono = $conn->real_escape_string($_POST['valorTelefono']);
$Comunidad = $conn->real_escape_string($_POST['valorComunidad']);
$Direccion = $conn->real_escape_string($_POST['valorDireccion']);
$Coordenada = $conn->real_escape_string($_POST['valorCoordenada']);

if(mysqli_num_rows(mysqli_query($conn, "SELECT * FROM centrales WHERE nombre='$Nombres' AND telefono='$Telefono' AND comunidad='$Comunidad' AND direccion='$Direccion' AND coordenadas='$Coordenada'"))>0){
	echo '<script >M.toast({html:"Ya se encuentra una central con los mismos datos registrados.", classes: "rounded"})</script>';
}else{
	$sql = "UPDATE centrales SET nombre = '$Nombres', telefono = '$Telefono', comunidad = '$Comunidad', direccion = '$Direccion', coordenadas = '$Coordenada' WHERE id = '$IdCentral'";
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
}
mysqli_close($conn);
?>
