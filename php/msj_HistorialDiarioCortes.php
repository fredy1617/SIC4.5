<?php<?php
#INCLUIMOS EL ARCHIVO CON LA CONEXION A LA BASE DE DATOS
include('../php/conexion.php');
#INCLUIMOS TODAS LAS LIBRERIAS  DE MAILER PARA PODER ENVIAR CORREOS DE ESTE ARCHIVO
include('../Mailer/src/PHPMailer.php');
include('../Mailer/src/SMTP.php');
include('../Mailer/src/Exception.php');

$mail = new PHPMailer\PHPMailer\PHPMailer();
#AGREGAMOS LOS ATRIVUTOS DEL CORREO DESDE EL CUAL SE ENVIARA
$mail->isSMTP();
$mail->SMTPDebug = 0;
$mail->SMTPAuth = true;
$mail->SMTPSecure = 'tls';
$mail->Host = 'smtp.gmail.com';
$mail->Port = 587;
$mail->Username = 'sic.redes.som@gmail.com';
$mail->Password = 'Respif_rede5';
#COLOCAMOS UN TITULO AL CORREO  COMO REMITENTE
$mail->setFrom('no-replay@gmail.com', 'HISTORIAL CORTES DIARIO');
#DEFINIMOS A QUE CORREOS SERAN LOS DESTINATARIOS
$mail->addAddress('alfredo.martinez@sicsom.com');
$mail->addAddress('gabriel.valles@sicsom.com');

#------------------------------------------------------------------------------------
#HAY QUE GENERAR EL MENSAJE Y EL ASUNTO CON LA INFORMACION DE LOS CORTES DIARIOS
#------------------------------------------------------------------------------------
#DEFINIMOS UNA ZONA HORARIA
date_default_timezone_set('America/Mexico_City');
$Fecha_hoy = date('Y-m-d');//CREAMOS UNA FECHA DEL DIA EN CURSO SEGUN LA ZONA HORARIA
#SELECCIONAMOS TODOS LOS CORRTES REALIZADOS CON LA FECHA DE HOY
$sql_cortes = mysqli_query($conn, "SELECT * FROM cortes WHERE fecha = '$Fecha_hoy'");
#VERIFICAMOS SI SE ENCONTRARON CORTES

if(mysqli_num_rows($sql_cortes) > 0){
    echo "ENTRE <br>";
    #INICIAMOS A CREAR EL MENSAJE A ENVIAR CABECERA
    $Mensaje = "<b>HISTORIAL DIARIO DE CORTES:<br>
                  Fecha: ".$Fecha_hoy.".</b><br><br>";
    $Total_Credito = 0;
    $Total_Banco = 0;
    $Total_Efectivo = 0;
    #SI SE ENCONTRARON CORTES SE RECORE UNO POR UNO...
    while($Corte = mysqli_fetch_array($sql_cortes)){      
      $id_user = $Corte['usuario'];
      $corte = $Corte['id_corte'];
      #TOMAMOS LA INFORMACION DEL USUARIO QUE ESTA LOGEADO QUIEN HIZO LOS COBROS
      $cobrador = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM users WHERE user_id = $id_user"));
      #TOMAMOS LA INFORMACION DEL DEDUCIBLE CON EL ID GUARDADO EN LA VARIABLE $corte QUE RECIBIMOS CON EL GET
      $sql_Deducible = mysqli_query($conn, "SELECT * FROM deducibles WHERE id_corte = '$corte'");  
      if (mysqli_num_rows($sql_Deducible) > 0) {
        $Deducible = mysqli_fetch_array($sql_Deducible);
        $Deducir = $Deducible['cantidad'];
      }else{
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
      $Efectivo = $Corte['cantidad']-$Deducir-$Adeudo;
      $Banco = $Corte['banco'];
      $Credito = $Corte['credito'];

      #LE SUMAMOS LA INFORMACION DEL CORTE EN TURNO AL MENSAJE
      $Mensaje .= "#-------------------------------------------------------------------------<br>
                    >>> Cobrador: <b>".$cobrador['firstname']."(".$cobrador['user_name'].")</b>, Folio: <b>".$corte."</b><br>
                    >>><b>Adeudo = $".$Adeudo." _ Deducibles = $".$Deducir.".<br>
                    ENTREGO:<br>
                    * * SubTotal_Credito = $".$Credito."<br>
                    * * SubTotal_Banco = $".$Banco."<br>
                    * * SubTotal_Efecivo = $".$Efectivo."<br></b>
                   #-------------------------------------------------------------------------<br>";
      #SUMAMOS LOS SUBTOTALES AL TOTAL
      $Total_Credito += $Credito;
      $Total_Banco += $Banco;
      $Total_Efectivo += $Efectivo;
    }// FIN DEL WHILE
    #CREAMOS EL PIE DEL MENSAJE MOSTRANDO LOS TOTALES
    $Mensaje .= "<br> ///////////////////////////////////////////////////////////<br>
                 <b> | > TOTAL CREDITO = $".$Total_Credito."<br>
                  | > TOTAL BANCO = $".$Total_Banco."<br>
                  | > TOTAL EFECTIVO = $".$Total_Efectivo."<br></b>
                 ///////////////////////////////////////////////////////////";
    #echo $Mensaje;
    $mail->isHTML(true);
    $mail->Subject = 'Historial Fecha: '.$Fecha_hoy;
    $mail->Body = $Mensaje;
    if (!$mail->send()) {
      echo "NO SE ENVIO";
    }else{
      echo "CORREO ENVIADO CON EXITO !!!";
    }
}// FIN DEL IF