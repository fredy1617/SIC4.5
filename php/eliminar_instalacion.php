<?php
include('../php/conexion.php');
$id_cliente = $conn->real_escape_string($_POST['valorIdCliente']);

$cliente=mysqli_fetch_array(mysqli_query($conn,"SELECT * FROM clientes WHERE id_cliente = $id_cliente"));

$nombre = $cliente['nombre'];
$telefono = $cliente['telefono'];
$direccion = $cliente['direccion'];
$referencia = $cliente['referencia'];
$lugar = $cliente['lugar'];

$repetida = mysqli_query($conn, "SELECT * FROM canceladas WHERE id_cliente = '$id_cliente' AND nombre = '$nombre' AND telefono = '$telefono' AND direccion = '$direccion' AND referencia = '$referencia' AND lugar = '$lugar'");
$si = mysqli_num_rows($repetida);

if ($si > 0) {
	echo "<script>M.toast({ html: 'Esta cliente ya fue registrado en canceladas.', classes: 'rounded'});</script>";
	if (mysqli_query($conn, "DELETE FROM clientes WHERE id_cliente = $id_cliente")) {
		echo "<script> M.toast({html: 'Se elimino la instalacion correctamente.', classes: 'rounded'});</script>";
		echo '<script>recargar()</script>';
	}
}else{
	$sql = "INSERT INTO canceladas (id_cliente, nombre, telefono, direccion, referencia, lugar) VALUES ('$id_cliente', '$nombre', '$telefono', '$direccion', '$referencia','$lugar') ";
	if (mysqli_query($conn, $sql)) {
		if (mysqli_query($conn, "DELETE FROM clientes WHERE id_cliente = $id_cliente")) {
			echo "<script> M.toast({html: 'Se elimino la instalacion correctamente.', classes: 'rounded'});</script>";
			echo '<script>recargar()</script>';
		}else{
			echo "<script>M.toast({html: 'Solo se agrego a canceladas pero no se borro cliente', classes: 'rounded'});</script>";
			echo '<script>recargar()</script>';
		}
	}else{
		echo "<script> M.toast({ html: 'Ocurrio un error.', classes: 'rounded' }); </script>";
	}
}
?>