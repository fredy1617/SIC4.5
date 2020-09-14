<?php
#INCLUIMOS EL ARCHIVO DONDE TEMENMOS EL API PARA LA CONEXION CON MIKROTIK
include_once('../API/api_mt_include2.php');
#INCLUIMOS EL ARCHIVO CON LA CONEXION A LA BASE DE DATOS
include('../php/conexion.php');
#INCLUIMOS EL PHP DONDE VIENE LA INFORMACION DEL INICIO DE SESSION
include('is_logged.php');
#GENERAMOS UNA FECHA DEL DIA EN CURSO REFERENTE A LA ZONA HORARIA
$Fecha = date('Y-m-d');


$Servidor = $conn->real_escape_string($_POST['valorServidor']);


$serv = mysqli_fetch_array(mysqli_query($conn,"SELECT * FROM servidores WHERE id_servidor = $Servidor"));

echo "<h3> Servidor: ".$serv['nombre']." </h3>";
//////// INFORMACION DEL SERVIDOR
$ServerList = $serv['ip'] ; //ip_de_tu_API
$Username = $serv['user']; //usuario_API
$Pass = $serv['pass']; //contraseña_API
$Port = $serv['port']; //puerto_API

$API = new routeros_api();
$API->debug = false;
#CONEXION A MICROTICK DEL SERVIDOR EN TURNO
if ($API->connect($ServerList, $Username, $Pass, $Port)) {
	echo "<br>Cortando Servicios...<br><br>";
	#SELECCIONAMOS TODOS LOS CLIENTES QUE TENGA DE FECHA DE CORTE MENOR A HOY QUE PERTENEZCAN AL SERVIDOR SELECCIONADO.
	$ARRAYCORTADOS = mysqli_query($conn, "SELECT * FROM clientes INNER JOIN comunidades ON clientes.lugar = comunidades.id_comunidad WHERE clientes.fecha_corte < '$Fecha' AND clientes.instalacion = 1 AND comunidades.servidor = $Servidor");
	#CONTAMOS CUANTOS CLIENTES SON
	$Morosos = mysqli_num_rows($ARRAYCORTADOS);
	#VERIFICAMOS SI EL CONTADOR DE CLEINTES MOROSOS ES MAYOR A 0
	if ($Morosos > 0) {
		$Estan = 0; $EstanStr = ''; $Agregar = 0; $AgregarStr = '';
		while ($cortes = mysqli_fetch_array($ARRAYCORTADOS)) {
			$IP = $cortes['ip'];//IP DEL CLIENTE
			#BUSCAMOS LA IP EN ADDRESS-LIST MOROSOS
			$API->write("/ip/firewall/address-list/getall",false);
            $API->write('?address='.$IP,false);
            $API->write('?list=MOROSOS',true);       
            $READ = $API->read(false);
            $ARRAY = $API->parse_response($READ); // AQUI SE GUARDA LA RESPUESTA DE SI ENCONTRO LA IP EN ADDRESS-LIST MOROSOS
            #VERIFICAMOS SI SE ENCONTRO
            if(count($ARRAY)>0){
            	#SI SE ENCUENTRA EN MOROSOS INCREMENTAMOS EN 1 $Estan 
                $Estan ++;
                $EstanStr.= $IP.'<br>';
            }else{ // si no existe lo creo;
            	#NO SE ENCUENTRA EN MOROSOS INCREMENTAMOS EN 1 $Agregar
            	$Agregar ++;
            	#AGREGAMOS LA IP A LISTA DE MOROSOS
            	$id_user = $_SESSION['user_id'];
				$usuario = mysqli_fetch_array(mysqli_query($conn,"SELECT * FROM users WHERE user_id = $id_user"));
            	$comment = 'No_Cliente:_'.$cortes['id_cliente'].'_Cortado_por:_'.$usuario['firstname'];
            	$API->write("/ip/firewall/address-list/add",false);
                $API->write('=address='.$IP,false);   // IP
                $API->write('=list=MOROSOS',false);       // lista
                $API->write('=comment='.$comment,true);  // comentario
                $READ = $API->read(false);
                $ARRAY = $API->parse_response($READ);
            	$AgregarStr .= $comment.' - '.$IP.'<br>';
            }
		}
		$Total = $Agregar+$Estan;
		echo "<h5><b>Clientes Por Cortar: ".$Morosos.' ||  Clientes Cortados: '.$Total."</b></h5>";
		#echo 'YA ESTAN EN MOROSOS:<br>'.$EstanStr;
		#echo 'SE AGREGARON A MOROSOS:<br>'.$AgregarStr;
	}else{
		echo "NO HAY CLIENTES MOROSOS!!...<br>";
	} 
    $API->disconnect();
}else{
	echo "ERROR DE CONEXION MICROTICK...<br>";
}
?>