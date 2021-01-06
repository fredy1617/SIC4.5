<?php
include('../php/conexion.php');

$Folio = $conn->real_escape_string($_POST['valorFolio']);
$id = $conn->real_escape_string($_POST['valorID']);

if(mysqli_query($conn, "DELETE FROM detalles_pedidos WHERE id = '$id' AND folio = '$Folio'")){
    echo '<script >M.toast({html:"Material Borrado...", classes: "rounded"})</script>';	
}else{
    echo '<script >M.toast({html:"Ocurrio un error...", classes: "rounded"})</script>';
}
?>
<script>
    var a = document.createElement("a");
	a.href = "../views/detalles_pedido.php?folio="+<?php echo $Folio; ?>;
	a.click();
</script>