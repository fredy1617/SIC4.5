<!DOCTYPE html>
<html>
<head>
	<title>SIC | Reportes de Rutas</title>
<?php
include ('fredyNav.php');
include('../php/Admin.php');
?>
</head>
<body>
	<div class="container">
		<div class="row">
			<h3 class="hide-on-med-and-down"> Reporte de Rutas</h3>
  			<h5 class="hide-on-large-only"> Reporte de Rutas</h5>
		</div>
		<table class="bordered highlight responsive-table" width="100%">
			<thead>
				<tr>
					<th>#</th>
					<th>Responsable</th>
					<th>Acompañante</th>
					<th>Fecha</th>
					<th>Hora</th>
					</tr>
			</thead>
			<tbody>
			<?php
			$sql = mysqli_query($conn, "SELECT * FROM rutas WHERE fecha > '2020-09-09' ORDER BY id_ruta DESC");
			$filas =  mysqli_num_rows($sql);
			if ($filas <= 0) {
				echo "<center><b><h3>No se encontraron rutas</h3></b></center>";
			}else{
				while ( $resultados = mysqli_fetch_array($sql)) {	
				?>
				<tr>		
					<td><?php echo $resultados['id_ruta']; ?></td>			
					<td><?php echo $resultados['responsable']; ?></td>			
					<td><?php echo $resultados['acompanante']; ?></td>			
					<td><?php echo $resultados['fecha']; ?></td>			
					<td><?php echo $resultados['hora']; ?></td>			
				</tr>
				<?php
				}
			}
				?>
			</tbody>				
		</table><br>
	</div>
</body>
</html>