<?php
  include ('../php/conexion.php');
  $IdComunidad = $conn->real_escape_string($_POST['valorIdComunidad']);
  date_default_timezone_set('America/Mexico_City');
  include('is_logged.php');
  $id_user = $_SESSION['user_id'];
  $Hoy = date('Y-m-d');

  #CHECAMOS SI HAY REPORTES EN ESTA COMUNIDAD
  $reportes = mysqli_query($conn, "SELECT * FROM clientes INNER JOIN reportes ON clientes.id_cliente = reportes.id_cliente WHERE ((reportes.fecha_visita = '$Hoy' AND reportes.atender_visita = 0) OR (reportes.fecha_visita < '$Hoy' AND reportes.atender_visita = 0 AND reportes.visita = 1) OR reportes.atendido != 1 OR reportes.atendido IS NULL) AND clientes.lugar = '$IdComunidad' ORDER BY reportes.fecha");
  $SiRep = mysqli_num_rows($reportes);
  $Rep = 0;
  if ($SiRep > 0) {
  	#SI HAY REPORTES LOS AGREGAMOS A tmp_reportes
  	while ($Reporte = mysqli_fetch_array($reportes)) { 
        $id_reporte=$Reporte['id_reporte'];
        if (mysqli_num_rows(mysqli_query($conn, "SELECT * FROM tmp_reportes WHERE id_reporte = '$id_reporte'"))>0) {
        	echo "<script>M.toast({html: 'Ya se encuentra este reporte en ruta.', classes: 'rounded'});</script>";
        }else{
        	if (mysqli_query($conn, "INSERT INTO tmp_reportes (id_reporte, usuario) VALUES ('$id_reporte', '$id_user')")) {
        		$Rep ++;
        	}
        }
    }
  }

  #CHECAMOS SI HAY REPORTES ESPECIALES EN ESTA COMUNIDAD
  $reportesEsp = mysqli_query($conn, "SELECT * FROM especiales INNER JOIN reportes ON especiales.id_cliente = reportes.id_cliente WHERE ((reportes.fecha_visita = '$Hoy' AND reportes.atender_visita = 0) OR (reportes.fecha_visita < '$Hoy' AND reportes.atender_visita = 0 AND reportes.visita = 1) OR reportes.atendido != 1 OR reportes.atendido IS NULL) AND especiales.lugar = '$IdComunidad'");
  $SiEsp = mysqli_num_rows($reportesEsp);
  if ($SiEsp > 0) {
  	#SI HAT REPORTES ESPECIALES SE AREGAN A tmp_reportes
  	while ($Reporte1 = mysqli_fetch_array($reportesEsp)) { 
        $id_reporte=$Reporte1['id_reporte'];
        if (mysqli_num_rows(mysqli_query($conn, "SELECT * FROM tmp_reportes WHERE id_reporte = '$id_reporte'"))>0) {
        	echo "<script>M.toast({html: 'Ya se encuentra este reporte en ruta.', classes: 'rounded'});</script>";
        }else{
          if (mysqli_query($conn, "INSERT INTO tmp_reportes (id_reporte, usuario) VALUES ('$id_reporte', '$id_user')")) {
        		$Rep ++;
        	}
        }
    }
  }
  $mensaje = '';
  $Total = $SiRep+$SiEsp;
  if ($Total > 0) {
  	$mensaje.= 'Reportes: '.$Rep.'/'.$Total.' ,  ';
  }

  #CHECAMOS SI HAY INSTALACIONES PENDIENTES EN LA COMUNIDAD.
  $instalaciones = mysqli_query($conn, "SELECT * FROM clientes WHERE instalacion IS NULL AND lugar = '$IdComunidad' ORDER BY id_cliente ASC");
  $SiInst = mysqli_num_rows($instalaciones);
  $inst = 0;
  if ($SiInst > 0) {
  	#SI HAY INSTALIACIONES PENDIENTES AGREGAMOS A tmp_pendientes

  	while ($instalacion = mysqli_fetch_array($instalaciones)) {
  		$id_cliente = $instalacion['id_cliente'];
  		$sql_chequeo = mysqli_query($conn, "SELECT * FROM tmp_pendientes WHERE id_cliente = $id_cliente");
      $numero_columnas = mysqli_num_rows($sql_chequeo);
      if($numero_columnas==0){

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
  			if (mysqli_query($conn, "INSERT INTO tmp_pendientes (id_cliente, nombre, telefono, lugar, direccion, referencia, total, dejo, pagar, paquete, fecha_registro, usuario) VALUES ($id_cliente, '$nombre', '$telefono', '$lugar', '$direccion', '$referencia', $total, $dejo, $pagar, $paquete, '$fecha', '$id_user')")) {
  				$inst ++;
  			}
  		  
      }else{
        echo '<script>M.toast({html: "Ya se encuentra esta instalacion en ruta.", classes: "rounded"});</script>';
      }
  	}
  $mensaje.= 'Instalaciones: '.$inst.'/'.$SiInst;
  }

echo '<script>M.toast({html:"'.$mensaje.'", classes: "rounded"});</script>';
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
              $sql_tmp = mysqli_query($conn, "SELECT * FROM tmp_reportes WHERE ruta = 0 AND usuario = $id_user");
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