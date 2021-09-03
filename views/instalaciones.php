<!DOCTYPE html>
<html lang="en">
<head>
<?php
  include('fredyNav.php');
  include('../php/conexion.php');
  $user_id = $_SESSION['user_id'];
  $area = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM users WHERE user_id=$user_id"));
?>
<title>SIC | Instalaciones Pendientes</title>
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
  function verificar_eliminar(id_cliente){      
    $.post("../php/verificar_eliminar.php", {
          valorIdCliente: id_cliente,
        }, function(mensaje) {
            $("#Continuar").html(mensaje);
        }); 
   };
  function ruta(id_cliente) {
      if (id_cliente == "") {
        M.toast({html:"Ocurrio un error al seleccionar el cliente.", classes: "rounded"});
      }else{
        $.post("../php/insert_tmp_pendientes.php", {
            valorIdCliente: id_cliente,
          }, function(mensaje) {
              $("#instalaciones_ruta").html(mensaje);
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
</head>
<main>
<body>
	<div class="container">            
    <div id="Continuar"></div>
    <div id="reporte_borrar"></div>
    <div id="cliente_borrado"></div>
            <div class="row" >
              <h3 class="hide-on-med-and-down">Instalaciones Pendientes</h3>
              <h5 class="hide-on-large-only">Instalaciones Pendientes</h5>

              <a class="waves-effect waves-light btn pink right" href="../views/ruta_comunidad.php">Por Comunidad<i class="material-icons left">location_city</i></a>
            </div>
            <table class="bordered highlight responsive-table">
                <thead>
                    <tr>
                        <th>No. Cliente</th>
                        <th>Nombre</th>
                        <th>Servicio</th>
                        <th>Telefono</th>
                        <th>Lugar</th>
                        <th>Fecha Registro</th>
    					          <th>Registro</th>
                        <?php if($area['area'] != "Cobrador"){ ?>
                        <th>Alta</th>
                        <?php } ?>
    					          <th>Abono</th>
                        <?php if($area['area'] != "Cobrador"){ ?>
                        <th>Agregar</th>
                        <th>Borrar</th>
                        <?php } ?>
                    </tr>
                </thead>
                <tbody>
        	<?php 
        		$sql_pendientes = mysqli_query($conn,"SELECT * FROM clientes WHERE instalacion is NULL ORDER BY id_cliente ASC");
        		while($pendientes = mysqli_fetch_array($sql_pendientes)){
              $id_comunidad = $pendientes['lugar'];
              if ($id_comunidad == "") {
                $comuni= "CONOCIDA";
              }else{
                $sql_comunidad = mysqli_fetch_array(mysqli_query($conn,"SELECT * FROM comunidades WHERE id_comunidad=$id_comunidad"));
                $comuni=$sql_comunidad['nombre'].', '.$sql_comunidad['municipio'];
              }
        			?>
                    <tr>
                        <td><?php echo $pendientes['id_cliente'];?></td>
                        <td><?php echo $pendientes['nombre'];?></td>
                        <td><b><?php echo $pendientes['servicio'];?></b></td>
                        <td><?php echo $pendientes['telefono'];?></td>
                        <td><?php echo $comuni;?></td>
                        <td><?php echo $pendientes['fecha_registro'];?></td>
    					<td><?php echo $pendientes['registro'];?></td>
                        <?php if($area['area'] != "Cobrador"){ ?>                        
                        <td><form method="post" action="../views/alta_instalacion.php"><input type="hidden" name="id_cliente" value="<?php echo $pendientes['id_cliente'];?>"><button button type="submit" class="btn btn-floating pink waves-effect waves-light"><i class="material-icons">done</i></button></form></td>
                        <?php } ?>
                        <td><form method="post" action="../views/abonar_instalacion.php"><input type="hidden" name="id_cliente" value="<?php echo $pendientes['id_cliente'];?>"><button button type="submit" class="btn btn-floating indigo darken-1 waves-effect waves-light"><i class="material-icons">attach_money</i></button></form></td>
                        <?php if($area['area'] != "Cobrador"){ ?>
                        <td><a onclick="ruta(<?php echo $pendientes['id_cliente'];?>);" class="btn btn-floating pink waves-effect waves-light"><i class="material-icons">add</i></a></td>
                        <td><a onclick="verificar_eliminar(<?php echo $pendientes['id_cliente']; ?>)" class="btn btn-floating red darken-1 waves-effect waves-light"><i class="material-icons">delete</i></a></td>
                        <?php } ?>
                    </tr>
                    <?php
        		}
        	?>
                </tbody>
            </table>
            <br><br><br>
  <?php if($area['area'] != "Cobrador"){ ?>
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
  <?php } ?>
</div><br><br><br>
</body>
</main>
</html>