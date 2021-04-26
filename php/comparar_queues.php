<?php
#INCLUIMOS EL ARCHIVO DONDE TEMENMOS EL API PARA LA CONEXION CON MIKROTIK
include_once('../API/api_mt_include2.php');
#INCLUIMOS EL ARCHIVO CON LA CONEXION A LA BASE DE DATOS
include('../php/conexion.php');
#INCLUIMOS EL PHP DONDE VIENE LA INFORMACION DEL INICIO DE SESSION
include('is_logged.php');

$IDServidor = $conn->real_escape_string($_POST['id']);
?>

<div class="row">
	<table class="striped centered responsive-table">
		<thead>
			<tr>
			    <th colspan="4">Info Sistema</th>
			    <th colspan="5">Info Mikrotik</th>
			</tr>
			<tr>
			    <th>#</th>
			    <th>Nombre</th>
			    <th>Servicio</th>
			    <th>Fecha Corte</th>
			    <th>#</th>
			    <th>Nombre</th>
			    <th>Velocidad Up/Down</th>
			    <th>IP Sistema/Mikrotik</th>
			    <th>ESTATUS</th>
			</tr>
		</thead>
		<tbody>
		<?php
		#SELECCIONAMOS TODOS LOS CLIENTES QUE PERTENEZCAN AL SERVIDOR SELECCIONADO.
		$ARRAYCLIENTES = mysqli_query($conn, "SELECT *, clientes.nombre AS name FROM clientes INNER JOIN comunidades ON clientes.lugar = comunidades.id_comunidad WHERE clientes.instalacion = 1 AND comunidades.servidor = $IDServidor  ORDER BY id_cliente");
		#VERIFICAMOS SI HAY CLIENTES PERTENECIENTRES A ESTE SERVIDOR
		if (mysqli_num_rows($ARRAYCLIENTES)>0) {
			$serv = mysqli_fetch_array(mysqli_query($conn,"SELECT * FROM servidores WHERE id_servidor = $IDServidor"));
			//////// INFORMACION DEL SERVIDOR //////////
			$ServerList = $serv['ip'] ; //ip_de_tu_API
			$Username = $serv['user']; //usuario_API
			$Pass = $serv['pass']; //contraseña_API
			$Port = $serv['port']; //puerto_API
			$API = new routeros_api();
			$API->debug = false;
			#CONEXION A MICROTICK DEL SERVIDOR
			if ($API->connect($ServerList, $Username, $Pass, $Port)){ $Conecta = true; }else{ $Conecta = false;	}

			#SI HAY MAS DE 0 CLIENTES LOS RECORREMOS UNO POR UNO CON UN WHILE
			while ($CLIENTE_S = mysqli_fetch_array($ARRAYCLIENTES)) {
				$IP_S = trim($CLIENTE_S['ip']);// ip del cliente en turno
				#Buscamos en la tabla tmp_mikrotik si hay algun queue con la misma ip que el cliente en turno
				$sql_QUEUE = mysqli_query($conn, "SELECT * FROM tmp_mikrotik WHERE servidor = '$IDServidor' AND ip = '$IP_S'");
				$id = '';
				$Velocidad = '';
				if (mysqli_num_rows($sql_QUEUE)>0) {
					#SI LO ENCUENTRA VACIAMOS TODA LA INFORMACION DEL QUEUE EN VARIABLES RESPECTIVAMENTE
					$QUEUE = mysqli_fetch_array($sql_QUEUE);
					$id = $QUEUE['id'];
					$Name_M = $QUEUE['nombre'];
					$Velocidad = $QUEUE['subida'].'/'.$QUEUE['bajada'];
					$IP_M = $QUEUE['ip'];
					$Estatus = '<b class = "green-text">ACTIVO</b>';
				}else if ($Conecta) {
					for ($x = 0; $x < 10; $x++) {
					  $API->write("/ip/firewall/address-list/getall",false);
				      $API->write('?address='.$IP_S,false);
				      $API->write('?list=MOROSOS',true);       
				      $READ = $API->read(false);
				      $ARRAY = $API->parse_response($READ); // busco si ya existe
					  if (count($ARRAY)>0) {
					  	$encontro = true;
					    break;
					  }else{
					  	$encontro = false;
					  }//FIN ELSE BREAK
					}//FIN DE FOR X10					
			        if(count($ARRAY)>0){ 
						$id = $ARRAY[0][".id"];
						$Name_M = $ARRAY[0]["comment"];
						$IP_M = $ARRAY[0]["address"];
						$Estatus = '<b class = "blue-text">INACTIVO</b>';
					}else{
						$Name_M = '//// NO SE ENCUENTRA EN MIKROTIK /////';
						$IP_M = '';
						$Estatus = '<b class = "red-text">SIN REGISTRO</b>';
					}//FIN ELSE COUNT
				}else{					
					$Name_M = 'SIN CONEXION MIKROTIK';
					$IP_M = 'POSIBLE:';
					$Estatus = '<b class = "red-text">INACTIVO/ SIN REGISTRO</b>';
				}//FIN ELSE CONECTA
				?>
				<tr>
					<td><?php echo $CLIENTE_S['id_cliente']; ?></td>
					<td><?php echo $CLIENTE_S['name']; ?></td>
					<td><b><?php echo $CLIENTE_S['servicio']; ?></b></td>
					<td><?php echo $CLIENTE_S['fecha_corte']; ?></td>
					<td><?php echo $id; ?></td>
					<td><?php echo $Name_M; ?></td>
					<td><?php echo $Velocidad; ?></td>
					<td><?php echo $IP_S.'/'.$IP_M; ?></td>
					<td><?php echo $Estatus; ?></td>
				</tr>
				<?php
			}// FIN WHILE CLIENTES
		}// FIN IF CLIENTES
		?>
		</tbody>
	</table>
</div>