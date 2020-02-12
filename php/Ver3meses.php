<!DOCTYPE html>
<html>
<head>
	<title>SIC | Revisión 3 meses</title>
<?php
include ('fredyNav.php');
include ('../php/cobrador.php');
date_default_timezone_set('America/Mexico_City');
$Hoy = date('Y-m-d');
$nuevafecha = strtotime('-3 month', strtotime($Hoy));
$FechaPaso = date('Y-m-05', $nuevafecha);
echo $FechaPaso;
echo $Hoy;
?>
</head>
<body>
	<div class="container"><br>
		<div class="row">
			<h3> Mas de 3 meses si servicio:</h3>
		</div>
		<table class="bordered  highlight responsive-table">
		<thead>
			<tr>
				<th>No.Cliente</th>
				<th>Nombre</th>
				<th>Fecha de Corte</th>
				<th>Utimo Pago</th>
				<th>Cantidad</th>
				<th>Ip</th>
				<th>Servidor</th>
			</tr>
		</thead>	
		<tbody>
		<?php

		$sql = mysqli_query($conn, "SELECT * FROM clientes  WHERE fecha_corte <= $FechaPaso");
		$filas = mysqli_num_rows($sql);
		if ($filas == 0) {
			# code..
		}else{
			while ($cliente = mysqli_fetch_array($sql)) {
				$id_comunidad = $cliente['lugar'];
				$comunidad = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM comunidades WHERE id_comunidad = '$id_comunidad'"));
				$id_servidor = $comunidad['servidor'];
				$Servidor = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM servidores WHERE id_servidor = '$id_servidor'"));
				$id_cliente = $cliente['id_cliente'];
				$pago = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM pagos WHERE id_cliente = '$id_cliente' AND tipo = 'Mensualidad' Order by  fecha DESC"));
		?>
			<tr>
				<td><?php echo $id_cliente; ?></td>
				<td><?php echo $cliente['nombre']; ?></td>
				<td><?php echo $cliente['fecha_corte']; ?></td>
				<td><?php echo $pago['descripcion']; ?></td>
				<td><?php echo $pago['cantidad']; ?></td>
				<td><?php echo $cliente['ip']; ?></td>
				<td><?php echo $Servidor['nombre']; ?></td>
			</tr>
		<?php
			}
		}	

mysqli_close($conn);
?>
		
		</tbody>
		</table><br><br>
	</div>
</body>
</html>