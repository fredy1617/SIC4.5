<?php
include('../php/conexion.php');
date_default_timezone_set('America/Mexico_City');

$Referencia = $conn->real_escape_string($_POST['valorReferencia']);
$Descripcion = $conn->real_escape_string($_POST['valorDescripcion']);
$Id_Reporte = $conn->real_escape_string($_POST['valorIdReporte']);

if (mysqli_query($conn,"UPDATE reportes SET descripcion = '$Descripcion' WHERE id_reporte = $Id_Reporte ")) {	
	$Cliente = mysqli_fetch_array(mysqli_query($conn,"SELECT id_cliente FROM reportes WHERE id_reporte = $Id_Reporte"));
	$IdCliente = $Cliente['id_cliente'];
	mysqli_query($conn,"UPDATE clientes SET referencia='$Referencia' WHERE id_cliente=$IdCliente ");

	echo '<script>M.toast({html:"El reporte fue actualizado correctamente.", classes: "rounded"})</script>';
	?>
	<script>    
	    var a = document.createElement("a");
	      a.href = "../views/reportes.php";
	      a.click();
	</script>
	<?php
}else{	
	echo '<script">M.toast({html:"Ha ocurrido un error.", classes: "rounded"})</script>';
}
 