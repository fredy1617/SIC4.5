<?php
include('../php/conexion.php');

$Tipo = $conn->real_escape_string($_POST['valorTipo']);
$ValorDe = $conn->real_escape_string($_POST['valorDe']);
$ValorA = $conn->real_escape_string($_POST['valorA']);
$Usuario = $conn->real_escape_string($_POST['valorUsuario']);

if ($Tipo == 'tecnico') {
	$sql = mysqli_query($conn, "SELECT * FROM reportes WHERE  fecha_solucion>='$ValorDe' AND fecha_solucion<='$ValorA' AND tecnico='$Usuario'  AND (atendido = 1 OR atendido != NULL) ");
}else{
	$sql = mysqli_query($conn, "SELECT * FROM reportes WHERE  fecha_solucion>='$ValorDe' AND fecha_solucion<='$ValorA' AND atendido = 1");
}
?>
<table class="bordered highlight responsive-table" width="100%">
	<thead>
		<tr>
			<th>#</th>
			<th>Id. Reporte</th>
			<th>Id. Cliente</th>
			<th>Nombre Cliente</th>
			<th>Fecha Solución</th>
			<th>Hora</th>
			<th width="15%">Descripción</th>
			<th>Técnico</th>
		</tr>
	</thead>
	<tbody>
	<?php 
		$aux = 0;
		while ($info = mysqli_fetch_array($sql)) {
			$aux ++;
			$id_cliente = $info['id_cliente'];
			$sql_c = mysqli_query($conn, "SELECT * FROM clientes WHERE id_cliente=$id_cliente");
			$filas = mysqli_num_rows($sql_c);
			if ($filas == 0) {
			    $sql_c = mysqli_query($conn, "SELECT * FROM especiales WHERE id_cliente=$id_cliente");
			}
			$cliente = mysqli_fetch_array($sql_c);

			$id_tecnico=$info['tecnico'];
			if ($id_tecnico == NULL) {
				$tecnico['user_name'] = 'Sin Tecnico';
			}else{
				$tecnico = mysqli_fetch_array(mysqli_query($conn, "SELECT user_name FROM users WHERE user_id=$id_tecnico"));
			}
			$id_apoyo=$info['apoyo'];
			if ($id_apoyo == NULL OR $id_apoyo == 0) {
				$apoyo = '';
			}else{
				$sql_apoyo = mysqli_fetch_array(mysqli_query($conn, "SELECT user_name FROM users WHERE user_id=$id_apoyo"));
				$apoyo = ', Apoyo: '.$sql_apoyo['user_name'];
			}
			?>
			<tr>
				<td><?php echo $aux; ?></td>
				<td><?php echo $info['id_reporte']; ?></td>
				<td><?php echo $info['id_cliente']; ?></td>
				<td><?php echo $cliente['nombre']; ?></td>		
				<td><?php echo $info['fecha_solucion']; ?></td>
				<td><?php echo $info['hora_atendido']; ?></td>
				<td><?php echo $info['descripcion']; ?></td>
				<td><?php echo $tecnico['user_name'].$apoyo; ?></td>
			</tr>
		<?php } //FIN WHILE
		mysqli_close($conn);
		?>
	</tbody>				
</table>
