<?php //FUNCION QUE INSERTA TODOS LOS REPORTES EN CAMPO E INSTALACIONES SOLAMENTE
#INCLUIMOS EL ARCHIVO CON LOS DATOS Y CONEXXION A LA BASE DE DATOS
include ("../php/conexion.php");
#DEFINIMOS UNA ZONA HORARIA
date_default_timezone_set('America/Mexico_City');
#GENERAMOS UNA FECHA DEL DIA EN CURSO REFERENTE A LA ZONA HORARIA
$Hoy = date('Y-m-d');
$Hora = date('H:i:s');

#RECIBIMOS EL LA VARIABLE valorIdComunidad CON EL METODO POST DEL DOCUMENTO ruta_comunidad.php PARA HACER LA INSERCION DE LOS REPORTES E INSTALACIONES
$IdComunidad = $conn->real_escape_string($_POST['valorIdComunidad']);

#INCLUIMOS EL PHP DONDE VIENE LA INFORMACION DEL INICIO DE SESSION
include('is_logged.php');
$id_user = $_SESSION['user_id'];//ID DEL USUARIO LOGEADO EN EL SISTEMA

#SELECCIONAMOS LOS REPORTES EN CAMPO PENDIENTES EN ESTA COMUNIDAD
$reportes = mysqli_query($conn, "SELECT id_reporte FROM clientes INNER JOIN reportes ON clientes.id_cliente = reportes.id_cliente WHERE ((reportes.fecha_visita = '$Hoy' AND reportes.atender_visita = 0) OR (reportes.fecha_visita < '$Hoy' AND reportes.atender_visita = 0 AND reportes.visita = 1) OR reportes.atendido != 1 OR reportes.atendido IS NULL) AND reportes.campo = 1 AND clientes.lugar = '$IdComunidad'  ORDER BY reportes.fecha");
#CONTABILIZAMOS EL TOTAL DE LOS REPORTES QUE SELECCIONAMOS
$SiRep = mysqli_num_rows($reportes);
$Rep = 0;//CONTADOR DE REPORTES AGREGADOS A tmp_reportes INICIADO EN 0
#VERIFICAMOS SI HAY ALGUN REPORTE
if ($SiRep > 0) {
  #SI HAY REPORTES LOS AGREGAMOS A tmp_reportes 
  #RECORREMOS EL ARREGLO DE LOS REPORTES UNO POR UNO
  while ($Reporte = mysqli_fetch_array($reportes)) { 
    $id_reporte=$Reporte['id_reporte'];//ID DEL REPORTE EN TURNO
    #VERIFICAMOS QUE ESTA ID DEL REPORTE NO ESTE  REGISTRADA EN tmp_reportes 
    if (mysqli_num_rows(mysqli_query($conn, "SELECT * FROM tmp_reportes WHERE id_reporte = '$id_reporte'"))>0) {
      #SI YA ESTA AGREGADA A UNA RUTA MOSTRAR ALERTA
      echo "<script>M.toast({html: 'Ya se encuentra este reporte en ruta.', classes: 'rounded'});</script>";
    }else{
      #SI NO ESTA AGREGADA LA ID DE A tmp_reportes  LA AGREGAMOS Y VERIFICAMOS
      if (mysqli_query($conn, "INSERT INTO tmp_reportes (id_reporte, usuario, hora) VALUES ('$id_reporte', '$id_user', '$Hora')")) {
        $Rep ++;//SI SE AGREGA CORRECATMENTE EL REPORTE A tmp_reportes INCREMENTAMOS EN 1 AL CONTADOR 
      }
    }
  }
  echo "<script>M.toast({html: 'Reportes Agregados: ".$Rep."/".$SiRep."', classes: 'rounded'});</script>";
}

#SELECCIONAMOS LAS INSTALACIONES PENDIENTES EN LA COMUNIDAD.
$instalaciones = mysqli_query($conn, "SELECT * FROM clientes WHERE instalacion IS NULL AND lugar = '$IdComunidad' ORDER BY id_cliente ASC");
#CONTABILIZAMOS EL TOTAL DE LAS INSTALACIONES QUE SELECCIONAMOS
$SiInst = mysqli_num_rows($instalaciones);
$inst = 0;//CONTADOR DE INSTALACIONES AGREGADAS A tmp_pendientes INICIADO EN 0
#VERIFICAMOS SI HAY ALGUNA INSTALACIONES
if ($SiInst > 0) {
  #SI HAY INSTALIACIONES PENDIENTES AGREGAMOS A tmp_pendientes
  #RECORREMOS LAS INSTALACIONES UNA POR UNA
  while ($instalacion = mysqli_fetch_array($instalaciones)) {
  	$id_cliente = $instalacion['id_cliente'];//ID DE LA INSTALACION(CLIENTE) EN TURNO
    #VERIFICAMOS QUE LA INSTALACION (CLIENTE) NO ESTE REGISTRADO EN tmp_pedientes
    if(mysqli_num_rows(mysqli_query($conn, "SELECT * FROM tmp_pendientes WHERE id_cliente = $id_cliente"))==0){
      #SI NO ESTA REGISTRADA PORCEDEMOS A AGREGARLA A tmp_pendientes
  		$nombre = $instalacion['nombre'];
  		$telefono = $instalacion['telefono'];
  		$lugar = $instalacion['lugar'];
  		$direccion = $instalacion['direccion'];
  		$referencia = $instalacion['referencia'];
  		$total = $instalacion['total'];
  		$dejo = $instalacion['dejo'];
  		$pagar = $total-$dejo;
  		$paquete = $instalacion['paquete'];
  		$fecha = $instalacion['fecha_registro'];
      #SE INSERTE Y SE VERIFICA SI ES AGREGADA CORRECTAMENTE
  		if (mysqli_query($conn, "INSERT INTO tmp_pendientes (id_cliente, nombre, telefono, lugar, direccion, referencia, total, dejo, pagar, paquete, fecha_registro, usuario, hora) VALUES ($id_cliente, '$nombre', '$telefono', '$lugar', '$direccion', '$referencia', $total, $dejo, $pagar, $paquete, '$fecha', '$id_user', '$Hora')")) {
  			$inst ++;//SI LA INSTALACION ES AGREGADA CORRECTAENTE A tmp_pendientes EL CONTADOR DE INCREMENTA EN 1
  		}  
    }else{
      #SI YA SE ENCUANTRA EL ID DE LA INSTALACION REGISTRADA MANDAR MSJ DE ALERTA
      echo '<script>M.toast({html: Ya se encuentra esta instalacion en ruta.", classes: "rounded"});</script>';
    }
  }
  echo '<script>M.toast({html:"Instalaciones Aregadas: '.$inst.'/'.$SiInst.'", classes: "rounded"});</script>';
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