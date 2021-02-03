<?php 
date_default_timezone_set('America/Mexico_City');
include('../php/conexion.php');
$IdMaterial = $conn->real_escape_string($_POST['valorID']);
$folio = $conn->real_escape_string($_POST['valorFolio']);
$Listo = $conn->real_escape_string($_POST['valorListo']);
$Fecha_hoy = date('Y-m-d');//CREAMOS UNA FECHA DEL DIA EN CURSO SEGUN LA ZONA HORARIA


$sql = "UPDATE detalles_pedidos SET listo = $Listo WHERE id='$IdMaterial'";
if(mysqli_query($conn, $sql)){
	echo '<script>M.toast({html:"Se actualizó correctamente.", classes: "rounded"})</script>';
}else{
	echo '<script>M.toast({html:"Ha ocurrido un error.", classes: "rounded"})</script>';	
}
$LISTOS = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM detalles_pedidos WHERE folio = $folio AND listo = 1"));
$TOTAL = mysqli_num_rows( mysqli_query($conn, "SELECT * FROM detalles_pedidos WHERE folio = $folio"));

if ($LISTOS == $TOTAL){
	if(mysqli_query($conn, "UPDATE pedidos SET estatus = 'Completo', fecha_completo = '$Fecha_hoy' WHERE folio = '$folio'")){
		echo '<script>M.toast({html:"Pedido actualizado correctamente..", classes: "rounded"})</script>';
	}else{
			echo '<script>M.toast({html:"Ocurrio un error!", classes: "rounded"})</script>';
	}
}
?>
<script>
    folio = <?php echo $folio; ?>;
    setTimeout("location.href='detalles_pedido.php?folio='+folio", 300);
</script>