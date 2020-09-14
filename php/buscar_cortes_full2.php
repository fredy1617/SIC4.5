<?php
include('is_logged.php');

include('../php/conexion.php');
date_default_timezone_set('America/Mexico_City');
$Servidor = $conn->real_escape_string($_POST['valorServidor']);

$hoy = date('Y-m-d');
$serv = mysqli_fetch_array(mysqli_query($conn,"SELECT * FROM servidores WHERE id_servidor = $Servidor"));

echo "<h3> Servidor: ".$serv['nombre']." </h3>";
echo "/ip firewall address-list <br>";

$ARRAYCORTADOS = mysqli_query($conn, "SELECT * FROM clientes INNER JOIN comunidades ON clientes.lugar = comunidades.id_comunidad WHERE clientes.fecha_corte < '$hoy' AND clientes.instalacion = 1 AND comunidades.servidor = $Servidor");

while ($cortes = mysqli_fetch_array($ARRAYCORTADOS)) {
	#AGREGAMOS LA IP A LISTA DE MOROSOS
            	$id_user = $_SESSION['user_id'];
	$usuario = mysqli_fetch_array(mysqli_query($conn,"SELECT * FROM users WHERE user_id = $id_user"));
    $comment = 'No_Cliente: '.$cortes['id_cliente'].' Cortado por: '.$usuario['firstname'];
	echo "add address= ".$cortes['ip']." comment= ".$comment." list= MOROSOS <br>";
}
?>