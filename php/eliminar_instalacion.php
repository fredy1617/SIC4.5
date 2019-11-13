<?php
include('../php/conexion.php');
include('../php/is_logged.php');
include('../php/superAdmin.php');

date_default_timezone_set('America/Mexico_City');
$Fecha_Hoy = date('Y-m-d');

$id_cliente = $conn->real_escape_string($_POST['valorIdCliente']);

$cliente=mysqli_fetch_array(mysqli_query($conn,"SELECT * FROM clientes WHERE id_cliente = $id_cliente"));

$nombre = $cliente['nombre'];
$telefono = $cliente['telefono'];
$direccion = $cliente['direccion'];
$referencia = $cliente['referencia'];
$lugar = $cliente['lugar'];
$instalada = $conn->real_escape_string($_POST['valorInstalada']);
if ($instalada == "No") {
	$Motivo = "Instalación no requerida (no instalada)";
	$ruta = '<script>recargar()</script>';
}else{
	$Motivo =  $conn->real_escape_string($_POST['valorMotivo']);
	$ruta = '<script>recargar2()</script>';
}

$repetida = mysqli_query($conn, "SELECT * FROM canceladas WHERE id_cliente = '$id_cliente' AND nombre = '$nombre' AND telefono = '$telefono' AND direccion = '$direccion' AND referencia = '$referencia' AND lugar = '$lugar'");
$si = mysqli_num_rows($repetida);

if ($si > 0) {
	echo "<script>M.toast({ html: 'Esta cliente ya fue registrado en canceladas.', classes: 'rounded'});</script>";
	if (mysqli_query($conn, "DELETE FROM clientes WHERE id_cliente = $id_cliente")) {
		echo "<script> M.toast({html: 'Se elimino la instalacion correctamente.', classes: 'rounded'});</script>";
		echo '<script>recargar()</script>';
	}
}else{
	$sql = "INSERT INTO canceladas (id_cliente, nombre, telefono, direccion, referencia, lugar, fecha, motivo) VALUES ('$id_cliente', '$nombre', '$telefono', '$direccion', '$referencia','$lugar', '$Fecha_Hoy', '$Motivo') ";
	if (mysqli_query($conn, $sql)) {
		if (mysqli_query($conn, "DELETE FROM clientes WHERE id_cliente = $id_cliente")) {
			echo "<script> M.toast({html: 'Se elimino la instalacion correctamente.', classes: 'rounded'});</script>";
			echo $ruta;
		}else{
			echo "<script>M.toast({html: 'Solo se agrego a canceladas pero no se borro cliente', classes: 'rounded'});</script>";
			echo $ruta;
		}
	}else{
		echo "<script> M.toast({ html: 'Ocurrio un error.', classes: 'rounded' }); </script>";
	}
}
?>