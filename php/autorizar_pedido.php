<?php
include('../php/conexion.php');
$folio = $conn->real_escape_string($_POST['folio']);

if(mysqli_query($conn, "UPDATE pedidos SET estatus = 'Autorizado' WHERE folio = '$folio'")){
		echo '<script>M.toast({html:"Orden actualizada correctamente..", classes: "rounded"})</script>';
}else{
		echo '<script>M.toast({html:"Ocurrio un error!", classes: "rounded"})</script>';
}
?>
<script>
	var a = document.createElement("a");
	 a.href = "../views/pedidos.php";
	 a.click();
</script>  


        