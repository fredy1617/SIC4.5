<?php 
include('../php/conexion.php');
date_default_timezone_set('America/Mexico_City');
include_once('../API/api_mt_include2.php');

$Fecha = date('Y-m-d');
$Hora = date('H:i:s');

$IdServidor = 11;

$servidor = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM servidores WHERE id_servidor='$IdServidor'"));

 //////// configura tus datos
$ServerList = $servidor['ip'] ; //ip_de_tu_API
$Username = $servidor['user']; //usuario_API
$Pass = $servidor['pass']; //contraseña_API
$Port = $servidor['port']; //puerto_API

$API = new routeros_api();
$API->debug = false;
if ($API->connect($ServerList, $Username, $Pass, $Port)) {
	$API->write('/ping',false);
	$API->write('=address=192.168.1.1',false); 
	$API->write('=count=1',false);
	$API->write('=interval=1');
	$READ = $API->read(false);
	$ARRAY = $API->parse_response($READ);
	if($ARRAY[0]['packet-loss'] == 0){
		echo "Conecto con la direccion: ".$ARRAY[0]['host'];
	}else{
		echo "Nel";
	}
	$API->write('/ping',false);
	$API->write('=address=8.8.8.8',false); 
	$API->write('=count=1',false);
	$API->write('=interval=1');
	$READ = $API->read(false);
	$ARRAY = $API->parse_response($READ);
	if($ARRAY[0]['packet-loss'] == 0){
		echo "Conecto con la direccion: ".$ARRAY[0]['host'];
	}else{
		echo "Nel";
	}
	$API->write('/ping',false);
	$API->write('=address=189.197.184.252',false); 
	$API->write('=count=1',false);
	$API->write('=interval=1');
	$READ = $API->read(false);
	$ARRAY = $API->parse_response($READ);
	if($ARRAY[0]['packet-loss'] == 0){
		echo "Conecto con la direccion: ".$ARRAY[0]['host'];
	}else{
		echo "Nel";
	}
	$API->write('/ping',false);
	$API->write('=address=19.197.184.252',false); 
	$API->write('=count=1',false);
	$API->write('=interval=1');
	$READ = $API->read(false);
	$ARRAY = $API->parse_response($READ);
	if($ARRAY[0]['packet-loss'] == 0){
		echo "Conecto con la direccion: ".$ARRAY[0]['host'];
	}else{
		echo "No hizo conexion con la direccion: ".$ARRAY[0]['host'];
	}

}else{
	echo '<script>M.toast({html:"No se ha podido hacer conexión al Mikrotik (TEST del Servidor: '.$servidor['nombre'].')", classes: "rounded"})</script>';
}

$API->disconnect();

mysqli_close($conn);
?>
