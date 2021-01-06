<?php
include('../php/is_logged.php');
include('../php/conexion.php');
include('../php/superAdmin.php');

$Folio = $conn->real_escape_string($_POST['valorFolio']);

if(mysqli_query($conn, "DELETE FROM pedidos WHERE folio = '$Folio'")){
    echo '<script >M.toast({html:"Pedido Borrado...", classes: "rounded"})</script>';	
}else{
    echo '<script >M.toast({html:"Ocurrio un error...", classes: "rounded"})</script>';
}
?>
<script>
	var a = document.createElement("a");
	a.href = "../views/pedidos.php";
	a.click();
</script>