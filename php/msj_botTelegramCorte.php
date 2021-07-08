<?php
#Falla
#INCLUIMOS EL ARCHIVO CON LA CONEXION A LA BASE DE DATOS
include('../php/conexion.php');
#INCLUIMOS TODAS LAS LIBRERIAS  DE MAILER PARA PODER ENVIAR CORREOS DE ESTE ARCHIVO
include('../Mailer/src/PHPMailer.php');
include('../Mailer/src/SMTP.php');
include('../Mailer/src/Exception.php');

#INCLUIMOS EL ARCHIVO CON LA INFORMACION DE LOS CHATS BOT
#include('../php/infoBots.php');

#FUNCION QUE SIRVE PARA ENVIAR EL MENSAJE A TELEGRAM DESDE EL BOT CORTES
#function sendMessage($id, $msj, $website){
    #CREAMOS EL URL AL CUAL SE ENVIARA EL MENSAJE CON EL ID DEL CHAT QUE RECIBIMOS Y EL MENSAJE QUE HAY QUE ENVIAR
    #$url = $website.'/sendMessage?chat_id='.$id.'&parse_mode=HTML&text='.urlencode($msj);
    #SE ENCARGA DE IR A EL URL Y ENVIAR EL MENSAJE DESDE EL BOT
   # file_get_contents($url);
#}          
#-------------------------------------------------------------------
#ENVIAR MENSAJES SI HAY ERROR CON LOS SERVIDORES DE MIKROTIK
#-------------------------------------------------------------------
#BUSCAMOS ERRORES CON ESTATUS  Mikrotik y msj_error en 0
$sql_corte = mysqli_query($conn, "SELECT * FROM cortes WHERE msj = 0");
if(mysqli_num_rows($sql_corte) > 0){
  echo "ENTRE <br>";
   	#SI SE ENCONTRARON ERRORES SE RECORREN CADA UNO...
   	while($Corte = mysqli_fetch_array($sql_corte)){
      #DEFINIMOS UNA ZONA HORARIA
      date_default_timezone_set('America/Mexico_City');
      $Fecha_hoy = date('Y-m-d');//CREAMOS UNA FECHA DEL DIA EN CURSO SEGUN LA ZONA HORARIA
      $id_user = $Corte['usuario'];
      $corte = $Corte['id_corte'];
      #TOMAMOS LA INFORMACION DEL USUARIO QUE ESTA LOGEADO QUIEN HIZO LOS COBROS
      $cobrador = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM users WHERE user_id = $id_user"));
      #TOMAMOS LA INFORMACION DEL DEDUCIBLE CON EL ID GUARDADO EN LA VARIABLE $corte QUE RECIBIMOS CON EL GET
      $sql_Deducible = mysqli_query($conn, "SELECT * FROM deducibles WHERE id_corte = '$corte'");  
      if (mysqli_num_rows($sql_Deducible) > 0) {
        $Deducible = mysqli_fetch_array($sql_Deducible);
        $descripcion_v = $Deducible['descripcion'].".<br>";
        $Deducir = $Deducible['cantidad'];
      }else{
        $descripcion_v = '';
        $Deducir = 0;
      }
      $sql_deuda =mysqli_query($conn, "SELECT * FROM deudas_cortes WHERE id_corte = $corte");
      if (mysqli_num_rows($sql_deuda) > 0) {
        $Deuda = mysqli_fetch_array($sql_deuda);
        $Adeudo = $Deuda['cantidad'];
      }else{
        $Adeudo = 0;
      }
      #GUARDAMOS LOS TOTALES DE CADA TIPO DE PAGO EN UNA RESPETIVA VARIABLE         
      $cantidad = $Corte['cantidad']-$Deducir-$Adeudo;
      $banco = $Corte['banco'];
      $credito = $Corte['credito'];
      $Mensaje = '';
      echo $cantidad.' - '.$banco.' - '.$credito;
      #VERIFICAMOS SI EN EL CORTE ECHO NO ESTEN TODAS LAS CANTIDADES VACIAS
      if ($cantidad >= 0 OR $banco > 0 OR $credito > 0) {
          #CREAMOS EL MENSAJE CON LA INFORMACION QUE HAY QUE ENVIAR POR TELEGRAM
          $Mensaje = "Corte del Dia: ".$Corte['fecha'].", Hora: ".$Corte['hora'].". <br>Con folio: <b>".$corte."</b> y usuario: <b>'".$cobrador['firstname']."(".$cobrador['user_name'].")'.</b> <br>  <b> -Adeudo = $".$Adeudo.". <br>  -Deducibles = $".$Deducir.".<br>   -</b>".$descripcion_v." <br><b>ENTREGO:<br>  *Banco = $".$banco.". <br>  *Efectivo = $".$cantidad.". <br>  *Credito = $".$credito.". <br> <br> Relizado por: ".$Corte['realizo'];
          $Aviso = "Corte del Dia: ".$Corte['fecha'].", Hora: ".$Corte['hora'].". <br>Con folio: <b>".$corte."</b> y usuario: <b>'".$cobrador['firstname']."(".$cobrador['user_name'].")'.</b> ";
      }
      #if( !sendMessage($id_Chat_Fredy, $Aviso, $website_Aviso)  AND !sendMessage($id_Chat_Rocio, $Aviso, $website_Aviso) AND !sendMessage($id_Chat_Fredy, $Mensaje, $website_Corte) AND !sendMessage($id_Chat_Gabriel, $Mensaje, $website_Corte)){
        #Si se ENVIA el mensaje modificar msj a 1 para comprobar que se envio el msj
   			#mysqli_query($conn, "UPDATE cortes SET msj = 1 WHERE id_corte = '$corte'");
      #}
      $asunto = 'Corte No.'.$corte;
      include('../enviar_correo.php');
      if (!$mail->send()) {
        echo "NO SE ENVIO";
      }else{
        echo "CORREO ENVIADO CON EXITO !!!";
        mysqli_query($conn, "UPDATE cortes SET msj = 1 WHERE id_corte = '$corte'");
      }
      include('../aviso_corte.php');
      if (!$mail->send()) {
        echo "NO SE ENVIO AVISO";
      }else{
        echo "AVISO ENVIADO CON EXITO !!!";
      }
    }
}