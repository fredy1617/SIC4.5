<!DOCTYPE html>
<html>
<head>
  <title>SIC | Ruta Comunidad</title>
  <?php
  #INCLUIMOS EL ARCHIVO DONDE ESTA LA BARRA DE NAVEGACION DEL SISTEMA
  include('fredyNav.php');
  #INCLUIMOS EL ARCHIVO CON LOS DATOS Y CONEXXION A LA BASE DE DATOS
  include('../php/conexion.php');
  $user_id = $_SESSION['user_id'];//ID DEL USUARIO LOGEADO EN LA SESSION DEL SISTEMA
  echo '<script>M.toast({html:"CARGANDO COMUNIDADES Y PENDIENTES...", classes: "rounded"})</script>';
  ?>
  <script>
      //FUNCION QUE MUESTRA LAS COMUNIDADES Y TODO EL CONTENIDO DE PENDIENTES
      function buscar(){
        var texto = $("input#busqueda").val();
        
      $.post("../php/buscar_rutaComunidad.php", {
              texto: texto,
            }, function(mensaje) {
                $("#comunidades").html(mensaje);
            }); 
      };
      //FUNCION QUE BORRA LAS INSATALACIONES DE LA LISTA PARA RUTA
      function borrar_inst(IdCliente){
          $.post("../php/borrar_inst.php", {
              valorIdCliente : IdCliente,
          }, function(mensaje){
              $("#add_delete").html(mensaje);
          });
      };
      //FUNCION QUE BORRA LOS REPORTES DE LA LISTA PARA RUTA
      function borrar_rep(IdReporte){
          $.post("../php/borrar_rep.php",{
              valorIdReporte : IdReporte
          }, function(mensaje){
              $("#add_delete").html(mensaje);
          });
      };
      //FUNCION QUE INSERTA TODOS LOS REPORTES EN CAMPO E INSTALACIONES SOLAMENTE
      function add_ruta(id_comunidad) {
          M.toast({html:"Insertando comunidada a ruta...", classes: "rounded"});
          $.post("../php/add_rutaCom.php", {valorIdComunidad: id_comunidad}, function(mensaje){
              $("#add_delete").html(mensaje);
          });
      };
      //FUNCION QUE HACE MOSTRAR EL MODAL PARA CREAR LA RUTA DONDE SE AGREGA RESPONSABLE, MATERIAL, ETC.
      function modal(){
         $(document).ready(function(){
            $('#rutamodal').modal();
            $('#rutamodal').modal('open'); 
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
  </script>
</head>
<body onload="buscar();">
  <div class="container">
   <div class="row"><br>
      <h3 class="hide-on-med-and-down col s10 m6 l6">Ruta por Comunidad</h3>
      <h5 class="hide-on-large-only col s10 m6 l6">Ruta por Comunidad</h5>
      <!-- BUSCADOR POR COMUNIDAD -->
      <form class="col s10 m5 l5">
          <div class="row">
            <div class="input-field col s12">
              <i class="material-icons prefix">search</i>
              <input id="busqueda" name="busqueda" type="text" class="validate" onkeyup="buscar();">
              <label for="busqueda">Buscar(Comunidad)</label>
            </div>
          </div>
      </form>
    </div>
    
    <!-- MUESTRA TODO LO QUE HAY POR CADA COMUNIDAD--->
    <div id="comunidades">
      
    </div>
    <div id="add_delete">
      <!-- MUESTRA Instalaciones DE RUTA--->
        <div class="row">
        <h4 class="hide-on-med-and-down">Ruta Instalaciones</h4>
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
                $sql_tmp = mysqli_query($conn,"SELECT * FROM tmp_pendientes WHERE ruta_inst = 0 AND usuario = $user_id");
                $columnas = mysqli_num_rows($sql_tmp);
                if($columnas == 0){
                    ?>
                    <h5 class="center">No hay instalaciones en ruta</h5>
                    <?php
                }else{
                    while($tmp = mysqli_fetch_array($sql_tmp)){
                        $id_comunidad = $tmp['lugar'];
                        $sql_comunidad1 = mysqli_fetch_array(mysqli_query($conn,"SELECT nombre FROM comunidades WHERE id_comunidad=$id_comunidad"));
                        $id_cliente = $tmp['id_cliente'];
                        $serv = mysqli_fetch_array(mysqli_query($conn,"SELECT * FROM clientes WHERE id_cliente=$id_cliente"));

                    ?>
                    <tr>
                      <td><?php echo $tmp['id_cliente']; ?></td>
                      <td><?php echo $tmp['nombre']; ?></td>
                      <td><?php echo $serv['servicio']; ?></td>
                      <td><?php echo $tmp['telefono']; ?></td>
                      <td><?php echo $sql_comunidad1['nombre']; ?></td>
                      <td><?php echo $tmp['direccion']; ?></td>
                      <td><a onclick="borrar_inst(<?php echo $tmp['id_cliente']; ?>);" class="btn btn-floating red darken-1 waves-effect waves-light"><i class="material-icons">delete</i></a></td>
                    </tr>
                <?php
                    }
                }
                ?>
                </tbody>
            </table>
          </div>
        </div><br>
      <!-- FIN Instalaciones DE RUTA--->
      <!-- MUESTRA REPORTES DE RUTA--->
        <div class="row" >
            <div id="reporte_borrar"></div>
          <h4 class="hide-on-med-and-down">Ruta Reportes</h4>
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
              $sql_tmp = mysqli_query($conn, "SELECT * FROM tmp_reportes WHERE ruta = 0 AND usuario = $user_id");
              $columnas = mysqli_num_rows($sql_tmp);
              if ($columnas == 0) {
                  echo "<h5 class = 'center'>No hay reportes en ruta</h5>";
              }else{
                while ($tmp = mysqli_fetch_array($sql_tmp)) {
                    $id_reporte = $tmp['id_reporte'];                    
                    if ((mysqli_num_rows(mysqli_query($conn, "SELECT * FROM reportes WHERE id_reporte = $id_reporte"))) == 0){
                      $sql = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM orden_servicios WHERE id = $id_reporte")); 
                      $id = $sql['id'];
                      $Descripcion = ($sql['trabajo'] == '')? $sql['solicitud']: $sql['trabajo'];  
                    }else{
                      $sql = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM reportes WHERE id_reporte = $id_reporte")); 
                      $id = $sql['id_reporte'];
                     $Descripcion = $sql['descripcion'];
                    }
                    $id_cliente = $sql['id_cliente'];
                    $ver = mysqli_query($conn, "SELECT * FROM clientes WHERE id_cliente = $id_cliente");
                    if (mysqli_num_rows($ver) == 0) {
                        $ver = mysqli_query($conn, "SELECT * FROM especiales WHERE id_cliente = $id_cliente");
                    }
                    $sql_nombre = mysqli_fetch_array($ver);
                    $id_comunidad = $sql_nombre['lugar'];
                    $comunidad = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM comunidades WHERE id_comunidad = $id_comunidad"));
                ?>
                <tr>
                    <td><?php echo $id; ?></td>
                    <td><?php echo $sql_nombre['nombre']; ?></td>
                    <td><?php echo $comunidad['nombre']; ?></td>
                    <td><?php echo $Descripcion; ?></td>
                    <td><?php echo $sql['fecha']; ?></td>
                    <td><a onclick="borrar_rep(<?php echo $id; ?>);" class="btn btn-floating red darken-1 waves-effect waves-light"><i class="material-icons">delete</i></a></td>
                </tr>
                <?php
                }
              }
              ?>
              </tbody>
          </table>
          </div>
        </div>
        <br><br>
        <a onclick="modal()" class="btn waves-light waves-effect right pink">Imprimir</a>
      <!-- FIN REPORTES DE RUTA--->
    </div>
  </div>
</body>
</html>