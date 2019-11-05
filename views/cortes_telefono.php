<!DOCTYPE html>
<html>
<head>
	<title>SIC | Cortes Telefono</title>
<?php
include('fredyNav.php')
?>
<script>
	function listo(id_cliente){    
      $.post("../php/cortar_tel.php", {
          valorIdCliente: id_cliente,
        }, function(mensaje) {
            $("#resp_listo").html(mensaje);
        });
	}
</script>
</head>
<body>
<div class="container">
	<div class="row">
		<h4>Cortar Telefono:</h4>
	</div>
	<div id="resp_listo"></div>
	<table class="bordered highlight responsive-table">
		<thead>
			<tr>
				<th>#</th>
				<th>Cliente</th>
				<th>Servicio</th>
				<th>Fecha Corte</th>
				<th>Comunidad</th>
				<th>Listo</th>
			</tr>
		</thead>
		<tbody>
		<?php
		date_default_timezone_set('America/Mexico_City');
		$Fecha_hoy = date('Y-m-d');
		$sql_tel = mysqli_query($conn,"SELECT * FROM clientes WHERE servicio IN ('Telefonia', 'Internet y Telefonia') AND tel_cortado = 0 AND corte_tel< '$Fecha_hoy'");

		$filas = mysqli_num_rows($sql_tel);
		if ($filas <= 0) {
			echo '<script>M.toast({html:"No se encontraron clientes.", classes: "rounded"})</script>';
		} else {
		  while ($cliente = mysqli_fetch_array($sql_tel)) {
		  	$id_comunidad = $cliente['lugar'];
		  	$comunidad = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM comunidades WHERE id_comunidad = $id_comunidad"));
		?>
			<tr>
				<td><?php echo $cliente['id_cliente']; ?></td>
				<td><?php echo $cliente['nombre']; ?></td>
				<td><?php echo $cliente['servicio']; ?></td>
				<td><?php echo $cliente['corte_tel']; ?></td>
				<td><?php echo $comunidad['nombre']; ?></td>
				<td><a onclick="listo(<?php echo $cliente['id_cliente'];?>);" class="btn btn-floating pink waves-effect waves-light"><i class="material-icons">check</i></a></td>
			</tr>
			<?php
			}
		}
		?>
		</tbody>
	</table>
</div>
</body>
</html>