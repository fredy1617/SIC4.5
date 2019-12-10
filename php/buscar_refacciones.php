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
			<th>#</th>
			<th>Cliente</th>
			<th>Dispositivo</th>
			<th>Refacción</th>
			<th>Fecha</th>
			<th>Cantidad</th>
		</tr>		
	</thead>
	<tbody>
 <?php
 if ($aux > 0) {

	echo '<script>M.toast({html:"Mostrando refacciones.", classes: "rounded"})</script>';
	$Total = 0;
 	while ($refacciones = mysqli_fetch_array($sql_refacciones)) {
 		$id_dispositivo = $refacciones['id_dispositivo'];
 		$Dispositivo = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM dispositivos WHERE id_dispositivo = '$id_dispositivo'"));
 		?>
 		<tr>
 			<td><b><?php echo $refacciones['id_refaccion']; ?></b></td>
 			<td><?php echo $Dispositivo['nombre']; ?></td>
 			<td><?php echo $Dispositivo['tipo'].' '.$Dispositivo['marca']; ?></td>
 			<td><?php echo $refacciones['descripcion']; ?></td>
 			<td><?php echo $refacciones['fecha']; ?></td>
 			<td>$ <?php echo $refacciones['cantidad']; ?></td>
 		</tr>
 		<?php
 		$Total += $refacciones['cantidad'];
 	}
	?>
		<tr>
 			<td></td><td></td><td></td><td></td>
 			<td><b>TOTAL:</b></td>
 			<td><b>$ <?php echo $Total; ?></b></td>
 		</tr>
	</tbody>
</table>
<?php
 }else{
 	echo "<center><b><h5>No se encontraron refacciones</h5></b></center>";
 }
mysqli_close($conn);
?>
