<?php 
#DEFINIMOS UNA ZONA HORARIA
date_default_timezone_set('America/Mexico_City');
#INCLUIMOS LA CONEXION A LA BASE DE DATOS PARA PODER HACER CUALQUIER MODIFICACION, INSERCION O SELECCION
include('../php/conexion.php');
#INCLUIMOS EL ARCHIVO DONDE TEMENMOS EL API PARA LA CONEXION CON MIKROTIK
include_once('../API/api_mt_include2.php');

#GENERAMOS UNA FECHA DEL DIA EN CURSO REFERENTE A LA ZONA HORARIA
$Fecha = date('Y-m-d');
#GENERAMOS LA HORA EXACTA A LA ORA DE USAR ESTE ARCHIVO SEGUN LA ZONA HORARIA DEFINIDA ANTERIORMENTE
$Hora = date('H:i:s');
 #HACEMOS UNA SELECCION DE TODOS LOS SERVIDORES REGISTRADOS LA TABLA servidores A ESECPION DEL SERVIDOR CON ID 16 YA QUE ESTA FUERA DE SERVICIO
$sql_servers = mysqli_query($conn, "SELECT * FROM servidores WHERE id_servidor != 16");
if(mysqli_num_rows($sql_servers) > 0){
	#RECORREMOS CON EL WHILE UNO POR UNO LA INFROMACION DE CADA SERVIDOR
    while($Servidor = mysqli_fetch_array($sql_servers)){
    	//////// INFORMACION DEL SERVIDOR
		$ServerList = $Servidor['ip'] ; //ip_de_tu_API
		$Username = $Servidor['user']; //usuario_API
		$Pass = $Servidor['pass']; //contraseña_API
		$Port = $Servidor['port']; //puerto_API

		$API = new routeros_api();
		$API->debug = false;
		#CONEXION A MICROTICK DEL SERVIDOR EN TURNO
		if ($API->connect($ServerList, $Username, $Pass, $Port)) {
			#BUSCAR UN ERROR DE LA MISMA IP en estatus Mikrotik
			$sql_eM = mysqli_query($conn, "SELECT * FROM errores_pings WHERE ip = '$ServerList' AND estatus = 'Mikrotik'");
			#VERIFICA SI ENCUENTRA ESTA IP en estatus Pediente
	   		if(mysqli_num_rows($sql_eM) > 0){
	   			#SI SE ENCONTRO UNA IP REGISTRADA 
	   			$error_pendiente_M = mysqli_fetch_array($sql_eM);
	   			$id_eM = $error_pendiente_M['id'];
	   			#CAMBIAMOS EL ESTATUS  DEL ERROR (ID ENCONTRADO) A Solucionado YA QUE PUDO HACER CONEXION CON EL MIKROTIK
		  		mysqli_query($conn, "UPDATE errores_pings SET estatus = 'Solucionado', hora_s = '$Hora', fecha_s = '$Fecha' WHERE id = '$id_eM'");
			}
			#HACER LA CONSULTA DE LAS CENTRALES QUE PERTENECEN AL SERVIDOR EN TURNO...
			$ID = $Servidor['id_servidor']; 
			$sql_Centrales =mysqli_query($conn, "SELECT * FROM centrales_pings INNER JOIN comunidades ON centrales_pings.comunidad = comunidades.id_comunidad WHERE centrales_pings.ip != '' AND comunidades.servidor = $ID");
	    	if(mysqli_num_rows($sql_Centrales) > 0){
	    		#SE RECORREN CADA UNA DE LAS CENTRALES PERTENECIENTES AL SERVIDOR EN TURNO UNA POR UNA
	        	while($Central = mysqli_fetch_array($sql_Centrales)){
	    			echo "Central".$Central['comunidad'];

	        		#COMENZAMOS A HACER PING SE LA CENTRAL EN TURNO EN LA CONSOLA DE MIKROTIK
	        		$IP = $Central['ip'];
	        		$API->write('/ping',false);
	    			$API->write('=address='.$IP,false);#IP A REALIZAR EL PING
	    			$API->write('=count=6',false);#NUMERO DE PINGS
	    			$API->write('=interval=1');
	    			$READ = $API->read(false);
	    			$ARRAY = $API->parse_response($READ);
	    			#SI PING ES MAYOR A 0 LOS ALGUNO DE LOS 3 PINGS SE REALIZAN Y ES UN PING CORRECTO
				    $PING = 0;
				    #RECORREMOS EL ARRAY CON LOS 3 PINGS
				    foreach ($ARRAY as $key => $value) {
				        #TOMAMOS EL PING A VER SI HAY PERDIDA O NO SI HACE PING INCREMENTA LA VARIABLE $PING EN 1 SI ALMENOS HACE 1 PING DE 3 SE TOMA COMO CORRECTO
				        if($value['packet-loss'] == 0){
				            $PING ++;// SI SE REALIZO EL PING A LA IP SE INCREMENTA EN 1
				        }
				    }
					#VERIFICAR SI UBO PERDIDAS DE PAQUETES AL HACER EL PING SI ALMENOS HACE 1 PING DE 3 SE TOMA COMO CORRECTO
					if($PING > 0){
				        echo "<br>HIZO PING IP: ".$IP."<br>";

	    				#SI SE REALIZO EL PING A LA IP
	    				#BUSCAR UN ERROR DE LA MISMA IP en estatus Pendiente
	    				$sql_e1 = mysqli_query($conn, "SELECT * FROM errores_pings WHERE ip = '$IP' AND estatus = 'Pendiente'");
	    				#VERIFICA SI ENCUENTRA ESTA IP en estatus Pediente
	       				if(mysqli_num_rows($sql_e1) > 0){
	       					#SI SE ENCONTRO ESTA IP REGISTRADA 
	       					$error_pendiente_conecto = mysqli_fetch_array($sql_e1);
	       					$id_e1 = $error_pendiente_conecto['id'];
	       					if ($error_pendiente_conecto['contador'] < 3) {
	       						#BORRARA ERROR PORQUE COMO EL CONTADOR ES MENOR A 3 NO SE CONSIDERA COMO ERROR
	       						mysqli_query($conn, "DELETE FROM errores_pings WHERE id = '$id_e1'");
	       					}else{
	       						#COMO EL CONTADOR ES MAYOR A 3 PERO REALIZO EL PING CAMBIAR ESTATUS A Solucionado
	       						mysqli_query($conn, "UPDATE errores_pings SET estatus = 'Solucionado', hora_s = '$Hora', fecha_s = '$Fecha' WHERE id = '$id_e1'");	
	       					}
	       				}
	    			}else{
				        echo "<br><br>--->>>>>>>NOOOO! HIZO PING IP: ".$IP."<br><br>";

	    				#NO SE REALIZO EL PING A LA IP
	    				#BUSCAR UN ERROR DE LA MISMA IP en estatus Pendiente
	    				$sql_e1 = mysqli_query($conn, "SELECT * FROM errores_pings WHERE ip = '$IP' AND estatus = 'Pendiente'");
	    				#VERIFICA SI ENCUENTRA ESTA IP en estatus Pediente
	       				if(mysqli_num_rows($sql_e1) > 0){
	       					#SI SE ENCUEBTRA YA REGISTRADA LA IP SOLO AUMENTAMOS EN 1 EL contador
	       					$error_pendiente = mysqli_fetch_array($sql_e1);
	       					$id_e1 = $error_pendiente['id'];
	       					mysqli_query($conn, "UPDATE errores_pings SET contador = contador+1 WHERE id = '$id_e1'");	
	       				}else{
	       					#CREAMOS LA DESCRIPCION DEL LA FALLA
	       					$Descripcion = 'No se logra conectar a la IP: '.$IP.', de la comunidad: '.$Central['nombre'].' perteneciente a la fibra: '.$Servidor['nombre'].', Hora: '.$Hora.', Fecha: '.$Fecha.', equipo: '.$Central['descripcion'];
	       					#REGISTRAMOS EN LA TABLA errores_pings UN NUEVO ERROR CON LA INFORMACION DE LA CENTRAL A LA QUE NO SE ACCEDIO
	       					mysqli_query($conn, "INSERT INTO errores_pings (descripcion, ip, estatus, fecha_e, hora_e, contador) VALUES('$Descripcion', '$IP', 'Pendiente', '$Fecha', '$Hora', 1)");
	       				}
	    			}
	        	}
	        }
	        #SE HACE LA DESCONECCION DE MIKROTIK DESPUES DE RECORRER TODAS LAS CENTRALES DEL SERVIDOR EN TURNO
			$API->disconnect();
		}else{
			#BUSCAR UN ERROR DE LA MISMA IP en estatus Mikrotik
			$sql_eM = mysqli_query($conn, "SELECT * FROM errores_pings WHERE ip = '$ServerList' AND estatus = 'Mikrotik'");
			#VERIFICA SI ENCUENTRA ESTA IP en estatus Pediente
	   		if(mysqli_num_rows($sql_eM) == 0){
	       		#CREAMOS LA DESCRIPCION DEL LA FALLA
				$Descripcion = 'No se ha podido hacer conexion al Mikrotik (TEST del Servidor: '.$Servidor['nombre'].'). Hora de falla: '.$Hora.' Fecha: '.$Fecha;
	       		#REGISTRAMOS EN LA TABLA errores_pings UN NUEVO ERROR CON LA INFORMACION DEL MICROTIK AL QUE NO SE ACCEDIO
		  		mysqli_query($conn, "INSERT INTO errores_pings (descripcion, ip, estatus, fecha_e, hora_e) VALUES('$Descripcion', '$ServerList', 'Mikrotik', '$Fecha', '$Hora')");
			}	
		}
    }
}