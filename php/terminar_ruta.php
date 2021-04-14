<?php 
include('../php/conexion.php');
$id_ruta = $conn->real_escape_string($_POST['valorRuta']);
$ruta = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM rutas WHERE id_ruta = $id_ruta"));
$fecha_ruta = $ruta['fecha'];//TOMAMOS LA FECHA DE LA RUTA EN TURNO
$hora_ruta = $ruta['hora'];//TOMAMOS LA HORA DE LA RUTA EN TURNO
#VERIFICAMOS SI SE HIZO EL CAMBIO A LA RUTA EN EL ESTATUS = 1 PARA SEÑALAR QUE ESTA TERMINADA
if(mysqli_query($conn, "UPDATE rutas SET estatus = 1  WHERE id_ruta = $id_ruta")){
	#BUSCAMOS REPORTES O MANTENIMIENTOS QUE NO HAYAN SIDO ATENDIDOS
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
	$sql_tmp3 = mysqli_query($conn,"SELECT * FROM tmp_pendientes INNER JOIN clientes ON tmp_pendientes.id_cliente = clientes.id_cliente  WHERE tmp_pendientes.ruta_inst = $id_ruta AND clientes.instalacion IS NULL");
	if(mysqli_num_rows($sql_tmp3) > 0){
		#SI ENCUANTRA INSTALACIONES, RECORREMOS UNA POR UNA
		while($tmp = mysqli_fetch_array($sql_tmp3)){
			$id_cliente = $tmp['id_cliente'];//TOMAMOS EL ID DEL CLIENTE DEL LA INSTALACION EN TURNO
			#AGREGAMOS LA INSTALACION EN TURNO A LA TABLA DE no_realidados id, id_trabajo, 'INSTALACION'
			if (mysqli_query($conn,"INSERT INTO no_realizados (id_trabajo, tipo, id_ruta) VALUES($id_cliente, 'INSTALACION', $id_ruta)")) {
				#SI SE AGREGA, ELIMINAMOS LA INSTALACION DE LA RUTA PARA tmp_pendientes Y ASI PUEDAN SER AGREGADAS A OTRAS RUTAS
				mysqli_query($conn, "DELETE FROM tmp_pendientes WHERE id_cliente = $id_cliente");
			}
		}//FIN WHILE
	}//FIN IF INSTALACIONES
	#UNA VEZ REALIZADAS TODAS LAS MODIFICACIONES MANDAMOS EL MENSAJE DE ALERTA
	echo '<script>M.toast({html:"La ruta se actualizado correctamente.", classes: "rounded"})</script>';
	?>
	<script>
		setTimeout("location.href='../views/menu_rutas.php'", 800);
	</script>
	<?php
}else{
	#SI NO SE PUDO HACER LA MODIFICACION DEL ESTATUS MANDAMOS LA ALERTA
	echo '<script>M.toast({html:"Ocurrio un error y no se actualizo.", classes: "rounded"})</script>';
}
//echo mysqli_error($conn);
mysqli_close($conn);
?>
