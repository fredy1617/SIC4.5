<?php
include('../php/is_logged.php');
include('../php/conexion.php');
include('../php/superAdmin.php');

$Codigo = $conn->real_escape_string($_POST['valorCodigo']);

if(mysqli_query($conn, "DELETE FROM inventario WHERE codigo = '$Codigo'")){
    echo '<script >M.toast({html:"Producto Borrado...", classes: "rounded"})</script>';	
}else{
    echo '<script >M.toast({html:"Ocurrio un error...", classes: "rounded"})</script>';
}
?>
<script>
    setTimeout("location.href='../views/inventario.php'", 1000);
</script>