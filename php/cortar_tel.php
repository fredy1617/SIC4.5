<?php 
include('../php/conexion.php');
$id_cliente = $conn->real_escape_string($_POST['valorIdCliente']);

if(mysqli_query($conn, "UPDATE clientes SET tel_cortado = 1 WHERE id_cliente = $id_cliente")){
	echo '<script>M.toast({html:"Cliente no: '.$id_cliente.' cortado.", classes: "rounded"})</script>';
	echo '<script>recargar10()</script>';
}else{
	echo '<script>M.toast({html:"Ocurrio un error.", classes: "rounded"})</script>';	
}

//echo mysqli_error($conn);
mysqli_close($conn);
?>