<?php
#INCLUIMOS EL ARCHIVO CON LA CONEXION A LA BASE DE DATPS
include('../php/conexion.php');

#FUNCION QUE SIRVE PARA ENVIAR EL MENSAJE A TELEGRAM DESDE EL BOT CORTES
function sendMessage($id, $msj, $website){
    #CREAMOS EL URL AL CUAL SE ENVIARA EL MENSAJE CON EL ID DEL CHAT QUE RECIBIMOS Y EL MENSAJE QUE HAY QUE ENVIAR
    $url = $website.'/sendMessage?chat_id='.$id.'&parse_mode=HTML&text='.urlencode($msj);
    #SE ENCARGA DE IR A EL URL Y ENVIAR EL MENSAJE DESDE EL BOT
    file_get_contents($url);
}
$bot_Token = '1284789530:AAGhC_vfTpyElbPA4pHkyNoPe7PvdxV1Vpo';//TOKEN UNICO DEL BOT CORTES (ID PARA IDENTIFICAR AL BOT Y PODER ENVIAR EL MENSAJE DESDE EL BOT)
$website = 'https://api.telegram.org/bot'.$bot_Token;//DIRECCION A LA QUE SE TIENE QUE ACCEDER LA FUNCION PARA PODER ENVIAR EL MENSAJE DESDE EL BOT
$id_Chat = '1087049979';//ID Fredy ES COMO UN NUMERO TELEFONICO CON EL QUE EL BOT IDENTIFICA A QUIEN ENVIAR EL MENSAJE
$id_Chat2 = '1080437366';//ID Gabriel ES COMO UN NUMERO TELEFONICO CON EL QUE EL BOT IDENTIFICA A QUIEN ENVIAR EL MENSAJE
$id_Chat3 = '972701200'; //ID Luis ES COMO UN NUMERO TELEFONICO CON EL QUE EL BOT IDENTIFICA A QUIEN ENVIAR EL MENSAJE

#-------------------------------------------------------------------
#ENVIAR MENSAJES SI HAY ERROR CON LOS SERVIDORES DE MIKROTIK
#-------------------------------------------------------------------
#BUSCAMOS ERRORES CON ESTATUS  Mikrotik y msj_error en 0
$sql_errores_mikrotik = mysqli_query($conn, "SELECT * FROM errores_pings WHERE msj_error = 0 AND estatus = 'Mikrotik'");
if(mysqli_num_rows($sql_errores_mikrotik) > 0){
   	#SI SE ENCONTRARON ERRORES SE RECORREN CADA UNO...
   	while($error_mikrotik = mysqli_fetch_array($sql_errores_mikrotik)){
   		#Enviar mensaje a telagram
   		$Mensaje = "<b>ALERTA !! FALLA MIKROTIK:</b> \n \n*".$error_mikrotik['descripcion']." \n *IP: ".$error_mikrotik['ip'];
        if(!sendMessage($id_Chat, $Mensaje, $website) AND !sendMessage($id_Chat2, $Mensaje, $website) AND !sendMessage($id_Chat3, $Mensaje, $website)){
        	#Si se ENVIA el mensaje modificar msj_error a 1 para comprobar que se envio el msj
   			$id_eM = $error_mikrotik['id'];
   			mysqli_query($conn, "UPDATE errores_pings SET msj_error = 1 WHERE id = '$id_eM'");
        }
    }
}


#-------------------------------------------------------------------
#ENVIAR MENSAJES SI HAY ERRORES DE PINGS A IP's
#-------------------------------------------------------------------
$sql_errores = mysqli_query($conn, "SELECT * FROM errores_pings WHERE msj_error = 0 AND estatus = 'Pendiente' AND contador >= 10");
if(mysqli_num_rows($sql_errores) > 0){
   	#SI SE ENCONTRARON ERRORES SE RECORREN CADA UNO...
   	while($error = mysqli_fetch_array($sql_errores)){
   		#Enviar mensaje a telagram
   		$Mensaje = "<b>ALERTA !! FALLA DE PING:</b> \n \n*".$error['descripcion'];
        if(!sendMessage($id_Chat, $Mensaje, $website) AND !sendMessage($id_Chat2, $Mensaje, $website) AND !sendMessage($id_Chat3, $Mensaje, $website)){
        	#Si se ENVIA el mensaje modificar msj_error a 1 para comprobar que se envio el msj
     			$id_e = $error['id'];
     			mysqli_query($conn, "UPDATE errores_pings SET msj_error = 1 WHERE id = '$id_e'");
        } 
    }
}


#-------------------------------------------------------------------
#ENVIAR MENSAJES SI ALGUNO DE LOS ERRORES DE PINGS FUE SOLUCIONADO
#-------------------------------------------------------------------
$sql_errores_solucion = mysqli_query($conn, "SELECT * FROM errores_pings WHERE msj_solucion = 0 AND estatus = 'Solucionado'");
if(mysqli_num_rows($sql_errores_solucion) > 0){
   	#SI SE ENCONTRARON ERRORES SE RECORREN CADA UNO...
   	while($error_solucion = mysqli_fetch_array($sql_errores_solucion)){
   		echo "<br>";
   		#Enviar mensaje a telagram
   		$Mensaje = "<b>SOLUCION DE FALLA:</b> \n \n*".$error_solucion['descripcion']."\n* Fecha: ".$error_solucion['fecha_s'].", Hora: ".$error_solucion['hora_s']." de solucion.";
        if(!sendMessage($id_Chat, $Mensaje, $website) AND !sendMessage($id_Chat2, $Mensaje, $website) AND !sendMessage($id_Chat3, $Mensaje, $website)){
        	#Si se ENVIA el mensaje modificar msj_error a 1 para comprobar que se envio el msj
   			  $id_eS = $error_solucion['id'];
   			  mysqli_query($conn, "UPDATE errores_pings SET msj_solucion = 1 WHERE id = '$id_eS'");
        } 
    }
}
?>