<?php 
#INCLUIMOS EL ARCHIVO CON LA CONEXION A LA BASE DE DATOS
include('../php/conexion.php');
session_start();
date_default_timezone_set('America/Mexico_City');
$Descripcion = $conn->real_escape_string($_POST['valorDescripcion']);
$Proveedor = $conn->real_escape_string($_POST['valorProveedor']);
$Folio = $conn->real_escape_string($_POST['valorFolio']);
$Ruta = $conn->real_escape_string($_POST['valorRuta']);

$id_user = $_SESSION['user_id'];
$Fecha_hoy = date('Y-m-d');

//o $consultaBusqueda sea igual a nombre + (espacio) + apellido
$sql = "INSERT INTO detalles_pedidos (folio, descripcion, Proveedor, fecha, usuario) VALUES('$Folio', '$Descripcion', '$Proveedor', '$Fecha_hoy', '$id_user')";
if(mysqli_query($conn, $sql)){
	echo '<script>M.toast({html :"el material se registró satisfactoriamente.", classes: "rounded"})</script>';
    
}else{
	echo '<script>M.toast({html :"Ha ocurrido un error.", classes: "rounded"})</script>';   	
}
?>
<script>
    folio = <?php echo $Folio ?>;
  	var ruta = $("input#ruta").val();
    setTimeout("location.href=ruta+'?folio='+folio", 500);
</script>
<input id="ruta" type="hidden" value="<?php echo $Ruta; ?>">
<?php

mysqli_close($conn);
?>