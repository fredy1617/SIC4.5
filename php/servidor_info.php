<?php
#INCLUIMOS EL ARCHIVO DONDE TEMENMOS EL API PARA LA CONEXION CON MIKROTIK
include_once('../API/api_mt_include2.php');
#INCLUIMOS EL ARCHIVO CON LA CONEXION A LA BASE DE DATOS
include('../php/conexion.php');
#INCLUIMOS EL PHP DONDE VIENE LA INFORMACION DEL INICIO DE SESSION

$IDServidor = $conn->real_escape_string($_POST['id']);

$serv = mysqli_fetch_array(mysqli_query($conn,"SELECT * FROM servidores WHERE id_servidor = $IDServidor"));

//////// INFORMACION DEL SERVIDOR
$ServerList = $serv['ip'] ; //ip_de_tu_API
$Username = $serv['user']; //usuario_API
$Pass = $serv['pass']; //contraseña_API
$Port = $serv['port']; //puerto_API

$API = new routeros_api();
$API->debug = false;
#CONEXION A MICROTICK DEL SERVIDOR EN TURNO
if ($API->connect($ServerList, $Username, $Pass, $Port)) {
	$API->write('/system/resource/print');

	$READ = $API->read(false);
	$ARRAY = $API->parse_response($READ);
		   
	$API->disconnect();

	$Modelo = $ARRAY[0]['board-name'];
	$UpTime = $ARRAY[0]['uptime'];
	$CPU = $ARRAY[0]['cpu-load'];
	$Version = $ARRAY[0]['version'];
	?><br><br>
	<div class="center col s12 l3 m3">
		<a class="col s12 waves-effect waves-light btn-large blue"><i class="material-icons left">local_movies</i>CPU : <?php echo $CPU; ?>%</a>
	</div>
	<div class="center col s12 l3 m3">
		<a class="col s12 waves-effect waves-light btn-large green"><i class="material-icons left">subtitles</i>Model: <?php echo $Modelo; ?></a>
	</div>
	<div class="center col s12 l3 m3">
		<a class="col s12 waves-effect waves-light btn-large blue"><i class="material-icons left">schedule</i>UpTime: <?php echo $UpTime; ?></a>
	</div>
	<div class="center col s12 l3 m3">
		<a class="col s12 waves-effect waves-light btn-large green"><i class="material-icons left">info_outline</i>V.  <?php echo $Version; ?></a>
	</div>
	<?php
}else{
	?><br><br>
	<div class="col s3"><br></div>
	<div class="center col s12 l6 m6">
		<a class="col s12 waves-effect waves-light btn-large red"><i class="material-icons left">info_outline</i>ERROR DE CONEXION A MICROTICK...</a>
	</div>
	<?php
}
?>