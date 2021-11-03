<?php 
include('../php/conexion.php');
include('is_logged.php');

$Nombre = $conn->real_escape_string($_POST['valorNombre']);
$Responsable = $conn->real_escape_string($_POST['valorResponsable']);
$Descripcion = $conn->real_escape_string($_POST['valorDescripcion']);

if(mysqli_num_rows(mysqli_query($conn, "SELECT * FROM unidades WHERE nombre='$Nombre' AND  responsable='$Responsable' AND descripcion='$Descripcion'"))>0){
	echo '<script >M.toast({html:"Ya se encuentra una unidad con los mismos datos registrados.", classes: "rounded"})</script>';
}else{
	$sql = "INSERT INTO unidades (nombre, responsable, descripcion) VALUES('$Nombre', '$Responsable', '$Descripcion')";
	if(mysqli_query($conn, $sql)){
		echo '<script >M.toast({html:"La unidad se dió de alta satisfactoriamente.", classes: "rounded"})</script>';	
		?>
		<script>
			var a = document.createElement("a");
			a.href = "../views/vehiculos.php";
			a.click();   
		</script>
		<?php
	}else{
		echo '<script >M.toast({html:"Ha ocurrido un error.", classes: "rounded"})</script>';	
	}
}
mysqli_close($conn);
?>
