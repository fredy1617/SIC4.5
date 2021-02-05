<?php
include('../php/conexion.php');

$Folio = $conn->real_escape_string($_POST['valorFolio']);

if(mysqli_query($conn, "UPDATE pedidos SET estatus='Entregado' WHERE folio='$Folio'")){
    echo '<script >M.toast({html:"Pedido actualizado...", classes: "rounded"})</script>';	
}else{
    echo '<script >M.toast({html:"Ocurrio un error...", classes: "rounded"})</script>';
}
?>
<script>
	var a = document.createElement("a");
	a.href = "../views/pedidos.php";
	a.click();
</script>