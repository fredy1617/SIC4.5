<?php 
#INCLUIMOS EL ARCHIVO DONDE TEMENMOS EL API PARA LA CONEXION CON MIKROTIK
include_once('../API/api_mt_include2.php');
#INCLUIMOS EL ARCHIVO CON LA CONEXION A LA BASE DE DATOS
include('../php/conexion.php');

$id_cliente = $conn->real_escape_string($_POST['valorCliente']);

//DATOS DEL CLIENTE
$datos = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM clientes WHERE id_cliente=$id_cliente"));
//SACAMOS LA INFO DE LA COMUNIDAD
$id_comunidad = $datos['lugar'];
$comunidad = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM comunidades WHERE id_comunidad='$id_comunidad'"));
//SACAMOS LA INFO DEL SERVIDOR
$id_servidor = $comunidad['servidor'];
$serv = mysqli_fetch_array(mysqli_query($conn,"SELECT * FROM servidores WHERE id_servidor = $id_servidor"));

//////// INFORMACION DEL SERVIDOR
$ServerList = $serv['ip'] ; //ip_de_tu_API
$Username = $serv['user']; //usuario_API
$Pass = $serv['pass']; //contraseña_API
$Port = $serv['port']; //puerto_API

$URL =$ServerList.':2405/graphs/';

echo '<script>M.toast({html:"Esto puede tardar algunos segundos.", classes: "rounded"})</script>';

echo '<script>
		var a = document.createElement("a");
			a.target = "_blank";
			a.href = "http://'.$URL.'";
			a.click();
	</script>';
?>
