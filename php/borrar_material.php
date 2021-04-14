<?php
include('../php/conexion.php');

$Folio = $conn->real_escape_string($_POST['valorFolio']);
$id = $conn->real_escape_string($_POST['valorID']);
$Ruta = $conn->real_escape_string($_POST['valorRuta']);

if(mysqli_query($conn, "DELETE FROM detalles_pedidos WHERE id = '$id' AND folio = '$Folio'")){
    echo '<script >M.toast({html:"Material Borrado...", classes: "rounded"})</script>';	
}else{
    echo '<script >M.toast({html:"Ocurrio un error...", classes: "rounded"})</script>';
}
?>
<script>
    folio = <?php echo $Folio ?>;
  	var ruta = $("input#ruta").val();
    setTimeout("location.href=ruta+'?folio='+folio", 500);
</script>
<input id="ruta" type="hidden" value="<?php echo $Ruta; ?>">