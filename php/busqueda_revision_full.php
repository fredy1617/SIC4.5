<?php 
		include('../php/conexion.php');
		$index = $conn->real_escape_string($_POST['valorIndex']);

		$inicia = 500 * ($index-1);
		$hasta = 500;

		$sql = mysqli_query($conn, "SELECT * FROM clientes  ORDER BY id_cliente LIMIT $inicia,$hasta");
		$filas = mysqli_num_rows($sql);
		if ($filas == 0) {
			# code..
		}else{
			while ($cliente = mysqli_fetch_array($sql)) {
				$id_comunidad = $cliente['lugar'];
				$comunidad = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM comunidades WHERE id_comunidad = '$id_comunidad'"));
				$id_servidor = $comunidad['servidor'];
				$Servidor = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM servidores WHERE id_servidor = '$id_servidor'"));
				$id_cliente = $cliente['id_cliente'];
				$pago = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM pagos WHERE id_cliente = '$id_cliente' AND tipo = 'Mensualidad' Order by fecha"));
		?>
			<tr>
				<td><?php echo $id_cliente; ?></td>
				<td><?php echo $cliente['nombre']; ?></td>
				<td><?php echo $cliente['fecha_corte']; ?></td>
				<td><?php echo $pago['descripcion']; ?></td>
				<td><?php echo $pago['cantidad']; ?></td>
				<td><?php echo $cliente['ip']; ?></td>
				<td><?php echo $Servidor['nombre']; ?></td>
				<td><form method="post" action="../views/editar_revision.php"><input name="id_cliente" type="hidden" value="<?php echo $id_cliente; ?>"><button type="submit" class="btn-floating btn-tiny waves-effect waves-light pink"><i class="material-icons">edit</i></button></form></td>
			</tr>
		<?php
			}
		}	

mysqli_close($conn);
?>