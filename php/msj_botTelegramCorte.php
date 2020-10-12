<?php
#INCLUIMOS EL ARCHIVO CON LA CONEXION A LA BASE DE DATOS
include('../php/conexion.php');

#FUNCION QUE SIRVE PARA ENVIAR EL MENSAJE A TELEGRAM DESDE EL BOT CORTES
function sendMessage($id, $msj, $website){
    #CREAMOS EL URL AL CUAL SE ENVIARA EL MENSAJE CON EL ID DEL CHAT QUE RECIBIMOS Y EL MENSAJE QUE HAY QUE ENVIAR
    $url = $website.'/sendMessage?chat_id='.$id.'&parse_mode=HTML&text='.urlencode($msj);
    #SE ENCARGA DE IR A EL URL Y ENVIAR EL MENSAJE DESDE EL BOT
    file_get_contents($url);
}
$bot_Token = '918836101:AAGGaH2MIoTjqdhOmRs_34G1Yjgx5VkwgFI';//TOKEN UNICO DEL BOT CORTES (ID PARA IDENTIFICAR AL BOT Y PODER ENVIAR EL MENSAJE DESDE EL BOT)
$website = 'https://api.telegram.org/bot'.$bot_Token;//DIRECCION A LA QUE SE TIENE QUE ACCEDER LA FUNCION PARA PODER ENVIAR EL MENSAJE DESDE EL BOT
$id_Chat = '1087049979';//ID Fredy ES COMO UN NUMERO TELEFONICO CON EL QUE EL BOT IDENTIFICA A QUIEN ENVIAR EL MENSAJE
$id_Chat2 = '1080437366';//ID Gabriel ES COMO UN NUMERO TELEFONICO CON EL QUE EL BOT IDENTIFICA A QUIEN ENVIAR EL MENSAJE
$id_Chat3 = '1140290694';//ID Mayra ES COMO UN NUMERO TELEFONICO CON EL QUE EL BOT IDENTIFICA A QUIEN ENVIAR EL MENSAJE
            

#-------------------------------------------------------------------
#ENVIAR MENSAJES SI HAY ERROR CON LOS SERVIDORES DE MIKROTIK
#-------------------------------------------------------------------
#BUSCAMOS ERRORES CON ESTATUS  Mikrotik y msj_error en 0
$sql_corte = mysqli_query($conn, "SELECT * FROM cortes WHERE msj = 0");
if(mysqli_num_rows($sql_corte) > 0){
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
        $descripcion_v = $Deducible['descripcion'].".\n";
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
      #VERIFICAMOS SI EN EL CORTE ECHO NO ESTEN TODAS LAS CANTIDADES VACIAS
      if ($cantidad > 0 OR $banco > 0 OR $credito > 0) {
          #CREAMOS EL MENSAJE CON LA INFORMACION QUE HAY QUE ENVIAR POR TELEGRAM
          $Mensaje = "Corte del Dia: ".$Corte['fecha'].", Hora: ".$Corte['hora'].". \nCon folio: <b>".$corte."</b> y usuario: <b>'".$cobrador['firstname']."(".$cobrador['user_name'].")"."'.</b> \n  <b> -Adeudo = $".$Adeudo.". \n  -Deducibles = $".$Deducir.".\n   -</b>".$descripcion_v." \n<b>ENTREGO:\n  *Banco = $".$banco.". \n  *Efectivo = $".$cantidad.". \n  *Credito = $".$credito.". \n \n Relizado por: ".$Corte['realizo'].". \n \n  <a href ='189.197.184.252:6288/SIC4.5/php/reimprimir_corte.php?id=".$corte."'>  -- DESCARGAR -- </a></b>";
      }
      if(!sendMessage($id_Chat, $Mensaje, $website) AND !sendMessage($id_Chat2, $Mensaje, $website) AND !sendMessage($id_Chat3, $Mensaje, $website)){
        #Si se ENVIA el mensaje modificar msj a 1 para comprobar que se envio el msj
   			mysqli_query($conn, "UPDATE cortes SET msj = 1 WHERE id_corte = '$corte'");
      }
    }
}