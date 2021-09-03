<?php
session_start();
include('../php/conexion.php');

$Folio = $conn->real_escape_string($_POST ['valorFolio']);
$Fecha = $conn->real_escape_string($_POST['valorFecha']);

$id_user = $_SESSION['user_id'];
$sql2= "UPDATE pedidos SET fecha_requerido = '$Fecha' WHERE  folio = $Folio";
if (mysqli_query($conn, $sql2)) {
  echo  '<script>M.toast({html:"Información actualizada...", classes: "rounded"})</script>';
  ?>
  <script>    
    var a = document.createElement("a");
      a.href = "../views/detalles_pedido.php?folio=<?php echo $Folio; ?>";
      a.click();
  </script>
  <?php
}else{
	echo  '<script>M.toast({html:"Ha ocurrido un error.", classes: "rounded"})</script>';	
}
mysqli_close($conn);
?>  