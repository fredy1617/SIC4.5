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
<body>
<div id="borrar_inst"></div>
<div id="reporte_borrar"></div>
<div class="container">
  <h2 class="hide-on-med-and-down">Reportes Pendientes</h2>
  <h4 class="hide-on-large-only">Reportes Pendientes</h4>
  <p><div id="resultado_reporte_pendiente">
  <table class="bordered  highlight responsive-table">
    <thead>
      <tr>
          <th>Estatus</th>
          <th>No.Reporte </th>
          <th>Cliente</th>
          <th>Descripción</th>
          <th>Fecha</th>
          <th>Comunidad</th>
          <th>Técnico</th>
          <th>Atender</th>
          <th>+Ruta</th>
          <th>Editar</th>
      </tr>
    </thead>
    <tbody>
    <?php    
    date_default_timezone_set('America/Mexico_City');
    $Hoy = date('Y-m-d');
    $sql = "SELECT * FROM reportes  WHERE (fecha_visita = '$Hoy'  AND atender_visita = 0) OR (fecha_visita < '$Hoy' AND atender_visita = 0 AND visita = 1) OR atendido != 1 OR atendido IS NULL ";
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
      $sql = mysqli_query($conn, "SELECT * FROM clientes WHERE id_cliente=$id_cliente");
      $filas = mysqli_num_rows($sql);
      if ($filas == 0) {
        $sql = mysqli_query($conn, "SELECT * FROM especiales WHERE id_cliente=$id_cliente");
      }
      $cliente = mysqli_fetch_array($sql);
      $id_comunidad = $cliente['lugar'];
      $comunidad = mysqli_fetch_array(mysqli_query($conn, "SELECT nombre FROM comunidades WHERE id_comunidad=$id_comunidad"));
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
      if ($resultados['visita']==1) {
        $color = "green";
        $estatus = 0;
        if ($resultados['fecha_visita']<$Hoy) {
          $color = "red accent-4";
          $estatus = "YA!";
          $Tecnico = $resultados['tecnico'];
          $nombreTecnico  = mysqli_fetch_array(mysqli_query($conn,"SELECT * FROM users WHERE user_id = '$Tecnico'"));
          $Nombre = $nombreTecnico['firstname'];
          
          mysqli_query($conn,"UPDATE reportes SET descripcion = 'RETRASO DE VISITA NO ATENDIO: ".$Nombre." VISTAR URGENTEMENTE!'  WHERE id_reporte = $id_reporte");
            $resultados = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM reportes WHERE id_reporte=$id_reporte"));  
        }
      }
      ?>
                  <tr>
                    <td><span class="new badge <?php echo $color; ?>" data-badge-caption=""><?php echo $estatus;?></span></td>
                    <td><b><?php echo $id_reporte;?></b></td>
                    <td><a class="tooltipped" data-position="top" data-tooltip="<?php echo 'Telefono: '. $cliente['telefono']; echo '  Comunidad: '.$comunidad['nombre'];?>"><?php echo $cliente['nombre'];?></a></td>
                    <td><?php echo $resultados['descripcion'];?></td>
                    <td><?php echo $resultados['fecha'];?></td>
                    <td><?php echo $comunidad['nombre'];?></td>
                    <td><?php echo $tecnico1[1];?></td>
                    <td><br><form action="atender_reporte.php" method="post"><input type="hidden" name="id_reporte" value="<?php echo $id_reporte;?>"><button type="submit" class="btn-floating btn-tiny waves-effect waves-light pink"><i class="material-icons">send</i></button></form></td>
                    <td><a onclick="ruta(<?php echo $id_reporte;?>);" class="btn btn-floating pink waves-effect waves-light"><i class="material-icons">add</i></a></td>
                    <td><br><form action="editar_reporte.php" method="post"><input type="hidden" name="id_reporte" value="<?php echo $id_reporte;?>"><button type="submit" class="btn-floating btn-tiny waves-effect waves-light pink"><i class="material-icons">edit</i></button></form></td>
                  </tr>  
<?php          
    }//Fin while $resultados
  } //Fin else $filas
    ?>
                </tbody>
            </table>
  </div></p>
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
                ?>
                    <tr>
                      <td><?php echo $tmp['id_cliente']; ?></td>
                      <td><?php echo $tmp['nombre']; ?></td>
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
      <div class="row">
      <h3 class="hide-on-med-and-down">Ruta Reportes</h3>
      <h5 class="hide-on-large-only">Ruta Reportes</h5>
      <div id="resultado_ruta_reporte">
        <table class="bordered highlight responsive-table">
                <thead>
                    <tr>
                        <th>Reporte No.</th>
                        <th>Cliente</th>
                        <th>Descripción</th>
                        <th>Fecha</th>
                        <th>Borrar</th>
                    </tr>
                </thead>
                <tbody>
                <?php 
                $sql_tmp = mysqli_query($conn,"SELECT * FROM tmp_reportes WHERE ruta = 0");
                $columnas = mysqli_num_rows($sql_tmp);
                if($columnas == 0){
                    ?>
                    <h5 class="center">No hay reportes en ruta</h5>
                    <?php
                }else{
                    while($tmp = mysqli_fetch_array($sql_tmp)){
                        $id_reporte = $tmp['id_reporte'];
                        $sql_reporte = mysqli_fetch_array(mysqli_query($conn,"SELECT * FROM reportes WHERE id_reporte = '$id_reporte'"));

                        $id_cliente = $sql_reporte['id_cliente'];
                        $sql_nombre = mysqli_fetch_array(mysqli_query($conn,"SELECT * FROM clientes WHERE id_cliente = '$id_cliente'"));
                ?>
                    <tr>
                      <td><?php echo $sql_reporte['id_reporte']; ?></td>
                      <td><?php echo $sql_nombre['nombre']; ?></td>
                      <td><?php echo $sql_reporte['descripcion']; ?></td>
                      <td><?php echo $sql_reporte['fecha']; ?></td>
                      <td><a onclick="borrar_rep(<?php echo $sql_reporte['id_reporte']; ?>);" class="btn btn-floating red darken-1 waves-effect waves-light"><i class="material-icons">delete</i></a></td>
                    </tr>
                <?php
                    }
                }
                mysqli_close($conn);
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
