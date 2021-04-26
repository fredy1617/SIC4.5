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

//////// INFORMACION DEL SERVIDOR //////////
$ServerList = $serv['ip'] ; //ip_de_tu_API
$Username = $serv['user']; //usuario_API
$Pass = $serv['pass']; //contraseña_API
$Port = $serv['port']; //puerto_API

$API = new routeros_api();
$API->debug = false;
$data = new StdClass();

#CONEXION A MICROTICK DEL SERVIDOR EN TURNO
if ($API->connect($ServerList, $Username, $Pass, $Port)){
		#VACIAMOS TODOS LOS QUEUES DE MIKROTIK EN UN ARRAY
		$ARRAY1 =$API->comm('/queue/simple/print');
		#CONTAMOS EL TOTAL DE LOS QUEUES
		$count = count($ARRAY1);
		#COMO SI ENCONTRAMOS QUEUES PONEMOS EL ESTADO EN 1
		$data->estado = 1;
		if($count>0){
			#SI ENCONTRAMOS MAS DE 1 QUEUE LSO RECORREMOS CON UN CICLO FOR
			for ($i=0; $i < $count; $i++) { 
				#INGRESAMOS LOS VALORES QUE NESECITAMOS A NUESTRA StdClass ($data) (id, ip, name, up, down)
				$ip = explode('/', $ARRAY1[$i]['target']);
				$speed = explode('/', $ARRAY1[$i]['max-limit']);
				$data->id[] = $ARRAY1[$i]['.id'];
				$data->ip[] = $ip[0];
				$data->name[] = $ARRAY1[$i]['name'];
				$data->up[] = $speed[0]/1000;
				$data->down[] = $speed[1]/1000;		   	
			}
		}

		#EL MIKROTIK AVECES NO DA LA INFORMACION COMPLETA, REPETIMOS EL PROCESOS 210 VECES PARA EASR SEGUROS DE QUE SON TODOS LOS QUEUES
		for ($a=0; $a < 210; $a++) { 
			#VACIAMOS TODOS LOS QUEUES DE MIKROTIK EN UN ARRAY
			$ARRAY2 =$API->comm('/queue/simple/print');
			#CONTAMOS EL TOTAL DE LOS QUEUES
			$count = count($ARRAY2);
			if($count>0){
				#SI ENCONTRAMOS MAS DE 1 QUEUE LSO RECORREMOS CON UN CICLO FOR
				for ($x=0; $x < $count; $x++) { 
					$Registra = true;
					#COMO ES REPETICION COMPARAMOS CON LOS QUEUES QUE YA TENEMOS REGISTRADOS PARA VER SI LO REGISTRAREMOS O NO (ES NUEVO)
					for ($b=0; $b < count($data->id); $b++) { 
						if ($data->id[$b] ==  $ARRAY2[$x]['.id']) {
							$Registra = false;
						}
					}
					#VERIFICAMOS SI ES UN NUEVO QUEUE (NO SE REPITE SI id)
					if ($Registra) {
						#INGRESAMOS LOS VALORES QUE NESECITAMOS A NUESTRA StdClass ($data) (id, ip, name, up, down)
						$ip = explode('/', $ARRAY2[$x]['target']);
					   	$speed = explode('/', $ARRAY2[$x]['max-limit']);
					   	$data->id[] = $ARRAY2[$x]['.id'];
					   	$data->ip[] = $ip[0];
					   	$data->name[] = $ARRAY2[$x]['name'];
					   	$data->up[] = $speed[0]/1000;
					   	$data->down[] = $speed[1]/1000;
					}  	
				}
			}
		}
		#CONTAMOS CUANTOS REGISTROS EN TOTAL FUERON (QUEUES) y lo guardamos en nuestra StdClass
		$data->c = count($data->id);		  
} else {
	#SI NO ENTRA LE COLOCAMOS AL ESTADO 0
	$data->estado = 0;
}
#VERIFICAMOS SI EL ESTADO ESTA EN 1 PARA PODER INSERTAR LOS QUEUES EN LA TABLA tmp_mikrotik
if(!$data->estado){
	#echo "No se encontraron";
}else{
	#echo "<h1 class = 'red-text'>Se encontraron: ".$data->c;
	#$Tabla = '';
	#CON UN CICLO RECORREMOS EL StdClass PARA TOMAR UNO POR UNO (QUEUE) E INSERTARLO A LA TABLA tmp_mikrotik
	for ($i=0; $i < $data->c; $i++) { 
		#$Tabla .= '<tr><td>'.$i.'</td><td>'.$data->id[$i].'</td><td>'.$data->name[$i].'</td><td>'.$data->ip[$i].'</td><td>'.$data->up[$i].'K </td><td> '.$data->down[$i].'K</td></tr>';
		$id = $data->id[$i];// id del queue en turno
		$ip = $data->ip[$i];// ip del queue en turno
		# COMPARAMOS LOS DATOS DEL QUEUE PARA VER SI NO HAY UNO IGUAL REGISTRADO Y NO REPETIR INFORMACION
		if(mysqli_num_rows(mysqli_query($conn, "SELECT * FROM tmp_mikrotik WHERE id = '$id' AND ip = '$ip' AND servidor = '$IDServidor'"))==0){
			$nombre = $data->name[$i];// nombre del queue en turno
			$subida = $data->up[$i];// velocidad de subida del queue en turno
			$bajada = $data->down[$i];// velocidad de bajada dek queue en tuno
			#SI NO HAY REPETIDOS PORCEDEMOS A HACER EL INSER DEL QUEUE EN TURNO
			mysqli_query($conn,"INSERT INTO tmp_mikrotik (id, nombre, ip, subida, bajada, servidor) VALUES ('$id', '$nombre', '$ip', '$subida', '$bajada', '$IDServidor')");
		}
	}
	?>
    <script>
      //REFRESCAMOS LA PAGINA PRINCIPAL PERO ENVIAMOS EL ID DEL SERVIDOR PARA PODER MOSTRAR EL BOTON COMPARAR
      id = <?php echo $IDServidor; ?>;
      var a = document.createElement("a");
        a.href = "../views/sistema_mikrotik.php?id="+id;
        a.click();
    </script>
	<?php
	#var_dump($data->id);
	#echo $Tabla;
}
?>