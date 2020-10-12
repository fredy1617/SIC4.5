<?php 
include('../php/conexion.php');
$id_ruta = $conn->real_escape_string($_POST['valorRuta']);
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
			if (mysqli_query($conn,"INSERT INTO no_realizados (id_trabajo, tipo) VALUES($id_reporte, 'REPORTE')")) {
				#SI SE AGREGA, ELIMINAMOS EL REPORTE DE LA RUTA PARA tmp_reportes Y ASI PUEDAN SER AGREGADOS A OTRAS RUTAS
				mysqli_query($conn, "DELETE FROM tmp_reportes  WHERE id_reporte = $id_reporte");
			}
		}//FIN WHILE
	}//FIN IF REPORTES
	#BUSCAMOS INSTALACIONES QUE NO HAYAN SIDO REALIZADAS AUN
	$sql_tmp2 = mysqli_query($conn,"SELECT * FROM tmp_pendientes INNER JOIN clientes ON tmp_pendientes.id_cliente = clientes.id_cliente  WHERE tmp_pendientes.ruta_inst = $id_ruta AND clientes.instalacion IS NULL");
	if(mysqli_num_rows($sql_tmp2) > 0){
		#SI ENCUANTRA INSTALACIONES, RECORREMOS UNA POR UNA
		while($tmp = mysqli_fetch_array($sql_tmp2)){
			$id_cliente = $tmp['id_cliente'];//TOMAMOS EL ID DEL CLIENTE DEL LA INSTALACION EN TURNO
			#AGREGAMOS LA INSTALACION EN TURNO A LA TABLA DE no_realidados id, id_trabajo, 'INSTALACION'
			if (mysqli_query($conn,"INSERT INTO no_realizados (id_trabajo, tipo) VALUES($id_cliente, 'INSTALACION')")) {
				#SI SE AGREGA, ELIMINAMOS LA INSTALACION DE LA RUTA PARA tmp_pendientes Y ASI PUEDAN SER AGREGADAS A OTRAS RUTAS
				mysqli_query($conn, "DELETE FROM tmp_pendientes WHERE id_cliente = $id_cliente");
			}
		}//FIN WHILE
	}//FIN IF INSTALACIONES
	#UNA VEZ REALIZADAS TODAS LAS MODIFICACIONES MANDAMOS EL MENSAJE DE ALERTA
	echo '<script>M.toast({html:"La ruta se actualizado correctamente.", classes: "rounded"})</script>';
}else{
	#SI NO SE PUDO HACER LA MODIFICACION DEL ESTATUS MANDAMOS LA ALERTA
	echo '<script>M.toast({html:"Ocurrio un error y no se actualizo.", classes: "rounded"})</script>';
}
//echo mysqli_error($conn);
mysqli_close($conn);
?>
