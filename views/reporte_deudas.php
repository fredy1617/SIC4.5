<!DOCTYPE html>
<html>
<head>
	<title>SIC | Reportes de Deudas</title>
<?php
include ('fredyNav.php');
include('../php/superAdmin.php');
?>
</head>
<body>
	<div class="container">
		<div class="row">
			<h3 class="hide-on-med-and-down"> Reporte de Deudas</h3>
  			<h5 class="hide-on-large-only"> Reporte de Deudas</h5>
		</div>
		<table class="bordered highlight responsive-table" width="100%">
				<thead>
					<tr>
						<th>#</th>
						<th>Estatus</th>
						<th>Id. Cliente</th>
						<th>Nombre Cliente</th>
						<th>Comunidad</th>
						<th>Descripción</th>	
						<th>Cantidad</th>
						<th>Abono</th>
						<th>Resta</th>
						<th>Fecha</th>
						<th>Usuario</th>
						<th>Ver</th>
					</tr>
				</thead>
				<tbody>
				<?php
				$sql = mysqli_query($conn, "SELECT * FROM deudas WHERE liquidada = 0");
				$filas =  mysqli_num_rows($sql);
				if ($filas <= 0) {
					echo "<center><b><h3>No se encontraron deudas</h3></b></center>";
				}else{
				date_default_timezone_set('America/Mexico_City');
				$Fecha_Hoy = date('Y-m-d');
				$cont=0;
				$total = 0;
				while ( $resultados = mysqli_fetch_array($sql)) {
					$id_cliente = $resultados['id_cliente'];
					$deuda = mysqli_fetch_array(mysqli_query($conn, "SELECT SUM(cantidad) AS suma FROM deudas WHERE id_cliente = $id_cliente AND liquidada = 1"));
    				$abono = mysqli_fetch_array(mysqli_query($conn, "SELECT SUM(cantidad) AS suma FROM pagos WHERE id_cliente = $id_cliente AND tipo = 'Abono'"));
    				if ($deuda['suma'] == "") {
				      $deuda['suma'] = 0;
				    }
				    if ($abono['suma'] == "") {
				      $abono['suma'] = 0;
				    }
				    $poner = mysqli_fetch_array(mysqli_query($conn, "SELECT min(id_deuda) AS id FROM deudas WHERE id_cliente = $id_cliente AND liquidada = 0"));
    				$tiene = $abono['suma']-$deuda['suma'];
					$cosnulta = mysqli_query($conn,"SELECT * FROM clientes WHERE id_cliente=$id_cliente");
				    if (mysqli_num_rows($cosnulta)<=0) {
				      $cosnulta = mysqli_query($conn,"SELECT * FROM especiales WHERE id_cliente=$id_cliente");
				    } 
				    $cliente = mysqli_fetch_array($cosnulta);
					$id_comunidad = $cliente['lugar'];
					$Comunidad = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM comunidades WHERE id_comunidad = $id_comunidad"));	
					$id_usuario = $resultados['usuario'];
					$usuario = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM users WHERE user_id = $id_usuario"));
					$Mas_mes = strtotime('+1 month', strtotime($resultados['fecha_deuda']));
					$Mas_Mes = date('Y-m-d', $Mas_mes);
					$color = "green";
					$estatus = "";
					if ($Fecha_Hoy >= $Mas_Mes) {
						$color = "red accent-4";
						$estatus = "Cobrar";
					}
					$cont++;
					$cantidad = $resultados['cantidad'];
					if ($cantidad =='') {
						$cantidad= 0;
					}
					if ($poner['id'] == $resultados['id_deuda']) {
						$tiene = $tiene;
					}else{
						$tiene = 0;
					}
					$resta = $cantidad-$tiene;
				?>
					<tr>
						<td><?php echo $cont; ?></td>
						<td><span class="new badge <?php echo$color; ?>" data-badge-caption=""><?php echo $estatus; ?></span></td>
						<td><?php echo $id_cliente; ?></td>
						<td><?php echo $cliente['nombre']; ?></td>
						<td><?php echo $Comunidad['nombre']; ?></td>		
						<td><?php echo $resultados['descripcion']; ?></td>			
						<td>$<?php echo $cantidad; ?></td>						
						<td>$<?php echo $tiene; ?></td>
						<td>$<?php echo $resta; ?></td>
						<td><?php echo $resultados['fecha_deuda']; ?></td>
						<td><?php echo $usuario['firstname']; ?></td>
					<td><form method="post" action="../views/credito.php"><input id="no_cliente" name="no_cliente" type="hidden" value="<?php echo $id_cliente; ?>"><button class="btn-floating btn-tiny waves-effect waves-light pink"><i class="material-icons">send</i></button></form></td>
					</tr>
				<?php
					$total += $resta;
				}
					?>
					<tr>
						<td></td><td></td><td></td><td></td><td></td>
						<td></td>
						<td></td>
						<td><b>TOTAL:</b></td><td><b> $<?php echo $total; ?></b></td>
						<td></td><td></td><td></td><td></td>
					</tr>
					<?php
				}
				?>
				</tbody>				
		</table><br>
		<div class="right"><br><a href="../php/imprimr_deudas.php" target="blank" class="waves-effect waves-light btn-small pink"><i class="material-icons right">print</i>Imprimir</a></div><br><br><br><br>

	</div>
</body>
</html>