<?php 
include('../php/conexion.php');
$IdMaterial = $conn->real_escape_string($_POST['valorID']);
$folio = $conn->real_escape_string($_POST['valorFolio']);
$Listo = $conn->real_escape_string($_POST['valorListo']);

$sql = "UPDATE detalles_pedidos SET listo = $Listo WHERE id='$IdMaterial'";
if(mysqli_query($conn, $sql)){
	echo '<script>M.toast({html:"Se actualizó correctamente.", classes: "rounded"})</script>';
}else{
	echo '<script>M.toast({html:"Ha ocurrido un error.", classes: "rounded"})</script>';	
}
?>
<script>
    folio = <?php echo $folio; ?>;
    setTimeout("location.href='detalles_pedido.php?folio='+folio", 300);
</script>