<?php 
		include('../php/conexion.php');
		$index = $conn->real_escape_string($_POST['valorIndex']);

		$inicia = 250 * ($index-1);
		$hasta = 250;

		$sql = mysqli_query($conn, "SELECT * FROM clientes  ORDER BY id_cliente LIMIT $inicia,$hasta");
		$filas = mysqli_num_rows($sql);
		if ($filas == 0) {
			# code..
		}else{
			while ($cliente = mysqli_fetch_array($sql)) {
				$id_comunidad = $cliente['lugar'];
				$comunidad = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM comunidades WHERE id_comunidad = '$id_comunidad'"));
				$id_cliente = $cliente['id_cliente'];
				$pago =  mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM pagos WHERE id_cliente = $id_cliente AND tipo = 'Mensualidad' ORDER BY id_pago DESC LIMIT 1"));
				$pago2 =  mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM pagos WHERE id_cliente = $id_cliente AND tipo = 'Mensualidad' ORDER BY id_pago DESC LIMIT 1,1"));

		?>
			<tr>
				<td><?php echo $id_cliente; ?></td>
				<td><?php echo $cliente['nombre']; ?></td>
				<td><?php echo '<b>PENULTIMO: </b>$'.$pago2['cantidad'].' '.$pago2['descripcion'].'<br><b>ULTIMO: </b>$'.$pago['cantidad'].' '.$pago['descripcion']; ?></td>
				<td width="110px"><b><?php echo $cliente['fecha_corte']; ?></b></td>
				<td><?php echo $cliente['ip']; ?></td>
				<td><b><?php echo $cliente['servicio']; ?></b></td>
				<td><?php echo $comunidad['nombre']; ?></td>
				<td><form method="post" action="../views/editar_revision.php"><input name="id_cliente" type="hidden" value="<?php echo $id_cliente; ?>"><button type="submit" class="btn-floating btn-tiny waves-effect waves-light pink"><i class="material-icons">edit</i></button></form></td>
			</tr>
		<?php
			}
		}	

mysqli_close($conn);
?>