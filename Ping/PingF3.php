<?php 
include('../php/conexion.php');
date_default_timezone_set('America/Mexico_City');
include_once('../API/api_mt_include2.php');

$Fecha = date('Y-m-d');
$Hora = date('H:i:s');

$servidorF3 = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM servidores WHERE id_servidor= 12"));

 //////// INFORMACION DEL SERVIDOR
$ServerList = $servidorF3['ip'] ; //ip_de_tu_API
$Username = $servidorF3['user']; //usuario_API
$Pass = $servidorF3['pass']; //contraseña_API
$Port = $servidorF3['port']; //puerto_API

echo $servidorF3['nombre'].'<br><br><br>';
$API = new routeros_api();
$API->debug = false;
#CONEXION A MICROTICK
if ($API->connect($ServerList, $Username, $Pass, $Port)) {
	#BUSCAR UN ERROR DE LA MISMA IP en estatus Mikrotik
	$sql_eM = mysqli_query($conn, "SELECT * FROM errores_pings WHERE ip = '$ServerList' AND estatus = 'Mikrotik'");
	#VERIFICA SI ENCUENTRA UN IP en estatus Pediente
   	if(mysqli_num_rows($sql_eM) > 0){
   		#SI SE ENCONTRO UNA IP REGISTRADA 
   		$error_pendiente_M = mysqli_fetch_array($sql_eM);
   		$id_eM = $error_pendiente_M['id'];
	  	mysqli_query($conn, "UPDATE errores_pings SET estatus = 'Solucionado', hora_s = '$Hora', fecha_s = '$Fecha' WHERE id = '$id_eM'");
	}
	$sql_Centrales =mysqli_query($conn, "SELECT * FROM centrales_pings INNER JOIN comunidades ON centrales_pings.comunidad = comunidades.id_comunidad WHERE centrales_pings.ip != '' AND (comunidades.servidor = 12 OR comunidades.servidor = 10)");
    if(mysqli_num_rows($sql_Centrales) == 0){
        echo '<h5 class="center">No hay centrales</h5>';
    }else{
        while($Central = mysqli_fetch_array($sql_Centrales)){
        	echo 'CENTRAL:'.$Central['nombre'].'<br>';
        	$IP = $Central['ip'];
        	$API->write('/ping',false);
			$API->write('=address='.$IP,false);#IP A REALIZAR EL PING
			$API->write('=count=1',false);
			$API->write('=interval=1');#NUMERO DE PINGS
			$READ = $API->read(false);
			$ARRAY = $API->parse_response($READ);

			#VERIFICAR SI UBO PERDIDAS DE PAQUETES AL HACER EL PING
			if($ARRAY[0]['packet-loss'] == 0){
				#SI SE REALIZO EL PING A LA IP
				echo "--->>>Conecto con la direccion: ".$ARRAY[0]['host']."<br>";
				#BUSCAR UN ERROR DE LA MISMA IP en estatus Pendiente
				$sql_e1 = mysqli_query($conn, "SELECT * FROM errores_pings WHERE ip = '$IP' AND estatus = 'Pendiente'");
				#VERIFICA SI ENCUENTRA UN IP en estatus Pediente
   				if(mysqli_num_rows($sql_e1) > 0){
   					#SI SE ENCONTRO UNA IP REGISTRADA 
   					$error_pendiente_conecto = mysqli_fetch_array($sql_e1);
   					$id_e1 = $error_pendiente_conecto['id'];
   					if ($error_pendiente_conecto['contador'] < 5) {
   						#BORRARA ERROR PORQUE COMO EL CONTADOR ES MENOR A 5 NO SE CONSIDERA COMO ERROR
   						mysqli_query($conn, "DELETE FROM errores_pings WHERE id = '$id_e1'");
   					}else{
   						#COMO EL CONTADOR ES MAYOR A 5 PERO REALIZO EL PING CAMBIAR ESTATUS A Solucionado
   						mysqli_query($conn, "UPDATE errores_pings SET estatus = 'Solucionado', hora_s = '$Hora', fecha_s = '$Fecha' WHERE id = '$id_e1'");	
   					}
   				}
			}else{
				#NO SE REALIZO EL PING A LA IP
				echo "--->>No se hizo conexión con al IP<br>";
				#BUSCAR UN ERROR DE LA MISMA IP en estatus Pendiente
				$sql_e1 = mysqli_query($conn, "SELECT * FROM errores_pings WHERE ip = '$IP' AND estatus = 'Pendiente'");
				#VERIFICA SI ENCUENTRA UN IP en estatus Pediente
   				if(mysqli_num_rows($sql_e1) > 0){
   					#SI SE ENCUEBTRA YA REGISTRADA LA IP SOLO AUMENTAMOS EN 1 EL contador
   					$error_pendiente = mysqli_fetch_array($sql_e1);
   					$id_e1 = $error_pendiente['id'];
   					mysqli_query($conn, "UPDATE errores_pings SET contador = contador+1 WHERE id = '$id_e1'");	
   				}else{
   					$Descripcion = 'No se logra conectar a la IP: '.$IP.', de la comunidad: '.$Central['nombre'].' perteneciente a la fibra: '.$servidorF3['nombre'].', Hora: '.$Hora.', Fecha: '.$Fecha.', equipo: '.$Central['descripcion'];
   					mysqli_query($conn, "INSERT INTO errores_pings (descripcion, ip, estatus, fecha_e, hora_e, contador) VALUES('$Descripcion', '$IP', 'Pendiente', '$Fecha', '$Hora', 1)");
   				}
			}
        }
    }
$API->disconnect();
}else{
	#BUSCAR UN ERROR DE LA MISMA IP en estatus Mikrotik
	$sql_eM = mysqli_query($conn, "SELECT * FROM errores_pings WHERE ip = '$ServerList' AND estatus = 'Mikrotik'");
	#VERIFICA SI ENCUENTRA UN IP en estatus Pediente
   	if(mysqli_num_rows($sql_eM) == 0){
		$Descripcion = 'No se ha podido hacer conexion al Mikrotik (TEST del Servidor: '.$servidorF3['nombre'].')';
	  	mysqli_query($conn, "INSERT INTO errores_pings (descripcion, ip, estatus, fecha_e, hora_e) VALUES('$Descripcion', '$ServerList', 'Mikrotik', '$Fecha', '$Hora')");
	}
}

mysqli_close($conn);
?>