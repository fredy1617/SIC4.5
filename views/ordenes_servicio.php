 <html>
<head>
	<title>SIC | Ordenes de Servicio</title>
<?php 
include('fredyNav.php');
include('../php/conexion.php');
include ('../php/cobrador.php');
$id_user = $_SESSION['user_id'];
?>
<!--Inicia Script de reportes tmp-->
<script>
  function borrar_inst(IdCliente){
    $.post("../php/borrar_inst.php", {   
            valorIdCliente: IdCliente,
    }, function(mensaje) {
    $("#delete").html(mensaje);
    }); 
  };
  function borrar_rep(IdReporte){
    $.post("../php/borrar_rep.php", {  
            valorIdReporte: IdReporte,
    }, function(mensaje) {
    $("#delete").html(mensaje);
    }); 
  };
  function ruta(id_reporte) {
      if (id_reporte == "") {
        M.toast({html:"Ocurrio un error al seleccionar el reporte.", classes: "rounded"});
      }else{
        $.post("../php/insert_tmp_reportes.php", {
            valorIdReporte: id_reporte,
          }, function(mensaje) {
              $("#resultado_ruta_reporte").html(mensaje);
          }); 
      }
  };
  function modal(){
   $(document).ready(function(){
      $('#rutamodal').modal();
      $('#rutamodal').modal('open'); 
   });
 };
</script>
<!--Termina script dispositivos-->
</head>
<main>
<body>
<div class="container">
  <div class="row">
      <br><br>
      <h3>Ordenes de Servicio </h3>
        <div class="col s4 m3 l3"><br>
          <a class="waves-effect waves-light btn pink" href="../views/ordenes_pendientes.php"><i class="material-icons prefix right">send</i>Pendientes</a>
        </div>        
    </div>
  <?php
  #<!-- ************  VISTA PARA REDES Y ADMINISTRADORES  ****************** -->

  #VERIFICAMOS QUE EL USUARIO LOGEADO PERTENEZCA A LOS SUPER ADMINISTRADORES O SEA DEL DEPARTAMENTO DE REDES
  if ((($id_user == 49 OR $id_user == 10 OR $id_user == 75 OR $id_user == 77) AND $area['area'] == "Administrador") OR $area['area'] == 'Redes' OR $id_user == 25 OR $id_user == 28) {
    #SI SI PERTENECE MOSTRAR TODAS LAS ORDENES SEPARADAS POR DEPARTAMENTO
  ?>
    <div class="row">
      <h5>Redes Ordenes Pendientes</h5>
      <div class="row">
      <?php 
      #CONTENIDO DE REDES
      $sql_orden = mysqli_query($conn,"SELECT * FROM orden_servicios  WHERE  estatus IN ('PorConfirmar', 'Revisar', 'Cotizar', 'Cotizado', 'Autorizado', 'Pedir', 'Ejecutar')  AND dpto = 1 ORDER BY fecha");
      include ('../php/tabla_ordenes_pendientes.php');
      ?>
      </div>
    </div>
  <?php } //CIERRA IF 
 
  #<!-- *************  VISTA PARA TALLER Y ADMINISTRADORES  **************** -->

  #VERIFICAMOS QUE EL USUARIO LOGEADO PERTENEZCA A LOS SUPER ADMINISTRADORES O SEA DEL DEPARTAMENTO DE TALLER
  if ((($id_user == 49 OR $id_user == 10 OR $id_user == 75 OR $id_user == 77 OR $id_user == 70) AND $area['area'] == "Administrador") OR $area['area'] == 'Taller') {
    #SI SI PERTENECE MOSTRAR TODAS LAS ORDENES SEPARADAS POR DEPARTAMENTO
  ?>
    <div class="row">
      <h5>Taller Ordenes Pendientes</h5>
      <?php 
        #CONTENIDO TALLER
      $sql_orden = mysqli_query($conn,"SELECT * FROM orden_servicios  WHERE  estatus IN ('PorConfirmar', 'Revisar', 'Cotizar', 'Cotizado', 'Autorizado', 'Pedir', 'Ejecutar')  AND dpto = 2 ORDER BY fecha");

      include ('../php/tabla_ordenes_pendientes.php');
      ?>
    </div>
  <?php } //CIERRA IF 

  #<!-- *************  VISTA PARA VENTAS Y ADMINISTRADORES  **************** -->

  #VERIFICAMOS QUE EL USUARIO LOGEADO PERTENEZCA A LOS SUPER ADMINISTRADORES O SEA DEL DEPARTAMENTO DE VENTAS
  if ((($id_user == 49 OR $id_user == 10 OR $id_user == 75 OR $id_user == 77) AND $area['area'] == "Administrador") OR $id_user == 59 OR $id_user == 66 OR $id_user == 70) {
    #SI SI PERTENECE MOSTRAR TODAS LAS ORDENES SEPARADAS POR DEPARTAMENTO
  ?>
    <div class="row">
      <h5>Ventas Ordenes Pendientes</h5>
      <?php 
        #CONTENIDO DE VENTAS
      $sql_orden = mysqli_query($conn,"SELECT * FROM orden_servicios  WHERE  estatus IN ('PorConfirmar', 'Revisar', 'Cotizar', 'Cotizado', 'Autorizado', 'Pedir', 'Ejecutar')  AND dpto = 3 ORDER BY fecha");

      include ('../php/tabla_ordenes_pendientes.php');
      ?>
    </div>
  <?php } //CIERRA IF ?>

  <br><br><br>
  <div id="delete">
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
  </div>
<br><br><br>
</div>
<br>
</body>
</main>
</html>