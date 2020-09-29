<?php
#INCLUIMOS EL ARCHIVO CON LOS DATOS Y CONEXXION A LA BASE DE DATOS
include('../php/conexion.php');
#INCLUIMOS EL PHP DONDE VIENE LA INFORMACION DEL INICIO DE SESSION
include('is_logged.php');
$id_user = $_SESSION['user_id'];//ID DEL USUARIO LOGEADO EN LA SESSION DEL SISTEMA
#RECIBIMOS EL LA VARIABLE valorIdReporte CON EL METODO POST QUE ES EL ID DEL REPORTE PARA PODERLO BORRAR
$IdReporte = $conn->real_escape_string($_POST['valorIdReporte']);
#VERIFICAMOS QUE SE BORRE CORRECTAMENTE EL REPORTE DE tmp_reportes
if(mysqli_query($conn, "DELETE FROM `tmp_reportes` WHERE `tmp_reportes`.`id_reporte` = $IdReporte")){
  #SI ES ELIMINADO MANDAR MSJ CON ALERTA
  echo '<script >M.toast({html:"Reporte Borrado de la Ruta.", classes: "rounded"})</script>';
}else{
  #SI NO ES BORRADO MANDAR UN MSJ CON ALERTA
  echo "<script >M.toast({html: 'Ha ocurrido un error.', classes: 'rounded'});/script>";
}
?> 
<!-- MUESTRA Instalaciones DE RUTA--->
<div class="row">
  <h3 class="hide-on-med-and-down">Ruta Instalaciones</h3>
  <h5 class="hide-on-large-only">Ruta Instalaciones</h5>
  <div id="instalaciones_ruta">
    <table class="bordered highlight responsive-table">
      <thead>
        <tr>
          <th>No. Cliente</th>
          <th>Nombre</th>
          <th>Servicio</th>
          <th>Telefono</th>
          <th>Lugar</th>
          <th>Dirección</th>
          <th>Borrar</th>
        </tr>
      </thead>
      <tbody>
      <?php 
      $sql_tmp = mysqli_query($conn,"SELECT * FROM tmp_pendientes WHERE ruta_inst = 0 AND usuario = $id_user");
      if(mysqli_num_rows($sql_tmp) == 0){
        echo '<h5 class="center">No hay instalaciones en ruta</h5>';
      }else{
        while($tmp = mysqli_fetch_array($sql_tmp)){
          $id_comunidad = $tmp['lugar'];
          $comunidad = mysqli_fetch_array(mysqli_query($conn,"SELECT nombre FROM comunidades WHERE id_comunidad=$id_comunidad"));
          $id_cliente = $tmp['id_cliente'];
          $serv = mysqli_fetch_array(mysqli_query($conn,"SELECT * FROM clientes WHERE id_cliente=$id_cliente"));
          ?>
          <tr>
            <td><?php echo $tmp['id_cliente']; ?></td>
            <td><?php echo $tmp['nombre']; ?></td>
            <td><?php echo $serv['servicio']; ?></td>
            <td><?php echo $tmp['telefono']; ?></td>
            <td><?php echo $comunidad['nombre']; ?></td>
            <td><?php echo $tmp['direccion']; ?></td>
            <td><a onclick="borrar_inst(<?php echo $tmp['id_cliente']; ?>);" class="btn btn-floating red darken-1 waves-effect waves-light"><i class="material-icons">delete</i></a></td>
          </tr>
          <?php
          } //FIN WHILE
        }//FIN ELSE
        ?>
      </tbody>
    </table>
  </div>
</div><br>
<!-- FIN Instalaciones DE RUTA--->
<!-- MUESTRA REPORTES DE RUTA--->
<div class="row" >
  <div id="reporte_borrar"></div>
  <h3 class="hide-on-med-and-down">Ruta Reportes</h3>
  <h5 class="hide-on-large-only">Ruta Reportes</h5>
  <div id="resultado_ruta_reporte">
    <table>
      <thead>
        <tr>
          <th>No. Reporte</th>
          <th>Cliente</th>
          <th>Comunidad</th>
          <th>Descripción</th>
          <th>Fecha</th>
          <th>Borrar</th>
        </tr>
      </thead>
      <tbody>
      <?php
      $sql_tmp = mysqli_query($conn, "SELECT * FROM tmp_reportes WHERE ruta = 0 AND usuario = $id_user");
      if (mysqli_num_rows($sql_tmp) == 0) {
        echo "<h5 class = 'center'>No hay reportes en ruta</h5>";
      }else{
        while ($tmp = mysqli_fetch_array($sql_tmp)) {
          $id_reporte = $tmp['id_reporte'];                    
          if ((mysqli_num_rows(mysqli_query($conn, "SELECT * FROM reportes WHERE id_reporte = $id_reporte"))) == 0){
            $reporte = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM orden_servicios WHERE id = $id_reporte")); 
            $id = $reporte['id'];
            $Descripcion = ($reporte['trabajo'] == '')? $reporte['solicitud']: $reporte['trabajo'];  
          }else{
            $reporte = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM reportes WHERE id_reporte = $id_reporte")); 
            $id = $reporte['id_reporte'];
            $Descripcion = $reporte['descripcion'];
          }
          $id_cliente = $reporte['id_cliente'];
          $ver = mysqli_query($conn, "SELECT * FROM clientes WHERE id_cliente = $id_cliente");
          if (mysqli_num_rows($ver) == 0) {
            $ver = mysqli_query($conn, "SELECT * FROM especiales WHERE id_cliente = $id_cliente");
          }
          $cliente = mysqli_fetch_array($ver);
          $id_comunidad = $cliente['lugar'];
          $comunidad = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM comunidades WHERE id_comunidad = $id_comunidad"));
          ?>
          <tr>
            <td><?php echo $id; ?></td>
            <td><?php echo $cliente['nombre']; ?></td>
            <td><?php echo $comunidad['nombre']; ?></td>
            <td><?php echo $Descripcion; ?></td>
            <td><?php echo $reporte['fecha']; ?></td>
            <td><a onclick="borrar_rep(<?php echo $id; ?>);" class="btn btn-floating red darken-1 waves-effect waves-light"><i class="material-icons">delete</i></a></td>
          </tr>
          <?php
        }//FIN WHILE
      }//FIN ELSE
      ?>
      </tbody>
    </table>
  </div>
</div>
<br><br>
<!-- FIN REPORTES DE RUTA--->
<a onclick="modal()" class="btn waves-light waves-effect right pink">Imprimir</a>