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
			<h3 class="hide-on-med-and-down">Contratos Internet</h3>	
			<h5 class="hide-on-large-only">Contratos Internet</h5>		
		</div>
		<div class="row" id="insertar_reporte">
			<table class="bordered  highlight responsive-table">
				<thead>
					<tr>
						<th>Estatus</th>		
						<th>#</th>
						<th>Nombre</th>
						<th>Comunidad</th>
						<th>Telefono</th>
						<th>Fecha Instalacion</th>
						<th>Fecha Vencimiento</th>
					</tr>
				</thead>
				<tbody>
				<?php
				include('../php/conexion.php');
				date_default_timezone_set('America/Mexico_City');

				$Hoy = date('Y-m-d');
				$contratos = mysqli_query($conn, "SELECT * FROM clientes WHERE contrato = 1  ORDER BY fecha_instalacion");
				$aux = mysqli_num_rows($contratos);
				if ($aux>0) {
					while ($contrato = mysqli_fetch_array($contratos)) {						
						$Instalacion = $contrato['fecha_instalacion'];
						$nuevafecha = strtotime('+6 months', strtotime($Instalacion));
						$Vence = date('Y-m-d', $nuevafecha);

						$id_comunidad = $contrato['lugar'];
						$Comunidad = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM comunidades WHERE id_comunidad = '$id_comunidad'"));

						$color = "green";
						$Estatus = "Vigente";
						if ($Hoy > $Vence) {
							$color = "red accent-4";
							$Estatus = "Vencido";
						}
				?>
					<tr>
						<td><span class="new badge <?php echo $color; ?>" data-badge-caption=""><?php echo $Estatus; ?></span></td>
						<td><?php echo $contrato['id_cliente']; ?></td>
						<td><?php echo $contrato['nombre']; ?></td>
						<td><?php echo $Comunidad['nombre']; ?></td>
						<td><?php echo $contrato['telefono']; ?></td>
						<td><?php echo $Instalacion; ?></td>
						<td><?php echo $Vence; ?></td>
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