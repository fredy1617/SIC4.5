<?php 
include('../php/conexion.php');
$IdCliente = $conn->real_escape_string($_POST['valorIdCliente']);
$Fecha = $conn->real_escape_string($_POST['valorFecha']);
$Paquete = $conn->real_escape_string($_POST['valorPaquete']);

//o $consultaBusqueda sea igual a nombre + (espacio) + apellido
$sql = "UPDATE clientes SET fecha_corte ='$Fecha', paquete = '$Paquete' WHERE id_cliente='$IdCliente'";
if(mysqli_query($conn, $sql)){
	$mensaje = '<script>M.toast({html:"Se actualizó correctamente.", classes: "rounded"})</script>';
	?>
	<script>
	  var a = document.createElement("a");
		a.href = "../views/revision_full.php";
		a.click();   
	</script>
	<?php
}else{
	$mensaje = '<script>M.toast({html:"Ha ocurrido un error.", classes: "rounded"})</script>';	
}

echo $mensaje;
mysqli_close($conn);
?>