<?php 
#INCLUIMOS EL ARCHIVO CON LA CONEXION A LA BASE DE DATOS
include('../php/conexion.php');
session_start();
date_default_timezone_set('America/Mexico_City');
$Descripcion = $conn->real_escape_string($_POST['valorDescripcion']);
$Folio = $conn->real_escape_string($_POST['valorFolio']);

$id_user = $_SESSION['user_id'];
$Fecha_hoy = date('Y-m-d');

//o $consultaBusqueda sea igual a nombre + (espacio) + apellido
$sql = "INSERT INTO detalles_pedidos (folio, descripcion, fecha, usuario) VALUES('$Folio', '$Descripcion', '$Fecha_hoy', '$id_user')";
if(mysqli_query($conn, $sql)){
	echo '<script>M.toast({html :"el material se registró satisfactoriamente.", classes: "rounded"})</script>';
    
}else{
	echo '<script>M.toast({html :"Ha ocurrido un error.", classes: "rounded"})</script>';
    	
}
?>
<script>
    folio = <?php echo $Folio ?>;
    setTimeout("location.href='detalles_pedido.php?folio='+folio", 800);
</script>
<?php

mysqli_close($conn);
?>