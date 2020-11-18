<?php
include('../php/conexion.php');
date_default_timezone_set('America/Mexico_City');

$id_cliente = $conn->real_escape_string($_POST['valorId_Cliente']);
$Descripcion = "Recoger aparatos por incumplimiento de pago";
$Fecha = date('Y-m-d');

$cliente = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM clientes WHERE id_cliente = '$id_cliente'"));

$Fecha_Instalacion = $cliente['fecha_instalacion'];

$nuevafecha = strtotime('+6 month', strtotime($Fecha_Instalacion));
$TerminoContrato = date('Y-m-d', $nuevafecha);

if ($TerminoContrato > $Fecha) {
	$Descripcion = "Multa por incumplimiento  de contrato y recoger aparatos por falta de pago";
}
if(mysqli_query($conn, "INSERT INTO reportes (id_cliente, descripcion, fecha) VALUES ($id_cliente, '$Descripcion', '$Fecha')")){
	echo '<script>M.toast({html: "El reporte se dio de alta con exito.", classes: "rounded"})</script>';
}else{
	echo '<script>M.toast({html: "Ocurrio un error, no se registro el reporte.", classes: "rounded"})</script>';
}
?>
		<div class="row" id="insertar_reporte">
			<table>
				<thead>
					<tr>
						<th>Dias</th>		
						<th>#</th>
						<th>Nombre</th>
						<th>Comunidad</th>
						<th>Telefono</th>
						<th>Fecha Contrato</th>
						<th>Ver</th>
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
						<td><?php echo $Comunidad['nombre']; ?></td>
						<td><?php echo $contrato['telefono']; ?></td>
						<td><?php echo $contrato['fecha_corte']; ?></td>
						<td><a onclick="insert_reporte(<?php echo $contrato['id_cliente']; ?>);" class="btn btn-floating pink waves-effect waves-light"><i class="material-icons">send</i></a></td>
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
<?php
mysqli_close($conn);
?> 