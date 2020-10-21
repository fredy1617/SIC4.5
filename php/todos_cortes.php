<?php
session_start();
include ('../php/conexion.php');

$ValorDe = $conn->real_escape_string($_POST['valorDe']);
$ValorA = $conn->real_escape_string($_POST['valorA']);

?>

<table class="bordered highlight responsive-table">
	<thead>
		<tr>
			<th>Id Corte</th>
			<th>Usuarios</th>
	        <th>Efectivo</th>
	        <th>Banco</th>
	        <th>Credito</th>
	        <th>Deducible(s)</th>
	        <th>Fecha</th>
	        <th>Hora</th>
	        <th>Clientes</th>
	        <th>Detalles</th>
		</tr>
	</thead>
	<tbody>
	<?php
	$resultado_cortes = mysqli_query($conn, "SELECT * FROM cortes WHERE fecha>='$ValorDe' AND fecha<='$ValorA' ORDER BY usuario DESC");
	$aux = mysqli_num_rows($resultado_cortes);
	if($aux>0){
	$total = 0;
	$totalClientes= 0;
	$totalbanco = 0;
	$totalcredito = 0;
	$totaldeducible = 0;
	while($cortes = mysqli_fetch_array($resultado_cortes)){
		$id_corte =$cortes['id_corte'];
		$pagos = mysqli_fetch_array(mysqli_query($conn,"SELECT count(*) FROM detalles WHERE id_corte = $id_corte"));
		$deducibles = mysqli_fetch_array(mysqli_query($conn,"SELECT * FROM deducibles WHERE id_corte = $id_corte"));
		$id_usuario = $cortes['usuario'];
		$usuario = mysqli_fetch_array(mysqli_query($conn,"SELECT * FROM users WHERE user_id = $id_usuario"));
	  ?>
	  <tr>
	    <td><b><?php echo $id_corte;?></b></td>
	    <td><?php echo $usuario['firstname'] ?></td>
	    <td>$<?php echo $cortes['cantidad'];?></td>
	    <td>$<?php echo $cortes['banco']; ?></td>
	    <td>$<?php echo $cortes['credito']; ?></td>
	    <td>$<?php echo ($deducibles['cantidad']== '')? 0:$deducibles['cantidad'].'<br>'.$deducibles['descripcion'];?></td>
	    <td><?php echo $cortes['fecha'];?></td>
	    <td><?php echo $cortes['hora'];?></td>
	    <td><?php echo $pagos['count(*)'];?></td>
	    <td><form method="post" action="../views/detalle_corte.php"><input id="id_corte" name="id_corte" type="hidden" value="<?php echo $cortes['id_corte']; ?>"><button class="btn-floating btn-tiny waves-effect waves-light pink"><i class="material-icons">credit_card</i></button></form></td>
	  </tr>
	  <?php
	  $total = $total+$cortes['cantidad'];
	  $totalbanco = $totalbanco+$cortes['banco'];
	  $totalcredito = $totalcredito+$cortes['credito'];
	  $totaldeducible = $totaldeducible+$deducibles['cantidad'];
	  $totalClientes = $totalClientes+$pagos['count(*)'];

	  $aux--;
	}
	?>
	  <tr>
	  	<td></td>
	  	<td><h5>TOTAL:</h5></td>
	  	<td><h5>$<?php echo $total; ?></h5></td>
	  	<td><h5>$<?php echo $totalbanco; ?></h5></td>
	  	<td><h5>$<?php echo $totalcredito; ?></h5></td>
	  	<td><h5>$<?php echo $totaldeducible; ?></h5></td><td></td>
	  	<td><h5>TOTAL:</h5></td>
	  	<td><h5><?php echo $totalClientes;?></h5></td>
	  	<td></td>
	  </tr>
	<?php
	}else{
	  echo "<center><b><h5>No se encontraron cortes para estas fechas</h5></b></center>";
	}
	?>
	<?php 
	mysqli_close($conn);
	?> 
		
	</tbody>
</table><br><br>