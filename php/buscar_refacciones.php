<?php
include ('../php/conexion.php');
 $ValorDe = $conn->real_escape_string($_POST['valorDe']);
 $ValorA = $conn->real_escape_string($_POST['valorA']);

 $sql_refacciones = mysqli_query($conn, "SELECT * FROM refacciones WHERE fecha >= '$ValorDe' AND fecha <= '$ValorA'");
 $aux = mysqli_num_rows($sql_refacciones);
 ?>
 <table class="bordered highlight responsive-table">
	<thead>
		<tr>
			<th>ID</th>
			<th>Descripcion</th>
			<th>Cantidad</th>
			<th>Fecha</th>
			<th>Cliente</th>
			<th>Telefono</th>
			<th>Dispositivo</th>
		</tr>		
	</thead>
	<tbody>
 <?php
 if ($aux > 0) {

	echo '<script>M.toast({html:"Mostrando Dispositivos.", classes: "rounded"})</script>';

 	while ($refacciones = mysqli_fetch_array($sql_refacciones)) {
 		$id_dispositivo = $refacciones['id_dispositivo'];
 		$Dispositivo = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM dispositivos WHERE id_dispositivo = '$id_dispositivo'"));
 		?>
 		<tr>
 			<td><?php echo $refacciones['id_refaccion']; ?></td>
 			<td><?php echo $refacciones['descripcion']; ?></td>
 			<td><?php echo $refacciones['cantidad']; ?></td>
 			<td><?php echo $refacciones['fecha']; ?></td>
 			<td><?php echo $Dispositivo['nombre']; ?></td>
 			<td><?php echo $Dispositivo['telefono']; ?></td>
 			<td><?php echo $Dispositivo['marca']; ?></td>
 		</tr>

 		<?php
 	}
?>
	</tbody>
</table>
<?php
 }else{
 	echo "<center><b><h5>No se encontraron refacciones</h5></b></center>";
 }
mysqli_close($conn);
?>
