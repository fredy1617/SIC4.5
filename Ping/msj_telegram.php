<?php
include('../php/conexion.php');
//FUncion la cual envia el mensaje al bot y el mensaje que reciba
function sendMessage($id, $msj, $website){
    $url = $website.'/sendMessage?chat_id='.$id.'&parse_mode=HTML&text='.urlencode($msj);
    file_get_contents($url);
}

$bot_Token = '1284789530:AAGhC_vfTpyElbPA4pHkyNoPe7PvdxV1Vpo';
$website = 'https://api.telegram.org/bot'.$bot_Token;
$id_Chat = '1087049979';#ID Fredy

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
        if(!sendMessage($id_Chat, $Mensaje, $website)){
        	#Si se ENVIA el mensaje modificar msj_error a 1 para comprobar que se envio el msj
   			$id_eM = $error_mikrotik['id'];
   			mysqli_query($conn, "UPDATE errores_pings SET msj_error = 1 WHERE id = '$id_eM'");
        }
    }
}


#-------------------------------------------------------------------
#ENVIAR MENSAJES SI HAY ERRORES DE PINGS A IP's
#-------------------------------------------------------------------
$sql_errores = mysqli_query($conn, "SELECT * FROM errores_pings WHERE msj_error = 0 AND estatus = 'Pendiente' AND contador >= 5");
if(mysqli_num_rows($sql_errores) > 0){
   	#SI SE ENCONTRARON ERRORES SE RECORREN CADA UNO...
   	while($error = mysqli_fetch_array($sql_errores)){
   		#Enviar mensaje a telagram
   		$Mensaje = "<b>ALERTA !! FALLA DE PING:</b> \n \n*".$error['descripcion'];
        if(!sendMessage($id_Chat, $Mensaje, $website)){
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
        if(!sendMessage($id_Chat, $Mensaje, $website)){
        	#Si se ENVIA el mensaje modificar msj_error a 1 para comprobar que se envio el msj
   			$id_eS = $error_solucion['id'];
   			mysqli_query($conn, "UPDATE errores_pings SET msj_solucion = 1 WHERE id = '$id_eS'");
        } 
    }
}
?>