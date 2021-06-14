<?php
#INCLUIMOS EL ARCHIVO DONDE TEMENMOS EL API PARA LA CONEXION CON MIKROTIK
include_once('../API/api_mt_include2.php');
#INCLUIMOS EL ARCHIVO CON LA CONEXION A LA BASE DE DATOS
include('../php/conexion.php');
#INCLUIMOS EL PHP DONDE VIENE LA INFORMACION DEL INICIO DE SESSION
include('is_logged.php');
#GENERAMOS UNA FECHA DEL DIA EN CURSO REFERENTE A LA ZONA HORARIA
$Fecha = date('Y-m-d');


$IDServidor = $conn->real_escape_string($_POST['id']);


$serv = mysqli_fetch_array(mysqli_query($conn,"SELECT * FROM servidores WHERE id_servidor = $IDServidor"));

//////// INFORMACION DEL SERVIDOR
$ServerList = $serv['ip'] ; //ip_de_tu_API
$Username = $serv['user']; //usuario_API
$Pass = $serv['pass']; //contraseña_API
$Port = $serv['port']; //puerto_API

$API = new routeros_api();
$API->debug = false;
$tabla = '';
#CONEXION A MICROTICK DEL SERVIDOR EN TURNO
if ($API->connect($ServerList, $Username, $Pass, $Port)){
	#VACIAMOS TODOS LOS QUEUES DE MIKROTIK
	$API->write('/queue/simple/print');
	#LO VACIAMOS EN UN ARRAY
	$READ = $API->read(false);
	$ARRAY = $API->parse_response($READ);	
	#CONTAMOPS EL TOTAL DE LOS QUEUES
	$count = count($ARRAY);
	echo $count;
	#SELECCIONAMOS TODOS LOS CLIENTES QUE TENGA DE FECHA DE CORTE MENOR A HOY QUE PERTENEZCAN AL SERVIDOR SELECCIONADO.
	$ARRAYCLIENTES = mysqli_query($conn, "SELECT *, clientes.nombre AS name FROM clientes INNER JOIN comunidades ON clientes.lugar = comunidades.id_comunidad WHERE clientes.instalacion = 1 AND comunidades.servidor = $IDServidor  ORDER BY id_cliente");
	#CONTAMOS CUANTOS CLIENTES SON
	#VERIFICAMOS SI EL CONTADOR DE CLEINTES MOROSOS ES MAYOR A 0
	if ( mysqli_num_rows($ARRAYCLIENTES) > 0) {
		while ($CLIENTE_S = mysqli_fetch_array($ARRAYCLIENTES)) {
			$IP_S = trim($CLIENTE_S['ip']);
			if($count>0){
			  $row = 0;
			  for ($i=0; $i < $count; $i++) { 
			   	$ip = explode('/', $ARRAY[$i]['target']);
			   	$IP_M = trim($ip[0]);
			   	if ($IP_M == $IP_S) {
			   	  $row ++;
			   	  $speed = explode('/', $ARRAY[$i]['max-limit']);
			   	  $nombre_m = $ARRAY[$i]['name'];
			   	  $velocidad = $ARRAY[$i]['max-limit'];
			   	  $IP_Mirotik = $IP_M;
			   	  $is = 'Activo';
			   	}
			   	if ($row == 0) {
			   	  $nombre_m = '';
			   	  $velocidad = '';
			   	  $IP_Mirotik = '';
			   	  $is = 'Desactivado';
			   	}
			  }
			}
			$tabla .=
					'<tr>
						<td>'.$CLIENTE_S['id_cliente'].'</td>
						<td>'.$CLIENTE_S['name'].'</td>
						<td>'.$CLIENTE_S['servicio'].'</td>
						<td>'.$CLIENTE_S['fecha_corte'].'</td>
						<td>PAGO</td>
						<td>'.$nombre_m.'</td>
						<td>'.$velocidad.'</td>
						<td>'.$IP_S.' / '.$IP_Mirotik.'</td>
						<td>'.$is.'</td>
					</tr>
					';
			
		}
	}
}
echo $tabla;
?>