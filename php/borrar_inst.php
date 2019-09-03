<?php
include('../php/conexion.php');
$IdCliente = $conn->real_escape_string($_POST['valorIdCliente']);
$tipo = $conn->real_escape_string($_POST['tipo']);
if ($tipo == "reporte") {
  $ruta = "../views/reportes.php";
}elseif ($tipo == "instalacion") {
  $ruta = "../views/instalaciones.php";
}elseif ($tipo == "comunidad") {
  $ruta = "../views/ruta_comunidad.php";
}
  if(mysqli_query($conn, "DELETE FROM `tmp_pendientes` WHERE `tmp_pendientes`.`id_cliente` = $IdCliente")){
    echo '<script >M.toast({html:"Instalacion Borrada de la Ruta.", classes: "rounded"})</script>';
    ?>
  <script>    
     function recargar3() {
    setTimeout("location.href='<?php echo $ruta; ?>'", 2000);
  }
  </script>
  <?php
  echo "<script>recargar3();</script>";
    }else{
    echo "<script >M.toast({html: 'Ha ocurrido un error.', classes: 'rounded'});/script>";
  }

mysqli_close($conn);
?>        
  