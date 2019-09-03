<html>
<head>
	<title>SIC | Reportes Pendientes</title>
<?php 
include('fredyNav.php');
include('../php/conexion.php');
include ('../php/cobrador.php');
?>
<!--Inicia Script de reportes tmp-->
<script>
  function buscar_rep(){
    var texto = $("input#busqueda").val();
    
  $.post("../views/buscar_reportes.php", {
          texto: texto,
        }, function(mensaje) {
            $("#EstosQUe").html(mensaje);
        }); 
  };
  function borrar_inst(IdCliente){
    $.post("../php/borrar_inst.php", {          
            tipo : "reporte", 
            valorIdCliente: IdCliente,
    }, function(mensaje) {
    $("#borrar_inst").html(mensaje);
    }); 
  };
  function borrar_rep(IdReporte){
    $.post("../php/borrar_rep.php", {           
            tipo : "reporte",
            valorIdReporte: IdReporte,
    }, function(mensaje) {
    $("#reporte_borrar").html(mensaje);
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
<body onload="buscar_rep();">
<div id="borrar_inst"></div>
<div id="reporte_borrar"></div>
<div class="container">
  <div class="row">
      <br><br>
      <h3 class="hide-on-med-and-down col s12 m6 l6">Reportes Pendientes</h3>
      <h5 class="hide-on-large-only col s12 m6 l6">Reportes Pendientes</h5>

          <form class="col s10 m5 l5">
          <div class="row">
            <div class="input-field col s12">
              <i class="material-icons prefix">search</i>
              <input id="busqueda" name="busqueda" type="text" class="validate" onkeyup="buscar_rep();">
              <label for="busqueda">Buscar(#Reporte, Nombre Clientre)</label>
            </div>
          </div>
        </form>
        <div class="col s2 m1 l1"><br>
          <a class="waves-effect waves-light btn pink" href="../php/imprimir_reportes.php" target="blank"><i class="material-icons black-text Small">picture_as_pdf</i></a>
        </div>
      <a class="waves-effect waves-light btn pink right" href="../views/ruta_comunidad.php">Por Comunidad<i class="material-icons left">location_city</i></a>
        
    </div>
    <div class="row">
      <p><div id="resultado_reporte_pendiente">
        <table class="bordered  highlight responsive-table">
          <thead>
            <tr>
                <th>Estatus</th>
                <th>No.Rep</th>
                <th>Cliente</th>
                <th>Descripción</th>
                <th>Fecha</th>
                <th>Comunidad</th>
                <th>Técnico</th>
                <th>Registró</th>
                <th>Atender</th>
                <th>+Ruta</th>
                <th>Editar</th>
            </tr>
          </thead>
          <tbody id="EstosQUe">
          </tbody>
        </table>
    </div></p>
  </div>
  <br><br><br>
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
                $sql_tmp = mysqli_query($conn,"SELECT * FROM tmp_pendientes WHERE ruta_inst  =0");
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
              $sql_tmp = mysqli_query($conn, "SELECT * FROM tmp_reportes WHERE ruta = 0");
              $columnas = mysqli_num_rows($sql_tmp);
              if ($columnas == 0) {
                  echo "<h5 class = 'center'>No hay reportes en ruta</h5>";
              }else{
                while ($tmp = mysqli_fetch_array($sql_tmp)) {
                    $id_reporte = $tmp['id_reporte'];
                    $sql_reporte = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM reportes WHERE id_reporte = $id_reporte"));
                    $id_cliente = $sql_reporte['id_cliente'];
                    $ver = mysqli_query($conn, "SELECT * FROM clientes WHERE id_cliente = $id_cliente");
                    if (mysqli_num_rows($ver) == 0) {
                        $ver = mysqli_query($conn, "SELECT * FROM especiales WHERE id_cliente = $id_cliente");
                    }
                    $sql_nombre = mysqli_fetch_array($ver);
                    $id_comunidad = $sql_nombre['lugar'];
                    $comunidad = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM comunidades WHERE id_comunidad = $id_comunidad"));
                ?>
                <tr>
                    <td><?php echo $sql_reporte['id_reporte']; ?></td>
                    <td><?php echo $sql_nombre['nombre']; ?></td>
                    <td><?php echo $comunidad['nombre']; ?></td>
                    <td><?php echo $sql_reporte['descripcion']; ?></td>
                    <td><?php echo $sql_reporte['fecha']; ?></td>
                    <td><a onclick="borrar_rep(<?php echo $sql_reporte['id_reporte']; ?>);" class="btn btn-floating red darken-1 waves-effect waves-light"><i class="material-icons">delete</i></a></td>
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
<br><br><br>
</div>
<br>
</body>
</main>
</html>