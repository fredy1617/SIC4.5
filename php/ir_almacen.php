<?php 
include('../php/conexion.php');
include('is_logged.php');
date_default_timezone_set('America/Mexico_City');

$IdDispocitivo = $conn->real_escape_string($_POST['valorIdDispocitivo']);

if (mysqli_query ($conn, "UPDATE dispositivos SET  estatus='Almacen'WHERE id_dispositivo ='$IdDispocitivo'")){
	echo '<script >M.toast({html:"El folio se envio a Almacen: '.$IdDispocitivo.'.", classes: "rounded"})</script>';
	?>
	<script>
	  var a = document.createElement("a");
		a.href = "../views/listos.php";
		a.click();   
	</script>
	<?php
}
?>