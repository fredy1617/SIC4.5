<?php
include('../php/conexion.php');

$Tipo = $conn->real_escape_string($_POST['valorTipo']);
$ValorDe = $conn->real_escape_string($_POST['valorDe']);
$ValorA = $conn->real_escape_string($_POST['valorA']);
$Usuario = $conn->real_escape_string($_POST['valorUsuario']);
$Nombre = $conn->real_escape_string($_POST['valorNombre']);

$mensaje = '';

if ($Tipo == 'tecnico') {
	$sql = mysqli_query($conn, "SELECT * FROM reportes WHERE  fecha_solucion>='$ValorDe' AND fecha_solucion<='$ValorA' AND tecnico='$Usuario'  AND (atendido = 1 OR atendido != NULL) ");
}else{
	$consulta = mysqli_query($conn, "SELECT * FROM clientes WHERE nombre LIKE '%$Nombre%' or id_cliente = '$Nombre' Limit 1");
		
		$filas = mysqli_num_rows($consulta);
		if ($filas == 0) {
		$mensaje = '<script>M.toast({html:"No se encontraron clientes.", classes: "rounded"})</script>';
		$id_cliente = 0;
		} else {
		  	$Cliente = mysqli_fetch_array($consulta);
		  	$id_cliente = $Cliente['id_cliente'];
		}
	$sql = mysqli_query($conn, "SELECT * FROM reportes WHERE  fecha_solucion>='$ValorDe' AND fecha_solucion<='$ValorA' AND id_cliente='$id_cliente'  AND atendido = 1");
}
echo $mensaje;
?>
<table class="bordered highlight responsive-table" width="100%">
				<thead>
					<tr>
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
				while ($info = mysqli_fetch_array($sql)) {
					$id_cliente = $info['id_cliente'];
					$cliente = mysqli_fetch_array(mysqli_query($conn, "SELECT nombre FROM clientes WHERE id_cliente=$id_cliente"));

					$id_tecnico=$info['tecnico'];
					if ($id_tecnico == NULL) {
						$tecnico['user_name'] = 'Sin Tecnico';
					}else{
						$tecnico = mysqli_fetch_array(mysqli_query($conn, "SELECT user_name FROM users WHERE user_id=$id_tecnico"));
					}
				?>
					<tr>
						<td><?php echo $info['id_reporte']; ?></td>
						<td><?php echo $info['id_cliente']; ?></td>
						<td><?php echo $cliente['nombre']; ?></td>		
						<td><?php echo $info['fecha_solucion']; ?></td>
						<td><?php echo $info['hora_atendido']; ?></td>
						<td><?php echo $info['descripcion']; ?></td>
						<td><?php echo $tecnico['user_name']; ?></td>
					</tr>
				<?php } 
				mysqli_close($conn);
				?>
				</tbody>				
</table>
