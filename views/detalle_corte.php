<!DOCTYPE html>
<html>
<head>
	<title>SIC | Detalle Corte</title>
<?php
	include('fredyNav.php');
	include('../php/conexion.php');
?>
</head>
<?php
if (isset($_POST['id_corte']) == false) {
	?>
	<script>
	  function atras(){
		M.toast({html: "Regresando a total cortes...", classes "rounded"});
		setTimeout("location.href='total_cortes.php'", 800);
	  }
	  atras();
	</script>
	<?php
}else{
?>
<script>
	function imprimir(id_corte){ 
      var a = document.createElement("a");
        a.target = "_blank";
        a.href = "../php/reimprimir_corte.php?id="+id_corte;
        a.click();
    };
</script>
<body>
  	<div class="container">
	  <?php 
	  $id_corte = $_POST['id_corte'];
	  $corte = mysqli_fetch_array(mysqli_query($conn,"SELECT * FROM cortes WHERE id_corte = $id_corte"));
	  ?>
	  <div class="row">
	  	<h3 class="hide-on-med-and-down">Detalle del Corte: (<?php echo $id_corte; ?>) - <?php echo $corte['fecha']; ?></h3>
  		<h5 class="hide-on-large-only">Detalle del Corte: (<?php echo $id_corte; ?>) - <?php echo $corte['fecha']; ?></h5>
	  </div><br>
	  <h4 class="row"><b><< Internet >></b></h4>
		<div class="row">
	      <h5 class="blue-text"><b>Efectivo:</b></h5>
		  <table class="bordered  highlight responsive-table">
		  	<thead>
		  		<tr>
		  			<th>Id Pago</th>
			  		<th>Cliente</th>
			  		<th>Descripción</th>
			  		<th>Tipo</th>
			  		<th>Fecha</th>
			  		<th>Cantidad</th>
		  		</tr>
		  	</thead>
		  	<tbody>
		  	<?php
		    $detalles = mysqli_query($conn,  "SELECT * FROM detalles WHERE id_corte = $id_corte ORDER BY id_pago DESC");
		    $aux = mysqli_num_rows($detalles);
		    if($aux>0){
		    $TotalEI = 0;
		    while($pagos = mysqli_fetch_array($detalles)){
		    	$id_pago = $pagos['id_pago'];
		    	$sql =  mysqli_query($conn,  "SELECT * FROM pagos WHERE id_pago = $id_pago AND tipo_cambio = 'Efectivo' AND tipo != 'Dispositivo' AND tipo != 'Orden Servicio'");
		    	$fila = mysqli_num_rows($sql);
		    	if ($fila > 0) {
		    	$pagox1 = mysqli_fetch_array($sql);
		    	$id_cliente = $pagox1['id_cliente'];
		        if ($pagox1['tipo'] == 'Abono Corte') {
                  $cliente = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM users WHERE user_id = $id_cliente"));
              	}else if ((mysqli_num_rows(mysqli_query($conn, "SELECT * FROM clientes WHERE id_cliente = $id_cliente"))) == 0) {
		          	$cliente = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM especiales WHERE id_cliente = $id_cliente"));
		        }else{
		            $cliente = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM clientes WHERE id_cliente = $id_cliente"));
		        }
		  	?>	
		  	  <tr>
			    <td><?php echo $pagox1['id_pago'];?></td>
                <td><?php echo ($pagox1['tipo'] == 'Abono Corte')?'USUARIO: '.$cliente['firstname'].' '.$cliente['lastname']:$cliente['nombre']; ?></td>
		        <td><?php echo $pagox1['descripcion'];?></td>   
		        <td><?php echo $pagox1['tipo'];?></td>
		        <td><?php echo $pagox1['fecha'].' '.$pagox1['hora'];?></td>
	       		<td>$<?php echo $pagox1['cantidad'];?></td>
		      </tr>
		      <?php
		      	$TotalEI += $pagox1['cantidad'];
		    	}
		    }
		    ?>
		      <tr>
		      	<td></td><td></td><td></td><td></td>
		      	<td><b>TOTAL:<b></td>
		      	<td><b>$<?php echo $TotalEI;?></b></td>
		      </tr>
		    <?php
		    }else{
		      echo "<center><b><h5>Este usuario aún no ha registrado pagos</h5></b></center>";
		    }
		    ?>
		  	</tbody>
		  </table><br>

	      <h5 class="blue-text"><b>Banco:</b></h5>
		  
		  <table class="bordered  highlight responsive-table">
		  	<thead>
		  		<tr>
		  			<th>Id Pago</th>
			  		<th>Cliente</th>
			  		<th>Descripción</th>
			  		<th>Tipo</th>
			  		<th>Fecha</th>
			  		<th>Cantidad</th>
		  		</tr>
		  	</thead>
		  	<tbody>
		  	<?php
		  	$detalles = mysqli_query($conn,  "SELECT * FROM detalles WHERE id_corte = $id_corte ORDER BY id_pago DESC");
		    $aux = mysqli_num_rows($detalles);
		  	if ($aux > 0) {
		  	$TotalBI = 0;
		  	while ($pagos= mysqli_fetch_array($detalles)) {
		  		$id_pago = $pagos['id_pago'];
		  		$sql = mysqli_query($conn, "SELECT * FROM pagos WHERE id_pago = $id_pago AND tipo_cambio = 'Banco' AND tipo != 'Dispositivo' AND tipo != 'Orden Servicio'");
		  		$filas = mysqli_num_rows($sql);
		  		if ($filas > 0) {
		  		$pago = mysqli_fetch_array($sql);
		  		$id_cliente = $pago['id_cliente'];
		  		if ($pago['tipo'] == 'Abono Corte') {
                  $cliente = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM users WHERE user_id = $id_cliente"));
              	}else if ((mysqli_num_rows(mysqli_query($conn, "SELECT * FROM clientes WHERE id_cliente = $id_cliente"))) == 0) {
		          	$cliente = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM especiales WHERE id_cliente = $id_cliente"));
		        }else{
		            $cliente = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM clientes WHERE id_cliente = $id_cliente"));
		        }
		  	?>	
		  		<tr>
			  		<td><?php echo $pago['id_pago'];?></td>
                	<td><?php echo ($pago['tipo'] == 'Abono Corte')?'USUARIO: '.$cliente['firstname'].' '.$cliente['lastname']:$cliente['nombre']; ?></td>
			  		<td><?php echo $pago['descripcion']; ?></td>
			  		<td><?php echo $pago['tipo']; ?></td>
			  		<td><?php echo $pago['fecha'].' '.$pagox1['hora']; ?></td>
			  		<td><?php echo $pago['cantidad']; ?></td>
		  		</tr>
		  	<?php
		  	   $TotalBI += $pago['cantidad'];
		  	   }
		  	}
		  	?>
		  		<tr>
		  			<td></td><td></td><td></td><td></td>
		  			<td><b>TOTAL:</b></td>
		  			<td><b>$<?php echo $TotalBI; ?></b></td>
		  		</tr>
		  	<?php
		  	}  ?>
		  	</tbody>
		  </table><br>
		  <h5 class="blue-text"><b>Credito:</b></h5>
		  
		  <table class="bordered  highlight responsive-table">
		  	<thead>
		  		<tr>
		  			<th>Id Pago</th>
			  		<th>Cliente</th>
			  		<th>Descripción</th>
			  		<th>Tipo</th>
			  		<th>Fecha</th>
			  		<th>Cantidad</th>
		  		</tr>
		  	</thead>
		  	<tbody>
		  	<?php
		  	$detalles = mysqli_query($conn,  "SELECT * FROM detalles WHERE id_corte = $id_corte ORDER BY id_pago DESC");
		    $aux = mysqli_num_rows($detalles);
		  	if ($aux > 0) {
		  	$TotalBI = 0;
		  	while ($pagos= mysqli_fetch_array($detalles)) {
		  		$id_pago = $pagos['id_pago'];
		  		$sql = mysqli_query($conn, "SELECT * FROM pagos WHERE id_pago = $id_pago AND tipo_cambio = 'Credito'AND tipo != 'Dispositivo'");
		  		$filas = mysqli_num_rows($sql);
		  		if ($filas > 0) {
		  		$pago = mysqli_fetch_array($sql);
		  		$id_cliente = $pago['id_cliente'];
		  		if ((mysqli_num_rows(mysqli_query($conn, "SELECT * FROM clientes WHERE id_cliente = $id_cliente"))) == 0) {
		          	$cliente = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM especiales WHERE id_cliente = $id_cliente"));
		        }else{
		            $cliente = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM clientes WHERE id_cliente = $id_cliente"));
		        }
		  	?>	
		  		<tr>
			  		<td><?php echo $pago['id_pago'];?></td>
			  		<td><?php echo $cliente['nombre']; ?></td>
			  		<td><?php echo $pago['descripcion']; ?></td>
			  		<td><?php echo $pago['tipo']; ?></td>
			  		<td><?php echo $pago['fecha'].' '.$pagox1['hora']; ?></td>
			  		<td><?php echo $pago['cantidad']; ?></td>
		  		</tr>
		  	<?php
		  	   $TotalBI += $pago['cantidad'];
		  	   }
		  	}
		  	?>
		  		<tr>
		  			<td></td><td></td><td></td><td></td>
		  			<td><b>TOTAL:</b></td>
		  			<td><b>- $<?php echo $TotalBI; ?></b></td>
		  		</tr>
		  	<?php
		  	}  ?>
		  	</tbody>
		  </table><br>
		</div><br><br>
	  <h4 class="row"><b><< Orden Servicio >></b></h4>
		<div class="row">
	      <h5 class="blue-text"><b>Efectivo:</b></h5>
		  <table class="bordered  highlight responsive-table">
		  	<thead>
		  		<tr>
		  			<th>Id Pago</th>
			  		<th>Cliente</th>
			  		<th>Descripción</th>
			  		<th>Tipo</th>
			  		<th>Fecha</th>
			  		<th>Cantidad</th>
		  		</tr>
		  	</thead>
		  	<tbody>
		  	<?php
		    $detalles = mysqli_query($conn,  "SELECT * FROM detalles WHERE id_corte = $id_corte ORDER BY id_pago DESC");
		    $aux = mysqli_num_rows($detalles);
		    if($aux>0){
		    $TotalEO = 0;
		    while($pagos = mysqli_fetch_array($detalles)){
		    	$id_pago = $pagos['id_pago'];
		    	$sql =  mysqli_query($conn,  "SELECT * FROM pagos WHERE id_pago = $id_pago AND tipo_cambio = 'Efectivo' AND tipo = 'Orden Servicio'");
		    	$fila = mysqli_num_rows($sql);
		    	if ($fila > 0) {
		    	$pagox1 = mysqli_fetch_array($sql);
		    	$id_cliente = $pagox1['id_cliente'];
		    	$sql2 = mysqli_query($conn,  "SELECT nombre FROM especiales WHERE id_cliente = $id_cliente");
		    	$cliente = mysqli_fetch_array($sql2);
		      ?>
		      <tr>
		        <td><?php echo $pagox1['id_pago'];?></td>
		        <td><?php echo $cliente['nombre'];?></td>
		        <td><?php echo $pagox1['descripcion'];?></td>   
		        <td><?php echo $pagox1['tipo'];?></td>
		        <td><?php echo $pagox1['fecha'].' '.$pagox1['hora'];?></td>
	       		<td>$<?php echo $pagox1['cantidad'];?></td>
		      </tr>
		      <?php
		      	$TotalEO += $pagox1['cantidad'];
		    	}
		    }
		    ?>
		      <tr>
		      	<td></td><td></td><td></td><td></td>
		      	<td><b>TOTAL:<b></td>
		      	<td><b>$<?php echo $TotalEO;?></b></td>
		      </tr>
		    <?php
		    }
		    ?>
		  	</tbody>
		  </table><br>

	      <h5 class="blue-text"><b>Banco:</b></h5>		  
		  <table class="bordered  highlight responsive-table">
		  	<thead>
		  		<tr>
		  			<th>Id Pago</th>
			  		<th>Cliente</th>
			  		<th>Descripción</th>
			  		<th>Tipo</th>
			  		<th>Fecha</th>
			  		<th>Cantidad</th>
		  		</tr>
		  	</thead>
		  	<tbody>
		  	<?php
		  	$detalles = mysqli_query($conn,  "SELECT * FROM detalles WHERE id_corte = $id_corte ORDER BY id_pago DESC");
		    $aux = mysqli_num_rows($detalles);
		  	if ($aux > 0) {
		  	$TotalBO = 0;
		  	while ($pagos= mysqli_fetch_array($detalles)) {
		  		$id_pago = $pagos['id_pago'];
		  		$sql = mysqli_query($conn, "SELECT * FROM pagos WHERE id_pago = $id_pago AND tipo_cambio = 'Banco' AND tipo = 'Orden Servicio'");
		  		$filas = mysqli_num_rows($sql);
		  		if ($filas > 0) {
		  		$pago = mysqli_fetch_array($sql);
		  		$id_cliente = $pago['id_cliente'];
		  		$sql2 = mysqli_query($conn,  "SELECT nombre FROM especiales WHERE id_cliente = $id_cliente");
		    	$cliente = mysqli_fetch_array($sql2);
		  	?>	
		  		<tr>
			  		<td><?php echo $pago['id_pago'];?></td>
			  		<td><?php echo $cliente['nombre']; ?></td>
			  		<td><?php echo $pago['descripcion']; ?></td>
			  		<td><?php echo $pago['tipo']; ?></td>
			  		<td><?php echo $pago['fecha'].' '.$pagox1['hora']; ?></td>
			  		<td><?php echo $pago['cantidad']; ?></td>
		  		</tr>
		  	<?php
		  	   $TotalBO += $pago['cantidad'];
		  	   }
		  	}
		  	?>
		  		<tr>
		  			<td></td><td></td><td></td><td></td>
		  			<td><b>TOTAL:</b></td>
		  			<td><b>$<?php echo $TotalBO; ?></b></td>
		  		</tr>
		  	<?php
		  	}  ?>
		  	</tbody>
		  </table><br>
		</div><br><br>
	  <h4 class="row"><b><< Servicio Técnico >></b></h4>
		<div class="row">
	      <h5 class="blue-text"><b>Efectivo:</b></h5>
		  <table class="bordered  highlight responsive-table">
		  	<thead>
		  		<tr>
		  			<th>Id Pago</th>
			  		<th>Cliente</th>
			  		<th>Descripción</th>
			  		<th>Tipo</th>
			  		<th>Fecha</th>
			  		<th>Cantidad</th>
		  		</tr>
		  	</thead>
		  	<tbody>
		  	<?php
		    $detalles = mysqli_query($conn,  "SELECT * FROM detalles WHERE id_corte = $id_corte ORDER BY id_pago DESC");
		    $aux = mysqli_num_rows($detalles);
		    if($aux>0){
		    $TotalEST = 0;
		    while($pagos = mysqli_fetch_array($detalles)){
		    	$id_pago = $pagos['id_pago'];
		    	$sql =  mysqli_query($conn,  "SELECT * FROM pagos WHERE id_pago = $id_pago AND tipo_cambio = 'Efectivo' AND tipo = 'Dispositivo'");
		    	$fila = mysqli_num_rows($sql);
		    	if ($fila > 0) {
		    	$pagox1 = mysqli_fetch_array($sql);
		    	$id_cliente = $pagox1['id_cliente'];
		    	$sql2 = mysqli_query($conn,  "SELECT nombre FROM dispositivos WHERE id_dispositivo = $id_cliente");
		    	$cliente = mysqli_fetch_array($sql2);
		      ?>
		      <tr>
		        <td><?php echo $pagox1['id_pago'];?></td>
		        <td><?php echo $cliente['nombre'];?></td>
		        <td><?php echo $pagox1['descripcion'];?></td>   
		        <td><?php echo $pagox1['tipo'];?></td>
		        <td><?php echo $pagox1['fecha'].' '.$pagox1['hora'];?></td>
	       		<td>$<?php echo $pagox1['cantidad'];?></td>
		      </tr>
		      <?php
		      	$TotalEST += $pagox1['cantidad'];
		    	}
		    }
		    ?>
		      <tr>
		      	<td></td><td></td><td></td><td></td>
		      	<td><b>TOTAL:<b></td>
		      	<td><b>$<?php echo $TotalEST;?></b></td>
		      </tr>
		    <?php
		    }
		    ?>
		  	</tbody>
		  </table><br>

	      <h5 class="blue-text"><b>Banco:</b></h5>		  
		  <table class="bordered  highlight responsive-table">
		  	<thead>
		  		<tr>
		  			<th>Id Pago</th>
			  		<th>Cliente</th>
			  		<th>Descripción</th>
			  		<th>Tipo</th>
			  		<th>Fecha</th>
			  		<th>Cantidad</th>
		  		</tr>
		  	</thead>
		  	<tbody>
		  	<?php
		  	$detalles = mysqli_query($conn,  "SELECT * FROM detalles WHERE id_corte = $id_corte ORDER BY id_pago DESC");
		    $aux = mysqli_num_rows($detalles);
		  	if ($aux > 0) {
		  	$TotalBST = 0;
		  	while ($pagos= mysqli_fetch_array($detalles)) {
		  		$id_pago = $pagos['id_pago'];
		  		$sql = mysqli_query($conn, "SELECT * FROM pagos WHERE id_pago = $id_pago AND tipo_cambio = 'Banco'AND tipo = 'Dispositivo'");
		  		$filas = mysqli_num_rows($sql);
		  		if ($filas > 0) {
		  		$pago = mysqli_fetch_array($sql);
		  		$id_cliente = $pago['id_cliente'];
		    	$sql2 = mysqli_query($conn,  "SELECT nombre FROM dispositivos WHERE id_dispositivo = $id_cliente");
		    	$cliente = mysqli_fetch_array($sql2);
		  	?>	
		  		<tr>
			  		<td><?php echo $pago['id_pago'];?></td>
			  		<td><?php echo $cliente['nombre']; ?></td>
			  		<td><?php echo $pago['descripcion']; ?></td>
			  		<td><?php echo $pago['tipo']; ?></td>
			  		<td><?php echo $pago['fecha'].' '.$pagox1['hora']; ?></td>
			  		<td><?php echo $pago['cantidad']; ?></td>
		  		</tr>
		  	<?php
		  		$TotalBST += $pago['cantidad'];
		  	   }
		  	}
		  	?>
		  		<tr>
		  			<td></td><td></td><td></td><td></td>
		  			<td><b>TOTAL:</b></td>
		  			<td><b>$<?php echo $TotalBST; ?></b></td>
		  		</tr>
		  	<?php
		  	}  ?>
		  	</tbody>
		  </table>
		</div>	
  	<a class="waves-effect waves-light btn pink right" onclick="imprimir(<?php echo $id_corte; ?>);">Imprimir<i class="material-icons right">print</i></a><br><br><br>
  	</div>
</body>
<?php
}
?>
</html>