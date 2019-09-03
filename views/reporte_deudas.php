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
						<th>Telefono</th>
						<th>Comunidad</th>	
						<th>Cantidad</th>
						<th>Fecha</th>
						<th>Descripción</th>
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
					$cliente = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM clientes WHERE id_cliente = $id_cliente"));
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

				?>
					<tr>
						<td><?php echo $cont; ?></td>
						<td><span class="new badge <?php echo$color; ?>" data-badge-caption=""><?php echo $estatus; ?></span></td>
						<td><?php echo $id_cliente; ?></td>
						<td><?php echo $cliente['nombre']; ?></td>
						<td><?php echo $cliente['telefono'] ?></td>
						<td><?php echo $Comunidad['nombre']; ?></td>						
						<td>$<?php echo $cantidad; ?></td>
						<td><?php echo $resultados['fecha_deuda']; ?></td>
						<td><?php echo $resultados['descripcion']; ?></td>
						<td><?php echo $usuario['firstname']; ?></td>
					<td><form method="post" action="../views/credito.php"><input id="no_cliente" name="no_cliente" type="hidden" value="<?php echo $id_cliente; ?>"><button class="btn-floating btn-tiny waves-effect waves-light pink"><i class="material-icons">send</i></button></form></td>
					</tr>
				<?php
					$total += $cantidad;
				}
					?>
					<tr>
						<td></td><td></td><td></td><td></td><td></td>
						<td><b>TOTAL:</b></td><td><b> $<?php echo $total; ?></b></td>
						<td></td><td></td><td></td><td></td>
					</tr>
					<?php
				}
				?>
				</tbody>				
		</table><br>
		<div class="right"><br><a href="../php/imprimr_deudas.php" target="blank" class="waves-effect waves-light btn-small pink"><i class="material-icons right">print</i>Imprimir</a></div><br><br>

	</div>
</body>
</html>