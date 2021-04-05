<?php
date_default_timezone_set('America/Mexico_City');
include('../php/conexion.php');
include('is_logged.php');
$IdCliente = $conn->real_escape_string($_POST['valorIdCliente']);
$id_user = $_SESSION['user_id'];
$Tipo_Campio = $conn->real_escape_string($_POST['valorTipo_Cambio']);
$Cantidad = $conn->real_escape_string($_POST['valorCantidad']);
$Fecha = date('Y-m-d');
$Hora = date('H:i:s');

$sql = "INSERT INTO pagos (id_cliente, descripcion, cantidad, fecha, hora, tipo, id_user, corte, tipo_cambio) VALUES ('$IdCliente', 'Abono de instalacion', '$Cantidad', '$Fecha', '$Hora', 'Abono Instalacion', '$id_user', 0, '$Tipo_Campio')";
if (mysqli_query($conn, $sql)){
	$cliente = mysqli_fetch_array(mysqli_query($conn,"SELECT * FROM clientes WHERE id_cliente = $IdCliente"));
	$Dejo = $cliente['dejo'];
	$Dejo = $Dejo+$Cantidad;
	$sql2="UPDATE clientes SET dejo = '$Dejo' WHERE id_cliente=$IdCliente";            
	mysqli_query($conn,$sql2);
	echo '<script>M.toast({html:"El pago se dió de alta satisfcatoriamente.", classes: "rounded"})</script>';
	$ultimo =  mysqli_fetch_array(mysqli_query($conn, "SELECT MAX(id_pago) AS id FROM pagos WHERE id_cliente = $IdCliente"));            
	$id_pago = $ultimo['id'];
	?>
	<script>
		id_pago = <?php echo $id_pago; ?>;
		var a = document.createElement("a");
			a.target = "_blank";
			a.href = "../php/imprimir.php?IdPago="+id_pago;
			a.click();
	</script>
   	<?php 
	echo '<script>recargar()</script>';
}
mysqli_close($conn);
?>