<!DOCTYPE html>
<html>
<head>
	<title>SIC | Clientes</title>
<?php 
include('fredyNav.php');
#___________CHECK DE PROMESAS DE PAGO VENCIDAS______________
include('../php/conexion.php');
date_default_timezone_set('America/Mexico_City');
$Hoy = date('Y-m-d');
$sql = mysqli_query($conn, "SELECT * FROM deudas WHERE hasta is not null");

if (mysqli_num_rows($sql)>0) {
	while ($deuda = mysqli_fetch_array($sql)) {
		$Id_deuda = $deuda['id_deuda'];
		$IdCliente = $deuda['id_cliente'];
		$fecha_corte = mysqli_fetch_array(mysqli_query($conn, 'SELECT * FROM clientes WHERE id_cliente='.$IdCliente));
		$Fecha = $fecha_corte['fecha_corte'];
		$nuevafecha = strtotime('-1 month', strtotime($Fecha));
		$FechaCorte = date('Y-m-05', $nuevafecha);
		$Pago = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM pagos WHERE id_deuda= $Id_deuda"));
		$Id_Pago = $Pago['id_pago'];
		if ($deuda['hasta'] <= $Hoy AND $deuda['tipo'] = 'Mensualidad') {
			#  CREA EL REPORTE.....
			mysqli_query($conn,"INSERT INTO reportes (id_cliente, descripcion, fecha) VALUES ($IdCliente, 'Cortar servicio, INCUMPLIO EN SU PROMESA DE PAGO.', '$Hoy')");
			#  BORRA EL PAGO.......
			if (mysqli_query($conn, "DELETE FROM pagos WHERE id_pago = '$Id_Pago'")) {	
				#  BORRAR LA DEUDA......
				mysqli_query($conn, "DELETE FROM deudas WHERE id_deuda = '$Id_deuda'");	
				#  RETRASAR LA FECHA DE CORTE
				mysqli_query($conn, "UPDATE clientes SET fecha_corte='$FechaCorte' WHERE id_cliente='$IdCliente'");	
			}			
		}
	}
}
#__________________________________________________________
#__________________________________________________________
#_________CHECAR SI DESPUES DE AGREGAR A TMP HAYA PASADO MAS DE 1 HORA Y LA RUTA ESTE EN 0___________
$Hora_1 = strtotime('-1 hour', strtotime(date('H:i:s')));
$Hora_1 = date('H:i:s', $Hora_1);

$sql_tmp_rep = mysqli_query($conn, "SELECT * FROM tmp_reportes WHERE ruta = 0 AND hora <= '$Hora_1' AND hora > '2020-01-01'");
if (mysqli_num_rows($sql_tmp_rep)>0) {
	while ($reporte = mysqli_fetch_array($sql_tmp_rep)) {
		$IdReporte = $reporte['id_reporte'];
		mysqli_query($conn, "DELETE FROM `tmp_reportes` WHERE `tmp_reportes`.`id_reporte` = $IdReporte");
	}
}

$sql_tmp_inst = mysqli_query($conn, "SELECT * FROM tmp_pendientes WHERE ruta_inst = 0 AND hora <= '$Hora_1' AND hora > '2020-01-01'");
if (mysqli_num_rows($sql_tmp_rep)>0) {
	while ($instalacion = mysqli_fetch_array($sql_tmp_inst)) {
		$IdCliente = $instalacion['id_cliente'];
		mysqli_query($conn, "DELETE FROM `tmp_pendientes` WHERE `tmp_pendientes`.`id_cliente` = $IdCliente");
	}
}
?>
<script >
	function buscar() {
    var texto = $("input#busqueda").val();
	$.post("../php/valida.php", {
          texto: texto,
        }, function(mensaje) {
            $("#datos").html(mensaje);
        }); 
	};
	function selCliente(id_cliente){
	$.post("../views/modal_pagos.php", {
          valorIdCliente: id_cliente,
        }, function(mensaje) {
            $("#Continuar").html(mensaje);
        });
	};
</script>
</head>
<body onload="buscar();">
	<div class="container">
		<div class="row">
			<br><br>
			<div id="Continuar"></div>
			<h3 class="hide-on-med-and-down col s12 m5 l5">Clientes:</h3>
      		<h5 class="hide-on-large-only col s12 m5 l5">Clientes:</h5>
      		<form class="col s12 m7 l7">
		      <div class="row">
		        <div class="input-field col s12">
		          <i class="material-icons prefix">search</i>
		          <input id="busqueda" name="busqueda" type="text" class="validate" onkeyup="buscar();">
		          <label for="busqueda">Buscar(C-Comunidad, #Cliente, Nombre, Ip*172.128.145.234)</label>
		        </div>
		      </div>
		    </form>
		</div>
		<div class="row">
			<table class="bordered highlight centered responsive-table">
			    <thead>
			      <tr>
			      	<th># Cliente</th>
			        <th>Nombre</th>
			        <th>Servicio</th>
			        <th>Comunidad</th>
			        <th>Pago</th>
			        <th>Reporte</th>
			        <th>Credito</th>
			      </tr>
			    </thead>
			    <tbody id="datos">
				</tbody>
			</table>
		</div>
	</div>
</body>
</html>