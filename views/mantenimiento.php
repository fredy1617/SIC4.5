<html>
<head>
	<title>SIC | Mantenimientos</title>
<?php 
include('fredyNav.php');
include('../php/conexion.php');
include ('../php/cobrador.php');
$user_id = $_SESSION['user_id'];
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
<div id="borrar_inst"></div>
<div id="reporte_borrar"></div>
<div class="container">
  <div class="row">
      <br><br>
      <h3 class="hide-on-med-and-down col s12 m6 l6">Mantenimiento: </h3>
      <h5 class="hide-on-large-only col s12 m6 l6">Mantenimiento: </h5>
        <div class="col s2 m1 l1"><br>
          <a class="waves-effect waves-light btn pink" href="../php/imprimir_reportes.php" target="blank"><i class="material-icons black-text Small">picture_as_pdf</i></a>
        </div><br>
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
          <tbody>
            <?php 

  date_default_timezone_set('America/Mexico_City');
  $Hoy = date('Y-m-d');
  $sql = "SELECT * FROM reportes  WHERE (atendido != 1 OR atendido IS NULL) AND id_cliente >= 10000 AND descripcion LIKE 'Mantenimiento:%' ORDER BY fecha";
    
  $especiales = '';
    
    $consulta = mysqli_query($conn, $sql);
    //Obtiene la cantidad de filas que hay en la consulta
    $filas = mysqli_num_rows($consulta);
    //Si no existe ninguna fila que sea igual a $consultaBusqueda, entonces mostramos el siguiente mensaje
    if ($filas <= 0) {
      echo '<script>M.toast({html:"No se encontraron reportes.", classes: "rounded"})</script>';
    } else {
    //La variable $resultado contiene el array que se genera en la consulta, así que obtenemos los datos y los mostramos en un bucle
    while($resultados = mysqli_fetch_array($consulta)) {
      $id_reporte = $resultados['id_reporte'];
      $id_cliente = $resultados['id_cliente'];
      $id_user=$resultados['registro'];
      if ($resultados['campo'] == 1) {
        $EnCampo = 'En Campo';
      }else{
        $EnCampo = '';
      }
      if ($resultados['apoyo'] != 0) {
        $id_apoyo = $resultados['apoyo'];
        $A = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM users WHERE user_id = $id_apoyo"));
        $Apoyo = ', Apoyo: '.$A['firstname'];
      }else{
        $Apoyo = '';
      }
      if ($id_user == 0) {
        $Usuario = "Sistema";
      }else{
        $users = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM users WHERE user_id=$id_user"));
        $Usuario = $users['firstname'];
      }
      $sql = mysqli_query($conn, "SELECT * FROM clientes WHERE id_cliente=$id_cliente");
      $filas = mysqli_num_rows($sql);
      if ($filas == 0) {
        $sql = mysqli_query($conn, "SELECT * FROM especiales WHERE id_cliente=$id_cliente");
      }
      $cliente = mysqli_fetch_array($sql);
      $id_comunidad = $cliente['lugar'];
      $comunidad = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM comunidades WHERE id_comunidad=$id_comunidad"));
      if($resultados['tecnico']==''){
        $tecnico1[0] = '';
        $tecnico1[1] = 'Sin tecnico';
      }else{
        $id_tecnico = $resultados['tecnico'];
        $tecnico1 = mysqli_fetch_array(mysqli_query($conn, "SELECT user_id, user_name FROM users WHERE user_id=$id_tecnico"));  
      }
      $Estatus2= 0;
      if ($resultados['fecha']<$Hoy) {
        $date1 = new DateTime($Hoy);
        $date2 = new DateTime($resultados['fecha']);
        //Le restamos a la fecha date1-date2
        $diff = $date1->diff($date2);
        $Estatus2= $diff->days;
      }
      $estatus=$Estatus2;
      if ($resultados['estatus']>$Estatus2) { $estatus = $resultados['estatus']; }
      $color = "green";
      if ($estatus== 1) { $color = "yellow darken-2";
      }elseif ($estatus == 2) { $color = "orange darken-4";
      }elseif ($estatus >= 3) { $color = "red accent-4"; }
      
      $especiales .= '
                  <tr>
                    <td><span class="new badge '.$color.'" data-badge-caption="">'.$estatus.'</span>'.$EnCampo.'</td>
                    <td><b>'.$id_reporte.'</b></td>
                    <td><a class="tooltipped" data-position="top" data-tooltip=" Telefono: '.$cliente['telefono'].'  Comunidad: '.$comunidad['nombre'].'">'.$cliente['nombre'].'</a></td>
                    <td>'.$resultados['descripcion'].'</td>
                    <td>'.$resultados['fecha'].'</td>
                    <td>'.$comunidad['nombre'].', '.$comunidad['municipio'].'</td>
                    <td>'.$tecnico1[1].$Apoyo.'</td>
                    <td>'.$Usuario.'</td>
                    <td><br><form action="atender_reporte.php" method="post"><input type="hidden" name="id_reporte" value="'.$id_reporte.'"><button type="submit" class="btn-floating btn-tiny waves-effect waves-light pink"><i class="material-icons">send</i></button></form></td>
                    <td><a onclick="ruta('.$id_reporte.');" class="btn btn-floating pink waves-effect waves-light"><i class="material-icons">add</i></a></td>
                    <td><br><form action="editar_reporte.php" method="post"><input type="hidden" name="id_reporte" value="'.$id_reporte.'"><button type="submit" class="btn-floating btn-tiny waves-effect waves-light pink"><i class="material-icons">edit</i></button></form></td>
                  </tr>';   
    }//Fin while $resultados
  } //Fin else $filas
echo $especiales;
    ?>
              
          </tbody>
        </table>
    </div></p>
  </div>
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
<br><br><br>
</div>
<br>
</body>
</main>
</html>