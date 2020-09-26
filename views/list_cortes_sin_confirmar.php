<!DOCTYPE html>
<html>
<head>
	<title>SIC | Cortes Sin Confirmar</title>
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
			<h3 class="hide-on-med-and-down"> Cortes Sin Confirmar</h3>
  			<h5 class="hide-on-large-only"> Cortes Sin Confirmar</h5>
		</div>
		<!--  GENERAMOS UNA TABLA CON LA INFROMACION DE TODOS LOS CORTES SIN CONFIRMAR -->
		<table class="bordered highlight responsive-table" width="100%">
			<thead>
				<tr>
					<th>#ID</th>
					<th>Cobrador</th>
					<th>Efectivo</th>
					<th>Banco</th>
					<th>Credito</th>
					<th>Fecha</th>
					<th>Realizo</th>
					<th>Estatus</th>
				</tr>
			</thead>
			<tbody>
			<?php
			#SELECCIONAMOS TODOS LOS CORTES QUE NO TENGAN CONFRIMRACION EN 0
			$sql = mysqli_query($conn, "SELECT * FROM cortes WHERE confirmar = 0");
			#VERIFICAMOS SI SE ENCONTRARON CORTES SIN CONFIRMAR
			if (mysqli_num_rows($sql) <= 0) {
				#SI NO SE ENCUENTRAN CORTES SIN CONFIRMAR MOSTRAR MENSAJE
				echo "<center><b><h3>No se encontraron cortes</h3></b></center>";
			}else{
				#SI SE ENCUENTRAN CORTES SIN CONFIRMAR RECORREMOS UNO POR UNO CON EL WHILE
				while ($resultados = mysqli_fetch_array($sql)) {	
					$id_usuario = $resultados['usuario'];//ID DEL USUARIO A QUIEN SE LE HIZO EL CORTE (COBRADOR)
					$usuario = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM users WHERE user_id = $id_usuario"));
					#LOS VAMOS IMPRIMIENDO FILA POR FILA
				?>
					<tr>
						<td><?php echo $resultados['id_corte']; ?></td>
						<td><?php echo $usuario['firstname']; ?></td>		
						<td>$<?php echo $resultados['cantidad']; ?></td>					
						<td>$<?php echo $resultados['banco']; ?></td>					
						<td>$<?php echo $resultados['credito']; ?></td>					
						<td><?php echo $resultados['fecha']; ?></td>
						<td><?php echo $resultados['realizo']; ?></td>
						<td><span class="new badge red" data-badge-caption=""><?php echo ($resultados['confirmar'] == 0 ) ? "PENDIENTE":"CONFIRMADO"; ?></span></td>
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