<?php
include('../php/conexion.php');
$IdReporte = $conn->real_escape_string($_POST['valorIdReporte']);
$tipo = $conn->real_escape_string($_POST['tipo']);
if ($tipo == "reporte") {
  $ruta = "../views/reportes.php";
}elseif ($tipo == "instalacion") {
  $ruta = "../views/instalaciones.php";
}elseif ($tipo == "comunidad") {
  $ruta = "../views/ruta_comunidad.php";
}
  if(mysqli_query($conn, "DELETE FROM `tmp_reportes` WHERE `tmp_reportes`.`id_reporte` = $IdReporte")){
    echo '<script >M.toast({html:"Reporte Borrado de la Ruta.", classes: "rounded"})</script>';
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