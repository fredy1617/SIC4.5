<?php
#INCLUIMOS EL ARCHIVO QUE HACE LA CONEXION A LA BASE DE DATOS
include('../php/conexion.php');
$id_ruta = $conn->real_escape_string($_POST['valorIdRuta']);//RECIBIMOS UNA VARIABLE CON EL METODO POST DEL DOC (rutas.php) Y LA ASIGNAMOS A $id_ruta
$Pedidos = array();// CREAMOS UN ARRAY DONDE IREMOS ALMACENANDO LOS PEDIDOS PARA DESPUES MOSTRARLOS
#HACEMOS UNA CONSULTA DE SI HAY MANTENIMIENTOS EN LA RUTA
$sql_mant = mysqli_query($conn,"SELECT tmp_reportes.id_reporte FROM tmp_reportes INNER JOIN reportes ON tmp_reportes.id_reporte = reportes.id_reporte WHERE (tmp_reportes.ruta = $id_ruta AND reportes.descripcion LIKE 'Mantenimiento:%')");
#VERIFICAMOS SI HAY MENTENIMIENTOS
if (mysqli_num_rows($sql_mant)>0) {
  #SI TIENE MANTENIMEINTOS VER SI TIENEN PEDIDOS';
  while ($mant = mysqli_fetch_array($sql_mant)) {
    $id_rep = $mant['id_reporte'];#ID DEL TMP REPORTE EN TURNO
    $sql_pedidos = mysqli_query($conn,"SELECT * FROM pedidos WHERE id_orden = $id_rep");
    if (mysqli_num_rows($sql_pedidos)) {
      while ($pedido = mysqli_fetch_array($sql_pedidos)) {
        $Pedidos[$pedido['folio']] = $pedido['id_orden'];//AGREFAMOS UN ELEMENTO AL ARRAY (UN PEDIDO DEL MANTENIMIENTO)
      }//FIN WHILE PEDIDOS
    }//FIN IF PEDIDOS
  }//FIN WHILE MANTENIMIENTOS
}//FIN IF MANTENIMIENTOS
#HACEMOS UNA CONSULTA DE SI HAY ORDENES DE SERVICO EN LA RUTA
$sql_orden = mysqli_query($conn,"SELECT id_reporte FROM tmp_reportes WHERE (ruta = $id_ruta AND id_reporte > 100000)");
#VERIFICAMOS SI HAY ORDENES DE SERVICIO
if (mysqli_num_rows($sql_orden)) {
  #SI TIENE ORDENES DE SERVICIO VER SI TIENEN PEDIDOS
  while ($orden = mysqli_fetch_array($sql_orden)) {
    $id_orden = $orden['id_reporte'];#ID DEL TMP REPORTE EN TURNO
    $sql_pedidos = mysqli_query($conn,"SELECT * FROM pedidos WHERE id_orden = $id_orden");
    if (mysqli_num_rows($sql_pedidos)) {
      while ($pedido = mysqli_fetch_array($sql_pedidos)) {
        $Pedidos[$pedido['folio']] = $pedido['id_orden'];//AGREFAMOS UN ELEMENTO AL ARRAY (UN PEDIDO DEL ORDENES DE SERVISIO)
      }//FIN WHILE PEDIDOS
    }//FIN IF PEDIDOS
  }//FIN WHILE ORDENES DE SERVICIO
}//FIN IF ORDENES DE SERVISIO

$sql_pedidos_r = mysqli_query($conn,"SELECT * FROM pedidos WHERE id_orden = $id_ruta");
if (mysqli_num_rows($sql_pedidos_r)) {
    while ($pedido = mysqli_fetch_array($sql_pedidos_r)) {
      $Pedidos[$pedido['folio']] = $pedido['id_orden'];//AGREFAMOS UN ELEMENTO AL ARRAY (UN PEDIDO DEL ORDENES DE SERVISIO)
    }//FIN WHILE PEDIDOS DE RUTA
}//FIN IF PEDIDOS DE RUTA

?>
<script>
  $(document).ready(function(){
      $('#pedidos').modal();
      $('#pedidos').modal('open'); 
  });
</script>

<!-- Modal pedidos IMPOTANTE! -->
<div id="pedidos" class="modal"><br>
  <div class="modal-content">
    <h5 class="red-text darken-2 center"><b>Pedidos de la Ruta No. <?php echo $id_ruta ?></b></h5><br><br>
    <?php
      if (count($Pedidos) > 0) {
        foreach ($Pedidos as $clave => $valor) {
            // $array[3] se actualizará con cada valor de $array...
          $es= ($valor > 100000)?'Orden de Servicio':'Mantenimiento';
          $es= ($valor < 5000)?'Ruta No':$es;
          echo "<b> - <a href = '../views/detalles_pedido.php?folio={$clave}'>Pedido No.{$clave}</a> ({$es}:{$valor}) </b><br>";
        }
      }else{
        echo "<b> - NO SE ENCONTRARON PEDIDOS EN LA RUTA </b>";
      }
    ?>
  </div>
  <div class="modal-footer">
     <a href="#" class="modal-action modal-close waves-effect waves-green btn-flat">Cerrar<i class="material-icons right">close</i></a>
  </div><br>
</div>
<!--Cierre modal PAGOS IMPOTANTE! -->