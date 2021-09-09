<!DOCTYPE html>
<html>
<head>
	<title>SIC | Contratos Vencidos</title>
<?php
	include('fredyNav.php');
?>
<script>
	function insert_reporte(id_cliente){
		if (id_cliente == "") {
			M.toast({html: "Ocurrio un error al seleccionar el reporte.", classes: "rounded"});
		}else{
			$.post("../php/instert_reporte_cont.php",{
				valorId_Cliente: id_cliente,
			}, function(mensaje){
				$("#insertar_reporte").html(mensaje);
			});
		}
	};
</script>
</head>
<body>
	<div class="container">
		<div class="row">
			<h3 class="hide-on-med-and-down">Clientes por Contrato:</h3>	
			<h5 class="hide-on-large-only">Clientes por Contrato:</h5>
			<a class="waves-effect waves-green btn pink right" href="../views/clientes_contrato.php">Todos<i class="material-icons right">assignment</i></a>		
		</div>
		<div class="row" id="insertar_reporte">
			<table class="bordered  highlight responsive-table">
				<thead>
					<tr>
						<th>Dias</th>		
						<th>#</th>
						<th>Nombre</th>
						<th>Comunidad</th>
						<th>Telefono</th>
						<th>Fecha Contrato</th>
						<th>Pago</th>
						<th>Reporte</th>
					</tr>
				</thead>
				<tbody>
				<?php
				include('../php/conexion.php');
				date_default_timezone_set('America/Mexico_City');

				$Hoy = date('Y-m-d');
				$nuevafecha = strtotime('+2 day', strtotime($Hoy));
				$Falta2D = date('Y-m-d', $nuevafecha);
				$contratos = mysqli_query($conn, "SELECT * FROM clientes WHERE contrato = 1 AND fecha_corte<= '$Falta2D' ORDER BY fecha_corte");
				$aux = mysqli_num_rows($contratos);
				if ($aux>0) {
					while ($contrato = mysqli_fetch_array($contratos)) {						
						$date1 = new DateTime($Hoy);
						$date2 = new DateTime($contrato['fecha_corte']);
						//Se resta a date1-date2
						$diff = $date1->diff($date2);
						$Dias = $diff->days; 
						$color = "green";
						$signos = "";
						if ($Hoy > $contrato['fecha_corte']) {
							$color = "red accent-4";
							$signos = "-";
						}
						$id_comunidad = $contrato['lugar'];
						$Comunidad = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM comunidades WHERE id_comunidad = '$id_comunidad'"));

				?>
					<tr>
						<td><span class="new badge <?php echo $color; ?>" data-badge-caption="<?php echo $Dias; ?>"><?php echo $signos; ?></span></td>
						<td><?php echo $contrato['id_cliente']; ?></td>
						<td><?php echo $contrato['nombre']; ?></td>
						<td><?php echo $Comunidad['nombre'].', '.$Comunidad['municipio']; ?></td>
						<td><?php echo $contrato['telefono']; ?></td>
						<td><?php echo $contrato['fecha_corte']; ?></td>
						<td><form method="post" action="../views/crear_pago.php"><input id="no_cliente" name="no_cliente" type="hidden" value="<?php echo $contrato['id_cliente']; ?>"><button class="btn-floating btn-tiny waves-effect waves-light pink"><i class="material-icons">payment</i></button></form></td>
						<td><a onclick="insert_reporte(<?php echo $contrato['id_cliente']; ?>);" class="btn btn-floating pink waves-effect waves-light"><i class="material-icons">add</i></a></td>
					</tr>
				<?php
					}
				}else{
					echo '<center><b><h4>No se encontraron contratos retrasados o a punto de expirar</h4></b></center>';
				}
				?>
				</tbody>
			</table>
		</div>
	</div>
</body>
</html>