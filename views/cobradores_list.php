<!DOCTYPE html>
<html>
<head>
	<title>SIC | Cobradores</title>
<?php
  #INCLUIMOS EL ARCHIVO DONDE ESTA LA BARRA DE NAVEGACION DEL SISTEMA
  include('fredyNav.php');
  #INCLUIMOS EL ARCHIVO EL CUAL HACE QUE SOLOS LOS USUARIOS QUE SEAN ADMINISTRADORES PUEDAN ACCEDER A ESTA LISTA
  include('../php/superAdmin.php');
?>
</head>
<body>
	<div class="container">
		<div class="row">
			<h3 class="hide-on-med-and-down"> Cobradores</h3>
  			<h5 class="hide-on-large-only"> Cobradores</h5>
		</div>
		<!--  GENERAMOS UNA TABLA CON LA INFROMACION DE TODOS LOS CUSUARIOS QUE RECIBAN PAGOS DE ALGUN TIPO -->
		<table class="bordered highlight responsive-table" width="100%">
			<thead>
				<tr>
					<th>#</th>
					<th>Cobrador</th>
					<th>Usuario</th>
					<th>Saldo</th>
				</tr>
			</thead>
			<tbody>
			<?php
			#SELECCIONAMOS TODOS LOS USUARIOS QUE RECIBAN PAGOS DE ALGUN TIPO
			$sql = mysqli_query($conn, "SELECT * FROM users WHERE area IN ('Cobrador' , 'Oficina') OR user_id IN (10, 25, 28, 56, 59, 41, 26, 49, 68)");
			#VERIFICAMOS SI SE ENCONTRARON USUARIOS
			if (mysqli_num_rows($sql) <= 0) {
				#SI NO SE ENCUENTRAN USUARIOS MOSTRAR MENSAJE
				echo "<center><b><h3>No se encontraron cobradores</h3></b></center>";
			}else{
				#SI SE ENCUENTRAN USUARIOS RECORREMOS UNO POR UNO CON EL WHILE
				while ($resultados = mysqli_fetch_array($sql)) {	
					#LOS VAMOS IMPRIMIENDO FILA POR FILA
				?>
					<tr>
						<td><?php echo $resultados['user_id']; ?></td>
						<td><?php echo $resultados['firstname'].' '.$resultados['lastname']; ?></td>		
						<td><?php echo $resultados['user_name']; ?></td>		
						<td><form method="post" action="../views/saldo_cobrador.php"><input name="id" type="hidden" value="<?php echo $resultados['user_id']; ?>"><button type="submit" class="btn-floating btn-tiny waves-effect waves-light pink"><i class="material-icons">attach_money</i></button></form></td>		
					</tr>
				<?php
				}//FIN WHILE
			}#FIN ELSE
			?>
			</tbody>				
		</table><br>
	</div><!--  FIN DE CONTAINER -->
</body>
</html>