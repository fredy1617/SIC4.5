<?php

include('../php/conexion.php');
date_default_timezone_set('America/Mexico_City');
$Servidor = $conn->real_escape_string($_POST['valorServidor']);

$hoy = date('Y-m-d');
$serv = mysqli_fetch_array(mysqli_query($conn,"SELECT * FROM servidores WHERE id_servidor = $Servidor"));

echo "<h3> Servidor: ".$serv['nombre']." </h3>";
echo "/ip firewall address-list <br>";

$ARRAYCORTADOS = mysqli_query($conn, "SELECT * FROM clientes INNER JOIN comunidades ON clientes.lugar = comunidades.id_comunidad WHERE clientes.fecha_corte < '$hoy' AND clientes.instalacion = 1 AND comunidades.servidor = $Servidor");

while ($cortes = mysqli_fetch_array($ARRAYCORTADOS)) {
	echo "add address= ".$cortes['ip']." comment= Numero_de_Cliente_".$cortes['id_cliente']."FREDO ESTUVO AQUI list= MOROSOS <br>";
}
?>