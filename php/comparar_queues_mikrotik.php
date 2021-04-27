<?php
#INCLUIMOS EL ARCHIVO CON LA CONEXION A LA BASE DE DATOS
include('../php/conexion.php');
#INCLUIMOS EL PHP DONDE VIENE LA INFORMACION DEL INICIO DE SESSION
include('is_logged.php');

$IDServidor = $conn->real_escape_string($_POST['id']);
$Tabla_si = '';
$Tabla_no = '';
#SELECCIONAMOS TODOS LOS QUEUES QUE PERTENEZCAN AL SERVIDOR SELECCIONADO.
$ARRAYQUEUES = mysqli_query($conn, "SELECT * FROM tmp_mikrotik  WHERE servidor = '$IDServidor' ");
#VERIFICAMOS SI HAY QUEUES PERTENECIENTRES A ESTE SERVIDOR
if (mysqli_num_rows($ARRAYQUEUES)>0) {
	//////// INFORMACION DEL SERVIDOR //////////
	$serv = mysqli_fetch_array(mysqli_query($conn,"SELECT * FROM servidores WHERE id_servidor = $IDServidor"));
	#SI HAY MAS DE 0 QUEUES LOS RECORREMOS UNO POR UNO CON UN WHILE
	while ($QUEUE = mysqli_fetch_array($ARRAYQUEUES)) {
		$IP_M = $QUEUE['ip'];// IP DEL QUEUE EN TURNO
		$sql_CLIENTE = mysqli_query($conn, "SELECT *FROM clientes WHERE ip = '$IP_M'");

		include('../php/filtro.php');
			
		if (mysqli_num_rows($sql_CLIENTE)>0) {
			#SI ENCUENTRA LA IP EN LA TABLA CLIENTES AGREGAMOS UNA FILA A LA TABLA SI
			$CLIENTE = mysqli_fetch_array($sql_CLIENTE);

			$Tabla_si .= '
				<tr>
					<td>'.$CLIENTE['id_cliente'].'</td>
					<td>'.$CLIENTE['nombre'].'</td>
					<td>'.$CLIENTE['servicio'].' '.$IP_M.'</td>
					<td>'.$QUEUE['id'].'</td>
					<td>'.$QUEUE['subida'].'/'.$QUEUE['bajada'].'</td>
				</tr>';			
		}else if ($filtro) {
		}else{
			#SI NO ENCUENTRA LA IP EN LA TABLA CLIENTES AGREGAMOS UNA FILA A LA TABLA NO
			$Tabla_no .= '
				<tr>
					<td>-</td>
					<td><b>NO SE ENCUENTRA<b></td>
					<td>'.$IP_M.'</td>
					<td>'.$QUEUE['id'].'</td>
					<td>'.$QUEUE['nombre'].'</td>
					<td>'.$QUEUE['subida'].'/'.$QUEUE['bajada'].'</td>
				</tr>';
		}
	}// FIN WHILE QUEUES
}// FIN IF QUEUES
?>

<div class="row">
	<div class="col s12 m6 l6">
	  <h4 class="blue-text">Encontrados en el Sistema</h4>
	  <table class="striped centered responsive-table">
		<thead>
			<tr>
			    <th colspan="3">Info Sistema</th>
			    <th colspan="2">Info Mikrotik</th>
			</tr>
			<tr>
			    <th>#</th>
			    <th>Nombre</th>
			    <th>Servicio / IP</th>
			    <th>#</th>
			    <th>Velocidad Up/Down</th>
			</tr>
		</thead>
		<tbody>
		<?php
			echo $Tabla_si;
		?>
		</tbody>
	  </table>
	</div>
	<div class="col s12 m6 l6">
	  <h4 class="red-text">Sin Registro en el Sistema</h4>
	  <table class="striped centered responsive-table">
		<thead>
			<tr>
			    <th colspan="3">Info Sistema</th>
			    <th colspan="3">Info Mikrotik</th>
			</tr>
			<tr>
			    <th>#</th>
			    <th>Nombre</th>
			    <th>Servicio / IP</th>
			    <th>#</th>
			    <th>Nombre</th>
			    <th>Velocidad Up/Down</th>
			</tr>
		</thead>
		<tbody>
		<?php
			echo $Tabla_no;
		?>
		</tbody>
	  </table>
	</div>
</div>