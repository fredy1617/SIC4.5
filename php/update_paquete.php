<?php 
session_start();
include('../php/conexion.php');
include('../php/admin.php');
date_default_timezone_set('America/Mexico_City');
$IdPaquete = $conn->real_escape_string($_POST['valorIdPaquete']);
$Subida = $conn->real_escape_string($_POST['valorSubida']);
$Bajada = $conn->real_escape_string($_POST['valorBajada']);
$Mensualidad = $conn->real_escape_string($_POST['valorMensualidad']);

//Variable vacía (para evitar los E_NOTICE)
$mensaje = "";
$id = $_SESSION['user_id'];
$area = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM users WHERE user_id=$id"));

if($area['area']!="Administrador"){
  echo "<script >M.toast({html: 'Sólo un administrador puede editar un paquete.', classes: 'rounded'});/script>";
}else{

	$sql = "UPDATE paquetes SET subida='$Subida', bajada='$Bajada', mensualidad='$Mensualidad' WHERE id_paquete='$IdPaquete'";
	if(mysqli_query($conn, $sql)){
		$mensaje = '<script>M.toast({html:"El paquete se actualizó correctamente.", classes: "rounded"})</script>';
		?>
		<script>
		  var a = document.createElement("a");
			a.href = "../views/paquetes.php";
			a.click();   
		</script>
		<?php
	}else{
		$mensaje = '<script>M.toast({html:"Ha ocurrido un error.", classes: "rounded"})</script>';	
	}
}

echo $mensaje;
mysqli_close($conn);
?>