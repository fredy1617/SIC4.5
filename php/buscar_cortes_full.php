<?php
include('../php/conexion.php');
date_default_timezone_set('America/Mexico_City');
$Servidor = $conn->real_escape_string($_POST['valorServidor']);

$hoy = date('Y-m-d');
$serv = mysqli_fetch_array(mysqli_query($conn,"SELECT * FROM servidores WHERE id_servidor = $Servidor"));

echo "<h3> Servidor: ".$serv['nombre']." </h3>";
echo "/ip firewall address-list <br>";

$ARRAYCORTADOS = mysqli_query($conn, "SELECT * FROM clientes  WHERE fecha_corte < '$hoy' AND instalacion = 1");
while ($cortes = mysqli_fetch_array($ARRAYCORTADOS)) {
	$id_comunidad = $cortes['lugar'];
                $comunidad = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM comunidades WHERE id_comunidad='$id_comunidad'"));
    $id_servidor = $comunidad['servidor'];
    $servidor = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM servidores WHERE id_servidor='$id_servidor'"));
                
	if($servidor['id_servidor']==$Servidor) {
		echo "add address= ".$cortes['ip']." comment= Numero_de_Cliente_".$cortes['id_cliente']." list= MOROSOS <br>";
	}
}
?>