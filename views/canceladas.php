<!DOCTYPE html>
<html>
<head>
	<title>SIC | Instalaciones Canceladas</title>
<?php
include ('fredyNav.php');
include('../php/cobrador.php');
?>
</head>
<body>
	<div class="container">
		<div class="row">
			<h3 class="hide-on-med-and-down"> Instalaciones Canceladas</h3>
  			<h5 class="hide-on-large-only"> Instalaciones Canceladas</h5>
		</div>
		<table class="bordered highlight responsive-table" width="100%">
				<thead>
					<tr>
						<th>#</th>
						<th>Nombre</th>
						<th>Telefono</th>
						<th>Comunidad</th>
						<th>Direccion</th>
						<th>Referencia</th>	
						<th>Fecha Cancelacion</th>
						<th>Motivo</th>
						<th>Eliminó</th>
					</tr>
				</thead>
				<tbody>
				<?php
				$sql = mysqli_query($conn, "SELECT * FROM canceladas ORDER BY fecha DESC");
				$filas =  mysqli_num_rows($sql);
				if ($filas <= 0) {
					echo "<center><b><h5>No se encontraron instalaciones canceladas</h5></b></center>";
				}else{
				$aux = 0;
				while ( $resultados = mysqli_fetch_array($sql)) {
					$id_comunidad = $resultados['lugar'];
					$Comunidad = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM comunidades WHERE id_comunidad = $id_comunidad"));	
					$aux++;
				?>
					<tr>
						<td><b><?php echo $aux; ?></b></td>
						<td><?php echo $resultados['nombre']; ?></td>
						<td><?php echo $resultados['telefono']; ?></td>
						<td><?php echo $Comunidad['nombre']; ?></td>		
						<td><?php echo $resultados['direccion']; ?></td>			
						<td><?php echo $resultados['referencia']; ?></td>			
						<td><?php echo $resultados['fecha']; ?></td>			
						<td><?php echo $resultados['motivo']; ?></td>		
						<td><?php echo $resultados['usuario']; ?></td>		
					</tr>
				<?php
				}
				}
				?>
				</tbody>				
		</table><br><br><br>
	</div>
</body>
</html>