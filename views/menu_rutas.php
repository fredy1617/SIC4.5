<!DOCTYPE html>
<html>
<head>
	<title>SIC | Rutas</title>
<?php
#INCLUIMOS EL ARCHIVO DONDE ESTA LA BARRA DE NAVEGACION DEL SISTEMA
include ('fredyNav.php');
#INCLUIMOS EL ARCHIVO CON LA CONEXION A LA BASE DE DATOS
include('../php/conexion.php');
#INCLUIMOS EL ARCHIVO EL CUAL HACE QUE SOLOS LOS USUARIOS QUE SEAN DIFERENTES A COBRADORES PUEDAN ACCEDER A ESTE ARCHIVO
include('../php/cobrador.php');
?>
</head>
<body>
	<div class="container">
		<div class="row">
			<h3 class="hide-on-med-and-down">Reporte de Rutas</h3>
  			<h5 class="hide-on-large-only">Reporte de Rutas</h5>
		<a class="waves-effect waves-green btn pink right" href="../views/rutas.php">Todas<i class="material-icons right">done</i></a>	
		</div>
		<?php
		#ES UN CICLO QUE SE RECORRERA 2 VECES CON LA I PRIMERO EN 0 Y LUEGO EN 1
		for ($i=0; $i <= 1; $i++) {
			$inicia=$i*3;//VARIABLE QUE SE USA PARA MARCAR DONDE INICIA EL REGNLO PRIMERO SERA EN 0 LUEGO INICIARA EN 3
			#SELECCIONAMOS LAS RUTAS DE 3 EN 3 EN ORDEN DECENDENTE ELIGIRA PRIMERO LA 0,1,2 LUEGO EN UNA SEGUNA VUELTA DEL CICLO ELEGIRA 3,4,5 SEGUN LA VARIABLE INICIAR Y EL VALOR ELEGIRA DE 3 EN 3
			$rutas = mysqli_query($conn, "SELECT * FROM rutas ORDER BY id_ruta DESC LIMIT $inicia,3");
			echo '<div class="row">';//FILAS 3 EN 3 RUTAS ------------------------------------------------------------------------------------
			#DEFINIMOS UNA ZONA HORARIA
			date_default_timezone_set('America/Mexico_City');
			#GENERAMOS UNA FECHA DEL DIA EN CURSO REFERENTE A LA ZONA HORARIA
			$Hoy = date('Y-m-d');
			#VERIFICAMOS SI ENCONTRO RUTAS EN LA SELECCION
			if (mysqli_num_rows($rutas) == 0) {
		       echo '<h5 class="center">No hay rutas</h5>';//SI NO ENCUENTRA RUTAS MOSTRAR EL MENSAJE               
		    }else{	
		    	#SI ENCUENTA RUTAS LAS RECORREMOS UNA POR UNA CON EL WHILE
				while($ruta = mysqli_fetch_array($rutas)) {
					$id_ruta = $ruta['id_ruta'];//TOMAMOS EL ID DE LA RUTA EN TURNO
					$fecha_ruta = $ruta['fecha'];//TOMAMOS LA FECHA DE LA RUTA EN TURNO
					$hora_ruta = $ruta['hora'];//TOMAMOS LA HORA DE LA RUTA EN TURNO
					#CONTAMOS TODAS LAS INSTALACIONES ASIGNADAS A ESTA RUTA EN TURNO
					$instalaciones = mysqli_fetch_array(mysqli_query($conn,"SELECT count(*) FROM tmp_pendientes WHERE ruta_inst = $id_ruta"));
					#CONTAMOS TODOS LOS REPORTES INCLUYE ORDENES Y MANTENIMIENTOS ASIGNADOS A ESTA RUTA EN TURNO
					$reportes = mysqli_fetch_array(mysqli_query($conn,"SELECT count(*) FROM tmp_reportes WHERE ruta = $id_ruta"));
					#SUMAMOS LOS CONNTADORES DE LOS REPORTES E INSTALACIONES PARA SABER QUE ES EL TOTAL DE LA CARGA QUE TIENE LA RUTA
					$Total =$instalaciones['count(*)']+$reportes['count(*)'];
					#CONTAMOS TODAS LAS INSTALACIONES TERMINADAS ASIGNADAS A ESTA RUTA EN TURNO
					$instalacion = mysqli_fetch_array(mysqli_query($conn,"SELECT count(*) FROM tmp_pendientes AS Pen JOIN clientes AS Cli ON Pen.id_cliente = Cli.id_cliente WHERE ruta_inst = $id_ruta AND instalacion = 1"));
					#CONTAMOS TODOS LOS REPORTES TERMINADOS INCLUYE MANTENIMIENTOS ASIGNADOS A ESTA RUTA EN TURNO
					$reporte = mysqli_fetch_array(mysqli_query($conn,"SELECT count(*) FROM tmp_reportes  AS TRep JOIN reportes AS Rep ON TRep.id_reporte = Rep.id_reporte WHERE ruta = $id_ruta AND atendido = 1 OR atendido !=  NULL"));
					#CONTAMOS TODAS LAS ORDENES TERMINADAS ASIGNADOS A ESTA RUTA EN TURNO
					$orden = mysqli_fetch_array(mysqli_query($conn,"SELECT count(*) FROM tmp_reportes  AS TRep JOIN orden_servicios AS Orden ON TRep.id_reporte = Orden.id WHERE ruta = $id_ruta AND ((fecha_r > '$fecha_ruta'  AND  fecha_s IS NULL)  OR (fecha_r = '$fecha_ruta' AND hora_r > '$hora_ruta' AND  fecha_s IS NULL)  OR (fecha_r <= '$fecha_ruta' AND fecha_s > '$fecha_ruta') OR (fecha_r <= '$fecha_ruta' AND fecha_s = '$fecha_ruta' AND hora_s > '$hora_ruta' ))"));

					#SUMAMOS LOS CONTADORES DE REPORTES, ORDENES E INSTALACIONES TERMINADAS PARA SABER QUE AVANCE LLEVA DEL TOTAL
					$Avance = $instalacion['count(*)']+$reporte['count(*)']+$orden['count(*)'];
					#SUMAMOS DOS DIA A LA FECHA DE LA RUTA
					$mas_dias = strtotime('+1 day', strtotime($fecha_ruta));
					$dosdias = date('Y-m-d', $mas_dias);//SE ASIGNA UNA VARIABLE LA SUMA DE LOS DOS DIAS
					#IF QUE COMPARA SI EL LA CARGA YA ESTA TERMINADO (TODOS LOS REPORTES E UNSTALACIONES QUE LLEVAVA LA RUTA 10/10), O SI YA PASARON 2 DIAS DESDE EL DIA DE CREACION DE LA RUTA
					if (($Total == $Avance OR $Hoy > $dosdias) AND $ruta['estatus'] != 1) {
						#SI LA RUTA EN TURNO TIENE LA CARGA TERMINADA O PASARON LOS 2 DIAS PASAMOS SU ESTAUS PENDIENTE = 0 A TERMINADA = 1
						#VERIFICAMOS SI SE HIZO EL CAMBIO A LA RUTA EN EL ESTATUS = 1 PARA SEÑALAR QUE ESTA TERMINADA
						if(mysqli_query($conn, "UPDATE rutas SET estatus = 1  WHERE id_ruta = $id_ruta")){
							#BUSCAMOS REPORTES, ORDENES O MANTENIMIENTOS QUE NO HAYAN SIDO ATENDIDOS
							$sql_tmp1 = mysqli_query($conn,"SELECT * FROM tmp_reportes INNER JOIN reportes ON tmp_reportes.id_reporte = reportes.id_reporte WHERE tmp_reportes.ruta = $id_ruta AND (reportes.atendido != 1 OR reportes.atendido IS NULL)");
							#VERIFICAMOS QUE HAYA ALMENOS UNO
						    if(mysqli_num_rows($sql_tmp1) > 0){
						    	# SI ENCUENTRA, RECORREMOS CADA UNO DE LOS REPORTES
								 while($tmp = mysqli_fetch_array($sql_tmp1)){
								 	$id_reporte = $tmp['id_reporte'];//TOMAMOS EL ID DEL REPORTE EN TURNO
								 	#AGREGAMOS EL REPORTE EN TURNO A LA TABLA DE no_realizados id,id_trabjo, 'REPORTE'
								 	if (mysqli_query($conn,"INSERT INTO no_realizados (id_trabajo, tipo, id_ruta) VALUES($id_reporte, 'REPORTE', $id_ruta)")) {
								 		#SI SE AGREGA, ELIMINAMOS EL REPORTE DE LA RUTA PARA tmp_reportes Y ASI PUEDAN SER AGREGADOS A OTRAS RUTAS
						            	mysqli_query($conn, "DELETE FROM tmp_reportes  WHERE id_reporte = $id_reporte");
								 	}
						        }//FIN WHILE
							}//FIN IF REPORTES
							#BUSCAMOS ORDENES QUE NO HAYAN SIDO ATENDIDOS
							$sql_tmp2 = mysqli_query($conn,"SELECT * FROM tmp_reportes INNER JOIN orden_servicios ON tmp_reportes.id_reporte = orden_servicios.id WHERE ruta = $id_ruta AND ((fecha_r IS NULL AND  fecha_s IS NULL)  OR (fecha_r < '$fecha_ruta'  AND  fecha_s IS NULL)  OR (fecha_r = '$fecha_ruta' AND hora_r < '$hora_ruta' AND  fecha_s IS NULL)  OR (fecha_r <= '$fecha_ruta' AND fecha_s < '$fecha_ruta') OR (fecha_r <= '$fecha_ruta' AND fecha_s = '$fecha_ruta' AND hora_s < '$hora_ruta' ))");
							#VERIFICAMOS QUE HAYA ALMENOS UNO
							if(mysqli_num_rows($sql_tmp2) > 0){
								# SI ENCUENTRA, RECORREMOS CADA UNO DE LOS REPORTES
								while($tmp = mysqli_fetch_array($sql_tmp2)){
									$id = $tmp['id'];//TOMAMOS EL ID DEL REPORTE EN TURNO
									#AGREGAMOS EL REPORTE EN TURNO A LA TABLA DE no_realizados id,id_trabjo, 'REPORTE'
									if (mysqli_query($conn,"INSERT INTO no_realizados (id_trabajo, tipo, id_ruta) VALUES($id, 'REPORTE', $id_ruta)")) {
										#SI SE AGREGA, ELIMINAMOS EL REPORTE DE LA RUTA PARA tmp_reportes Y ASI PUEDAN SER AGREGADOS A OTRAS RUTAS
										mysqli_query($conn, "DELETE FROM tmp_reportes  WHERE id_reporte = $id");
									}
								}//FIN WHILE
							}//FIN IF ORDENES
							#BUSCAMOS INSTALACIONES QUE NO HAYAN SIDO REALIZADAS AUN
							$sql_tmp2 = mysqli_query($conn,"SELECT * FROM tmp_pendientes INNER JOIN clientes ON tmp_pendientes.id_cliente = clientes.id_cliente  WHERE tmp_pendientes.ruta_inst = $id_ruta AND clientes.instalacion IS NULL");
						    if(mysqli_num_rows($sql_tmp2) > 0){
						    	#SI ENCUANTRA INSTALACIONES, RECORREMOS UNA POR UNA
								 while($tmp = mysqli_fetch_array($sql_tmp2)){
								 	$id_cliente = $tmp['id_cliente'];//TOMAMOS EL ID DEL CLIENTE DEL LA INSTALACION EN TURNO
								 	#AGREGAMOS LA INSTALACION EN TURNO A LA TABLA DE no_realidados id, id_trabajo, 'INSTALACION'
								 	if (mysqli_query($conn,"INSERT INTO no_realizados (id_trabajo, tipo, id_ruta) VALUES($id_cliente, 'INSTALACION', $id_ruta)")) {
								 		#SI SE AGREGA, ELIMINAMOS LA INSTALACION DE LA RUTA PARA tmp_pendientes Y ASI PUEDAN SER AGREGADAS A OTRAS RUTAS
						                mysqli_query($conn, "DELETE FROM tmp_pendientes WHERE id_cliente = $id_cliente");
								 	}
						        }//FIN WHILE
							}//FIN IF INSTALACIONES
						}//FIN IF VERIFICACION DE MODIFICACION estatus = 1
					}//FIN DE IF TERMINO DE CARGA O PASARON 2 DIAS
					$EstatusR = mysqli_fetch_array(mysqli_query($conn, "SELECT estatus FROM rutas WHERE id_ruta = $id_ruta"));
					if ($EstatusR ['estatus']==0) {
						$Estatus = '<h6 class="blue-text"><b>'."$Avance".' / '."$Total".'</b></h6>';
					}else{
						$Estatus = '<span class="new badge green" data-badge-caption="Terminado"></span>';
					}
					?>
					<div class="col s12 m4 l4">
					    <h5 class="header">Ruta No. <?php echo $id_ruta; ?></h5>
					    <div class="card horizontal">
					      <div class="card-image">
					        <img class="responsive-img" src="../img/som.png">
					      </div>
					      <div class="card-stacked">
					        <div class="card-content">
					          <p>
					          	<b>Estatus:</b><br>
					          	<?php echo $Estatus; ?>
					          	<b>Ing(s):</b>
					           	<ul>
					          		<li class="red-text darken-2"><?php echo $ruta['responsable']; ?></li>
					          		<li class="red-text darken-2"><?php echo $ruta['acompanante']; ?></li>
					          	</ul>
					          	<span class="new badge pink" data-badge-caption=""><?php echo $fecha_ruta; ?></span>
					          </p>
					        </div>
					        <div class="card-action row">
					        	<form method="post" action="../views/detalles_ruta.php" class="col s8">
					        		<input id="id_ruta" name="id_ruta" type="hidden" value="<?php echo $ruta['id_ruta']; ?>">
					        		<button class="waves-effect waves-light btn-small white orange-text"><b>Detalles</b></button>
					        	</form>
					        	<form action="editar_ruta.php" method="post" class="col s4">
					        		<input id="id_ruta" name="id_ruta" type="hidden" value="<?php echo $ruta['id_ruta']; ?>">
					        		<button type="submit" class="btn-floating btn-tiny btn-small waves-effect waves-light pink"><i class="material-icons">edit</i></button>
					        	</form>
					        </div>
					      </div>
					    </div>
					</div>
				<?php
				}//FIN DEL WHILE
			}//FIN DEL ELSE DE NO ESTA VACIAS LAS RUTAS
			echo '</div>';//FIN DE FILA DE 3 EN 3  ------------------------------------------------------------------------------------------------------------------------------
		}//FIN DEL CICLO FOR
		?>	
	</div>
</body>
</html>