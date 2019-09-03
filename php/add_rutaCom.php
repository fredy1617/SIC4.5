<?php
  include ('../php/conexion.php');
  $IdComunidad = $conn->real_escape_string($_POST['valorIdComunidad']);
  date_default_timezone_set('America/Mexico_City');
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
        	if (mysqli_query($conn, "INSERT INTO tmp_reportes (id_reporte) VALUES ('$id_reporte')")) {
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
        	if (mysqli_query($conn, "INSERT INTO tmp_reportes (id_reporte) VALUES ('$id_reporte')")) {
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
  			if (mysqli_query($conn, "INSERT INTO tmp_pendientes (id_cliente, nombre, telefono, lugar, direccion, referencia, total, dejo, pagar, paquete, fecha_registro) VALUES ($id_cliente, '$nombre', '$telefono', '$lugar', '$direccion', '$referencia', $total, $dejo, $pagar, $paquete, '$fecha')")) {
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
<script>
	function ir(){
		setTimeout("location.href='../views/ruta_comunidad.php'", 1500);
	}
</script>
<?php
 echo "<script>ir();</script>";

?>