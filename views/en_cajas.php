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
				      	<th colspan="2"></th>
				      	<th colspan="2">En Caja</th>
				      	<th colspan="2"></th>
				    </tr>
					<tr>
						<th>Nombre</th>
						<th>Apellidos</th>
						<th>Corte</th>
						<th>Pendiente</th>
						<th>Banco</th>
						<th>Credito</th>
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
                $AllEfectivo = 0;
                $AllBanco = 0;
                $AllPendiente = 0;
                $AllCredito = 0;
                while($tmp = mysqli_fetch_array($sql_tmp)){
                	$id_user = $tmp['user_id'];
					$efectivo = mysqli_fetch_array(mysqli_query($conn,"SELECT SUM(cantidad)  AS suma FROM pagos WHERE id_user=$id_user AND corte = 0 AND tipo_cambio='Efectivo'"));					
					$banco = mysqli_fetch_array(mysqli_query($conn,"SELECT SUM(cantidad)  AS suma FROM pagos WHERE id_user=$id_user AND corte = 0 AND tipo_cambio='Banco'"));
					$credito = mysqli_fetch_array(mysqli_query($conn,"SELECT SUM(cantidad)  AS suma FROM pagos WHERE id_user=$id_user AND corte = 0 AND tipo_cambio='Credito'"));
					// SACAMOS LA SUMA DE TODAS LAS DEUDAS Y ABONOS ....
				    $deuda = mysqli_fetch_array(mysqli_query($conn, "SELECT SUM(cantidad) AS suma FROM deudas_cortes WHERE cobrador = $id_user"));
				    $abono = mysqli_fetch_array(mysqli_query($conn, "SELECT SUM(cantidad) AS suma FROM pagos WHERE id_cliente = $id_user AND tipo = 'Abono Corte'"));
				    //COMPARAMOS PARA VER SI LOS VALORES ESTAN VACOIOS::
				    if ($deuda['suma'] == "") {
				      $deuda['suma'] = 0;
				    }elseif ($abono['suma'] == "") {
				      $abono['suma'] = 0;
				    }
				    //SE RESTAN DEUDAS DE ABONOS
				    $Saldo = $deuda['suma']-$abono['suma'];
					$Efectivo = $efectivo['suma']; 
					$Banco = $banco['suma']; 
					$Credito = $credito['suma']; 
					if ($Efectivo =='') {
						$Efectivo= 0;
					}
					if ($Banco =='') {
						$Banco= 0;
					}
					if ($Credito =='') {
						$Credito= 0;
					}					
                ?>
					<tr>
						<td><?php echo $tmp['firstname']; ?></td>
						<td><?php echo $tmp['lastname']; ?></td>
						<td>$<?php echo $Efectivo; ?></td>
						<td>$<?php echo $Saldo; ?></td>
						<td>$<?php echo $Banco; ?></td>	
						<td>$<?php echo $Credito; ?></td>	
					</tr>
					<?php
					$AllEfectivo = $AllEfectivo+$Efectivo;
					$AllBanco = $AllBanco+$Banco;
					$AllPendiente = $AllPendiente+$Saldo;
					$AllCredito = $AllCredito+$Credito;
                    }
                }
                ?>
					<tr>
						<td></td>
						<td><h5>TOTAL:</h5></td>
						<td><h5>$<?php echo $AllEfectivo; ?></h5></td>
						<td><h5>$<?php echo $AllPendiente; ?></h5></td>
						<td><h5>$<?php echo $AllBanco; ?></h5></td>
						<td><h5>$<?php echo $AllCredito; ?></h5></td>
					</tr>
				</tbody>				
		</table>
	</div>
</body>
</html>