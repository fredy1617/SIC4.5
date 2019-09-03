<!DOCTYPE html>
<html>
<head>
	<title>SIC | RUTAS</title>
<?php
include ('fredyNav.php');
include('../php/conexion.php');
$rutas = mysqli_query($conn, "SELECT * FROM rutas ORDER BY id_ruta DESC");
include('../php/cobrador.php');
?>
</head>
<body>
	<div class="container">		
		<h3 class="hide-on-med-and-down">Todas las Rutas</h3>
  		<h5 class="hide-on-large-only">Todas las Rutas</h5>
  		<table class="bordered  highlight responsive-table">
  			<thead>
  			  <th>Ruta No.</th>
	          <th>Fecha</th>
	          <th>Estatus</th>
	          <th>Tecnicos</th>
	          <th>Detalles</th>  				
  			</thead>
  			<tbody>
  			<?php
			$filas = mysqli_num_rows($rutas);
			if ($filas == 0) {
		       ?>
	            <h5 class="center">No hay rutas</h5>
	            <?php                    
		    } else {		
			while($ruta = mysqli_fetch_array($rutas)) {
			if ($ruta['estatus']==0) {
				$Estatus = '<span class="new badge red" data-badge-caption="Pendiente"></span>';
			}else{
				$Estatus = '<span class="new badge green" data-badge-caption="Terminado"></span>';
			}
			?>
			<tr>
  			  <td><?php echo $ruta['id_ruta']; ?></td>
  			  <td><?php echo $ruta['fecha']; ?></td>
  			  <td><?php echo $Estatus; ?></td>
  			  <td><?php echo $ruta['tecnicos']; ?></td>
  			  <td>
  			  <form method="post" action="../views/detalles_ruta.php"><input id="id_ruta" name="id_ruta" type="hidden" value="<?php echo $ruta['id_ruta']; ?>"><button class="btn btn-floating pink waves-effect waves-light"><i class="material-icons">add</i></button></form></td>
  			</tr>
  			<?php
			}
			} ?>	
  			</tbody>
  		</table>
	</div>
</body>
</html>