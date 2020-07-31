<?php 
include('../php/conexion.php');
include('is_logged.php');

$IdCentral = $conn->real_escape_string($_POST['valorId']);
$Comunidad = $conn->real_escape_string($_POST['valorComunidad']);
$Ip = $conn->real_escape_string($_POST['valorIP']);
$Descripcion = $conn->real_escape_string($_POST['valorDescripcion']);

	$sql = "UPDATE centrales_pings SET comunidad = '$Comunidad', ip = '$Ip', descripcion = '$Descripcion' WHERE id = '$IdCentral'";
	if(mysqli_query($conn, $sql)){
		echo '<script >M.toast({html:"La central se actualizo satisfactoriamente.", classes: "rounded"})</script>';
		?>
		<script>
			var a = document.createElement("a");
			a.href = "../views/centrales_pings.php";
			a.click();   
		</script>
		<?php	
	}else{
		echo '<script >M.toast({html:"Ha ocurrido un error.", classes: "rounded"})</script>';	
	}
mysqli_close($conn);
?>
