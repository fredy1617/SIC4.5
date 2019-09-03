<?php
include('conexion.php');
include_once('../API/api_mt_include2.php');
$IdServidor = $conn->real_escape_string($_POST['valorIdServidor']);

$servidor = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM servidores WHERE id_servidor='$IdServidor'"));

 //////// configura tus datos
$ServerList = $servidor['ip'] ; //ip_de_tu_API
$Username = $servidor['user']; //usuario_API
$Pass = $servidor['pass']; //contraseña_API
$Port = $servidor['port']; //puerto_API

$API = new routeros_api();
$API->debug = false;
if ($API->connect($ServerList, $Username, $Pass, $Port)) {
	echo '<script>M.toast({html:"Conexion exitosa al Mikrotik (TEST del Servidor: '.$servidor['nombre'].')", classes: "rounded"})</script>';
}else{
	echo '<script>M.toast({html:"No se ha podido hacer conexión al Mikrotik (TEST del Servidor: '.$servidor['nombre'].')", classes: "rounded"})</script>';
}

?>