<?php
#INCLUIMOS EL ARCHIVO CON LOS DATOS Y CONEXXION A LA BASE DE DATOS
include('../php/conexion.php');
#INCLUIMOS EL ARCHIVO CON LA INFORMACION DE LOS CHATS BOT
include('../php/infoBots.php');
#FUNCION QUE SIRVE PARA ENVIAR EL MENSAJE A TELEGRAM DESDE EL BOT RESUMEN_FALLAS
function sendMessage($id, $msj, $website){
    #CREAMOS EL URL AL CUAL SE ENVIARA EL MENSAJE CON EL ID DEL CHAT QUE RECIBIMOS Y EL MENSAJE QUE HAY QUE ENVIAR
    $url = $website.'/sendMessage?chat_id='.$id.'&parse_mode=HTML&text='.urlencode($msj);
    #SE ENCARGA DE IR A EL URL Y ENVIAR EL MENSAJE DESDE EL BOT
    file_get_contents($url);
}
    
#SELECCIONAMOS TODAS LAS CENTRALES REGISTRADAS EN LA  TABLA CENTRALES PINGS
$Centrales = mysqli_query($conn, "SELECT * FROM centrales_pings");

#VERIFICANMOS SI HAY CENRALES  
if (mysqli_num_rows($Centrales)>0) {
	$MSJ = "";//DEFINIMOS LA VARIABLE MSJ EN LA CUAL IREMOS AGREGANDO Y CREANDO EL FORMATO QUE ENVIARA...
	#DEFINIMOS UNA ZONA HORARIA
	date_default_timezone_set('America/Mexico_City');

	#CREAMOS UNA FECHA Y UNA HORA SEGUN LA ZONA HORARIA DE HOY Y JUSTO LA HORA DE EJECUCION DEL ARCHIVO
	$Fecha = date('Y-m-d');
	$Hora = date('H:i:s');

	#CREAMOS UNA HORA ANTERIOR A LA DEL JUSTO DEL MOMENTO -8 HORAS PARA EL RANGO DE 6 HORAS
	$nuevahora = strtotime('-8 hour', strtotime($Hora));
    $Hora_8 = date('H:i:s', $nuevahora);
    $MSJ.= "<b>ERRORES DE ".$Hora_8." -A- ".$Hora."</b> \n\n";//LO AGREGAMOS AL FORMATO DEL MENSAJE
    #RECORREMOS LAS CENTRALES UNA POR UNA
	while ($Central = mysqli_fetch_array($Centrales)) {

		#SQL DE SELECCION DE LOS ERRORES DE LA FECHA DE HOY Y CON UN RANGO DE 8 HORAS Y CON LA IP DE LA COMUNIDAD EN TURNO
		$ip = $Central['ip'];//IP DE LA CENTRAL EN TURNO
		$sql_Errores = mysqli_query($conn, "SELECT * FROM errores_pings WHERE (fecha_e = '$Fecha' AND hora_e >= '$Hora_8' AND hora_e <= '$Hora' AND ip = '$ip' ) OR (estatus = 'Pendiente' AND ip = '$ip') ORDER BY hora_e");

		#CONTRAMOS LOS ERRORES ENCOTRADOS
		$Errores = mysqli_num_rows($sql_Errores);

		#SI ENCONTRAMOS MAS DE 0 ERRORES
		if ($Errores >0 ) {
			$id_comunidad = $Central['comunidad'];//SACAMOS EL ID DE LA COMUNIDAD 
			#TOMAMOS EL NOMBRE DE LA COMUNIDAD EN TURNO CON EL ID DE LA COMUNIDAD
			$comunidad = mysqli_fetch_array(mysqli_query($conn, "SELECT nombre FROM comunidades WHERE id_comunidad = $id_comunidad"));
			#ANTES DE RECORRER MOSTRAR INFORMACION DE LA COMUNIDAD NOMBRE, IP, DESCRIPCION, NUMERO DE ERRORES
			$MSJ.= "<b>CENTRAL: </b>".$comunidad['nombre']." >> <b>IP: </b>".$ip." >> <b>DESC: </b>".$Central['descripcion']." >> <b>ERRORES: (".$Errores.")</b> \n\n";//LO AGREGAMOS AL FORMATO DEL MENSAJE
			#RECORREMOS LOS ERRORES UNO POR UNO
			$AUX = 0;
			$MSJ.= "________________________________________\n# --- ERROR -- SOLUCION -- TIEMPO --  \n";//LO AGREGAMOS AL FORMATO DEL MENSAJE
			while ($Error = mysqli_fetch_array($sql_Errores)) {
				$AUX++;
				#MOSTRAMOS AUX HORA ERROR, HORA SOLUCION, TIEMPO TRANSCURRIDO
				$hora_s = ($Error['hora_s'] == "")? '<b>Pendiente</b>':$Error['hora_s'];			
			}
			$MSJ.= "________________________________________\n\n";//CERRAR ERRORES
		}
	}
	#CUANDO RECORRIMOS TODAS LAS CENTRALES Y GENERAMOS EL FORMATO DEL MEENSAJE -> $MSJ PROCEDEMOS A ENVIAR EL MENSAJE
	if(!sendMessage($id_Chat_Fredy, $MSJ, $website_Resumen) AND !sendMessage($id_Chat_Gabriel, $MSJ, $website_Resumen) AND !sendMessage($id_Chat_Luis, $MSJ, $website_Resumen)){
        #Si se ENVIA el mensaje modificar msj_error a 1 para comprobar que se envio el msj
   		echo "MENSAJE ENVIADO!!!";
    }
}	
?>