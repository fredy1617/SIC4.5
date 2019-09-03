<!DOCTYPE html>
<html>
<head>
	<title>SIC | Cajas</title>
<?php
include ('fredyNav.php');
include ('../php/superAdmin.php');
?>
</head>
<body>
	<div class="container">
		<div class="row">
			<h3 class="hide-on-med-and-down">Saldo en Cajas:</h3>
  			<h5 class="hide-on-large-only">Saldo en Cajas:</h5>
		</div>
		<table class="bordered highlight responsive-table" width="100%">
				<thead>
					<tr>
						<th>Nombre</th>
						<th>Apellidos</th>
						<th>En Caja</th>
						<th>Banco</th>
					</tr>
				</thead>
				<tbody>
				<?php 
                $sql_tmp = mysqli_query($conn,"SELECT * FROM users");
                $columnas = mysqli_num_rows($sql_tmp);
                if($columnas == 0){
                    ?>
                    <h5 class="center">No hay instalaciones Usuarios</h5>	
                    <?php
                }else{
                $Todo = 0;
                $AllBanco = 0;
                while($tmp = mysqli_fetch_array($sql_tmp)){
                	$id_user = $tmp['user_id'];
					$sql=mysqli_query($conn,"SELECT SUM(cantidad)  AS suma FROM pagos WHERE id_user=$id_user AND corte = 0 AND tipo_cambio='Efectivo'");
					$sql2=mysqli_query($conn,"SELECT SUM(cantidad)  AS suma FROM pagos WHERE id_user=$id_user AND corte = 0 AND tipo_cambio='Banco'");
					$num = mysqli_num_rows($sql);	
					$pagos = mysqli_fetch_array($sql);					
					$banco = mysqli_fetch_array($sql2);

					$total = $pagos['suma']; 
					$Banco = $banco['suma'];
					if ($total =='') {
						$total= 0;
					}
					if ($Banco =='') {
						$Banco= 0;
					}
                ?>
					<tr>
						<td><?php echo $tmp['firstname']; ?></td>
						<td><?php echo $tmp['lastname']; ?></td>
						<td>$<?php echo $total; ?></td>	
						<td>$<?php echo $Banco; ?></td>	
					</tr>
					<?php
					$Todo = $Todo+$total;
					$AllBanco = $AllBanco+$Banco;
                    }
                }
                ?>
					<tr>
						<td></td>
						<td><h5>TOTAL:</h5></td>
						<td><h5>$<?php echo $Todo; ?></h5></td>
						<td><h5>$<?php echo $AllBanco; ?></h5></td>
					</tr>
				</tbody>				
		</table>
	</div>
</body>
</html>