<?php
    #INCLUIMOS EL ARCHIVO CON LAS LIBRERIAS DE FPDF PARA PODER CREAR ARCHIVOS CON FORMATO PDF
    include("../fpdf/fpdf.php");
    #INCLUIMOS EL PHP DONDE VIENE LA INFORMACION DEL INICIO DE SESSION
    include('is_logged.php');

    $corte = $_GET['id'];//TOMAMOS EL ID DEL CORTE PREVIAMENTE CREADO PARA¨PODERLE ASIGNAR LOS PAGOS EN EL DETALLE

    #FUNCION QUE SIRVE PARA ENVIAR EL MENSAJE A TELEGRAM DESDE EL BOT CORTES
    function sendMessage($id, $msj, $website){
        #CREAMOS EL URL AL CUAL SE ENVIARA EL MENSAJE CON EL ID DEL CHAT QUE RECIBIMOS Y EL MENSAJE QUE HAY QUE ENVIAR
        $url = $website.'/sendMessage?chat_id='.$id.'&parse_mode=HTML&text='.urlencode($msj);
        #SE ENCARGA DE IR A EL URL Y ENVIAR EL MENSAJE DESDE EL BOT
        file_get_contents($url);
    }

    $id_user = $_SESSION['user_id'];//ID DEL USUARIO LOGEADO EN LA SESSION DEL SISTEMA

    #CREAMOS LA CLASE DEL CONTENIDO DE NUESTRO PDF
    class PDF extends FPDF{
        function folioCliente()
        {
            #METEMOS LAS BARIABLES CREADAS FUERA DE LA CLASE PDF DENTRO DE LA MISMA
            global $id_user;
            global $corte;
            $bot_Token = '918836101:AAGGaH2MIoTjqdhOmRs_34G1Yjgx5VkwgFI';//TOKEN UNICO DEL BOT CORTES (ID PARA IDENTIFICAR AL BOT Y PODER ENVIAR EL MENSAJE DESDE EL BOT)
            $website = 'https://api.telegram.org/bot'.$bot_Token;//DIRECCION A LA QUE SE TIENE QUE ACCEDER LA FUNCION PARA PODER ENVIAR EL MENSAJE DESDE EL BOT
            $id_Chat = '1087049979';//ID Fredy ES COMO UN NUMERO TELEFONICO CON EL QUE EL BOT IDENTIFICA A QUIEN ENVIAR EL MENSAJE
            $id_Chat2 = '1080437366';//ID Gabriel ES COMO UN NUMERO TELEFONICO CON EL QUE EL BOT IDENTIFICA A QUIEN ENVIAR EL MENSAJE
            $id_Chat3 = '1140290694';//ID Mayra ES COMO UN NUMERO TELEFONICO CON EL QUE EL BOT IDENTIFICA A QUIEN ENVIAR EL MENSAJE
            #HACEMOS CONEXION CON LA BASE DE DATOS
            $enlace = mysqli_connect("localhost", "root", "root", "servintcomp");
            #DEFINIMOS UNA ZONA HORARIA
            date_default_timezone_set('America/Mexico_City');
            $Fecha_hoy = date('Y-m-d');//CREAMOS UNA FECHA DEL DIA EN CURSO SEGUN LA ZONA HORARIA
            #TOMAMOS LA INFORMACION DEL USUARIO QUE ESTA LOGEADO
            $cobrador = mysqli_fetch_array(mysqli_query($enlace, "SELECT * FROM users WHERE user_id = $id_user"));
            #TOMAMOS LA INFORMACION DEL CORTE CON EL ID GUARDADO EN LA VARIABLE $corte QUE RECIBIMOS CON EL GET
            $Cort =  mysqli_fetch_array(mysqli_query($enlace, "SELECT * FROM cortes WHERE id_corte = $corte"));  
            #TOMAMOS LA INFORMACION DEL DEDUCIBLE CON EL ID GUARDADO EN LA VARIABLE $corte QUE RECIBIMOS CON EL GET
            $Deducible =  mysqli_fetch_array(mysqli_query($enlace, "SELECT * FROM deducibles WHERE id_corte = '$corte'"));  
            #GUARDAMOS LOS TOTALES DE CADA TIPO DE PAGO EN UNA RESPETIVA VARIABLE         
            $cantidad = $Cort['cantidad']-$Deducible['cantidad'];
            $banco = $Cort['banco'];
            $credito = $Cort['credito'];
            #VERIFICAMOS SI EN EL CORTE ECHO NO ESTEN TODAS LAS CANTIDADES VACIAS
            if ($cantidad > 0 OR $banco > 0 OR $credito > 0) {
                #SI ALMENOS UNO DE LOS TOTALES ES MAYOR A CERO PROCEDEMOS A ENVIAR EL MENSAJE POR TELEGRAM
                $Ping = shell_exec("ping 8.8.8.8");//SENTENCIA QUE SIRVE PARA HACER PING MEDIANTE LA CONSOLA DE LA PC
                #COMPARAMOS LOS RESULTADOS DEL PING ECHO SI HACE PING SIN PERDIDAS A LA DIRECCION DE GOOGLE PARA COMPROBAR QUE ALLA INTERNET
                if (strpos($Ping, "perdidos = 0") AND strpos($Ping, "Respuesta desde 8.8.8.8: bytes=32")) {
                    #SI HACE PING SI HABRA INTERNET Y POR TANTO NO MARCARA ERROR PROCEDEMOS A ENVIAR EL MENSAJE
                    #CREAMOS EL MENSAJE CON LA INFORMACION QUE HAY QUE ENVIAR POR TELEGRAM
                    $Mensaje = "Corte en el sistema del dia: ".$Fecha_hoy.". \nCon folio: <b>".$corte."</b> y usuario: <b>'".$cobrador['firstname']."(".$cobrador['user_name'].")"."'</b> con las cantidades totales de: \n  <b>*Banco = $".$banco.". \n  *Efectivo = $".$cantidad.". \n  *Credito = $".$credito.". \n \n Relizado por: ".$Cort['realizo'].". \n \n  <a href ='189.197.184.252:6288/SIC4.5/php/reimprimir_corte.php?id=".$corte."'>  -- DESCARGAR -- </a></b>";
                    #MANDAMOS LLAMAR LA FUNCION DE ENVIAR EL MENSAJE POR TELEGRAM LE AÑADIMOS EL MENSAJE Y LO REPETIMOS CUANTAS VECES QUERAMOS ENVIAR EL MENSAJE A DIFERENTES CHATS SEGUN EL id_Chat
                    sendMessage($id_Chat, $Mensaje, $website);               
                    sendMessage($id_Chat2, $Mensaje, $website);               
                    sendMessage($id_Chat3, $Mensaje, $website);               
                }
            }
            
            $nombre_cobrador = $cobrador['firstname'].' '.$cobrador['lastname'];
            
            #------ INICIAMOS LA CREACION Y FORMTO DEL PDF -------- 
            // Colores de los bordes, fondo y texto
            $this->SetFillColor(255,255,255);
            $this->SetTextColor(0,0,0);
            $this->AddPage();
            $this->Image('../img/logo_ticket.jpg',28,4,20);
            $this->SetFont('Arial','B',13);
            $this->SetY(30);
            $this->Cell(20,4,'Corte De: ',0,0,'C',true);
            $this->Ln(5);
            $this->Cell(50,4,utf8_decode($nombre_cobrador),0,0,'C',true);
            $this->Ln(8);
            $this->Cell(32,4,'Folio: No. '.$corte,0,0,'C',true);
            $this->Ln(10);
            $this->Cell(90,4,'Fecha: '.$Fecha_hoy,0,0,'C',true);            
            $this->Ln(10);
            $this->SetFont('Arial','B',14);            
            $this->Cell(60,4,'<< Internet >>  ',0,0,'C',true);
            $this->Ln(6);
            //---------------EFECTIVO-------------------------------------------

            $sql_efectivoI = mysqli_query($enlace, "SELECT * FROM pagos WHERE id_user=$id_user AND corte = 0 AND tipo_cambio='Efectivo' AND tipo != 'Dispositivo' AND tipo != 'Orden Servicio' AND tipo != 'Orden Servicio'");
            $filas = mysqli_num_rows($sql_efectivoI);
            if ($filas > 0) {

                $this->SetFont('Arial','B',12);
                $this->Cell(20,4,'Efectivo: ',0,0,'C',true);
                $this->Ln(5);
                $this->SetFont('Arial','',11);
                
                while($fila = mysqli_fetch_array($sql_efectivoI)){
                    //insertar pagos de corte...
                    $id_pago = $fila['id_pago'];
                    mysqli_query($enlace,"INSERT INTO detalles(id_corte, id_pago) VALUES ($corte, $id_pago )");
                    $this->SetX(6);
                    $this->MultiCell(70,4,utf8_decode("Cliente: # " .$fila['id_cliente'].'; '.$fila['descripcion'].'; Tipo: '.$fila['tipo']),0,'L',true);
                    $this->MultiCell(70,4,utf8_decode("$ ". $fila['cantidad'].'.00'),0,'R',true);
                    $this->Ln(5);
                }
                $this->SetFont('Arial','B',13);
                $this->Ln(8);
                $total_EI=  mysqli_fetch_array(mysqli_query($enlace, "SELECT SUM(cantidad) AS precio FROM pagos WHERE id_user=$id_user AND corte = 0 AND tipo_cambio='Efectivo' AND tipo != 'Dispositivo' AND tipo != 'Orden Servicio'"));
                $this->MultiCell(65,4,utf8_decode('SUBTOTAL: $'.$total_EI['precio'].'.00'),0,'R',true);
                $this->Ln(10);
            }
            //---------------BANCO-------------------------------------------
            
            $sql_bancoI = mysqli_query($enlace, "SELECT * FROM pagos WHERE id_user=$id_user AND corte = 0 AND tipo_cambio='Banco' AND tipo != 'Dispositivo' AND tipo != 'Orden Servicio'");
            $filas = mysqli_num_rows($sql_bancoI);
            if ($filas > 0) {
            
                $this->SetFont('Arial','B',12); 
                $this->Cell(20,4,'Banco: ',0,0,'C',true);
                $this->Ln(5);
                $this->SetFont('Arial','',11);

                while($fila = mysqli_fetch_array($sql_bancoI)){
                    //insertar pagos de corte...
                    $id_pago = $fila['id_pago'];
                    mysqli_query($enlace,"INSERT INTO detalles(id_corte, id_pago) VALUES ($corte, $id_pago )");
                    $this->SetX(6);
                    $this->MultiCell(70,4,utf8_decode("Cliente: # ".$fila['id_cliente'].'; '.$fila['descripcion'].'; Tipo: '.$fila['tipo']),0,'L',true);
                    $this->MultiCell(70,4,utf8_decode("$ ". $fila['cantidad'].'.00'),0,'R',true);
                    $this->Ln(5);
                }
                $this->SetFont('Arial','B',13);
                $this->Ln(8);
                $totalbancoI = mysqli_fetch_array(mysqli_query($enlace, "SELECT SUM(cantidad) AS precio FROM pagos WHERE id_user=$id_user AND corte = 0 AND tipo_cambio='Banco' AND tipo != 'Dispositivo' AND tipo != 'Orden Servicio'"));
                $this->MultiCell(65,4,utf8_decode('SUBTOTAL: $'.$totalbancoI['precio'].'.00'),0,'R',true);
                $this->Ln(10);
            }
            //---------------CREDITO-------------------------------------------

            $sql_creditoI = mysqli_query($enlace, "SELECT * FROM pagos WHERE id_user=$id_user AND corte = 0 AND tipo_cambio='Credito' AND tipo != 'Dispositivo' AND tipo != 'Orden Servicio'");
            $filas = mysqli_num_rows($sql_creditoI);
            if ($filas > 0) {

                $this->SetFont('Arial','B',12);
                $this->Cell(20,4,'Credito: ',0,0,'C',true);
                $this->Ln(5);
                $this->SetFont('Arial','',11);
                
                while($fila = mysqli_fetch_array($sql_creditoI)){
                    //insertar pagos de corte...
                    $id_pago = $fila['id_pago'];
                    mysqli_query($enlace,"INSERT INTO detalles(id_corte, id_pago) VALUES ($corte, $id_pago )");
                    $this->SetX(6);
                    $this->MultiCell(70,4,utf8_decode("Cliente: # " .$fila['id_cliente'].'; '.$fila['descripcion'].'; Tipo: '.$fila['tipo']),0,'L',true);
                    $this->MultiCell(70,4,utf8_decode("$ ". $fila['cantidad'].'.00'),0,'R',true);
                    $this->Ln(5);
                }
                $this->SetFont('Arial','B',13);
                $this->Ln(8);
                $total_CI=  mysqli_fetch_array(mysqli_query($enlace, "SELECT SUM(cantidad) AS precio FROM pagos WHERE id_user=$id_user AND corte = 0 AND tipo_cambio='Credito' AND tipo != 'Dispositivo' AND tipo != 'Orden Servicio'"));
                $this->MultiCell(65,4,utf8_decode('SUBTOTAL: - $'.$total_CI['precio'].'.00'),0,'R',true);
                $this->Ln(10);
            }

            $this->SetFont('Arial','B',14); 
            $this->Cell(60,4,'------------------------------------------',0,0,'C',true);
            $this->Ln(10);           
            $this->Cell(60,4,'<< Orden Serv. >> ',0,0,'C',true);
            $this->Ln(6);
            $sql_efectivoO = mysqli_query($enlace, "SELECT * FROM pagos WHERE id_user=$id_user AND corte = 0 AND tipo_cambio='Efectivo' AND tipo = 'Orden Servicio'");
            $filas = mysqli_num_rows($sql_efectivoO);
            if ($filas > 0) {

                $this->SetFont('Arial','B',12);
                $this->Cell(20,4,'Efectivo: ',0,0,'C',true);
                $this->Ln(5);
                $this->SetFont('Arial','',11);
                
                while($fila = mysqli_fetch_array($sql_efectivoO)){
                    //insertar pagos de corte...
                    $id_pago = $fila['id_pago'];
                    mysqli_query($enlace,"INSERT INTO detalles(id_corte, id_pago) VALUES ($corte, $id_pago )");
                    $this->SetX(6);
                    $this->MultiCell(70,4,utf8_decode("Cliente: # " .$fila['id_cliente'].'; '.$fila['descripcion'].'; Tipo: '.$fila['tipo']),0,'L',true);
                    $this->MultiCell(70,4,utf8_decode("$ ". $fila['cantidad'].'.00'),0,'R',true);
                    $this->Ln(5);
                }
                $this->SetFont('Arial','B',13);
                $this->Ln(8);
                $total_EO=  mysqli_fetch_array(mysqli_query($enlace, "SELECT SUM(cantidad) AS precio FROM pagos WHERE id_user=$id_user AND corte = 0 AND tipo_cambio='Efectivo' AND tipo = 'Orden Servicio'"));
                $this->MultiCell(65,4,utf8_decode('SUBTOTAL: $'.$total_EO['precio'].'.00'),0,'R',true);
                $this->Ln(10);
            }
            //---------------BANCO-------------------------------------------
            
            $sql_bancoO = mysqli_query($enlace, "SELECT * FROM pagos WHERE id_user=$id_user AND corte = 0 AND tipo_cambio='Banco' AND tipo = 'Orden Servicio'");
            $filas = mysqli_num_rows($sql_bancoO);
            if ($filas > 0) {
            
                $this->SetFont('Arial','B',12); 
                $this->Cell(20,4,'Banco: ',0,0,'C',true);
                $this->Ln(5);
                $this->SetFont('Arial','',11);

                while($fila = mysqli_fetch_array($sql_bancoO)){
                    //insertar pagos de corte...
                    $id_pago = $fila['id_pago'];
                    mysqli_query($enlace,"INSERT INTO detalles(id_corte, id_pago) VALUES ($corte, $id_pago )");
                    $this->SetX(6);
                    $this->MultiCell(70,4,utf8_decode("Cliente: # ".$fila['id_cliente'].'; '.$fila['descripcion'].'; Tipo: '.$fila['tipo']),0,'L',true);
                    $this->MultiCell(70,4,utf8_decode("$ ". $fila['cantidad'].'.00'),0,'R',true);
                    $this->Ln(5);
                }
                $this->SetFont('Arial','B',13);
                $this->Ln(8);
                $totalbancoO = mysqli_fetch_array(mysqli_query($enlace, "SELECT SUM(cantidad) AS precio FROM pagos WHERE id_user=$id_user AND corte = 0 AND tipo_cambio='Banco' AND tipo = 'Orden Servicio'"));
                $this->MultiCell(65,4,utf8_decode('SUBTOTAL: $'.$totalbancoO['precio'].'.00'),0,'R',true);
                $this->Ln(10);
            }

            $this->SetFont('Arial','B',14); 
            $this->Cell(60,4,'------------------------------------------',0,0,'C',true);
            $this->Ln(10);           
            $this->Cell(60,4,'<< Serv. Tecnico >> ',0,0,'C',true);
            $this->Ln(6);
            //---------------EFECTIVO-------------------------------------------

            $sql_efectivoST = mysqli_query($enlace, "SELECT * FROM pagos WHERE id_user=$id_user AND corte = 0 AND tipo_cambio='Efectivo' AND tipo = 'Dispositivo'");
            $filas = mysqli_num_rows($sql_efectivoST);
            if ($filas > 0) {

                $this->SetFont('Arial','B',12);
                $this->Cell(20,4,'Efectivo: ',0,0,'C',true);
                $this->Ln(5);
                $this->SetFont('Arial','',11);
                
                while($fila = mysqli_fetch_array($sql_efectivoST)){
                    //insertar pagos de corte...
                    $id_pago = $fila['id_pago'];
                    mysqli_query($enlace,"INSERT INTO detalles(id_corte, id_pago) VALUES ($corte, $id_pago )");
                    $this->SetX(6);
                    $this->MultiCell(70,4,utf8_decode("Cliente: # " .$fila['id_cliente'].'; '.$fila['descripcion'].'; Tipo: '.$fila['tipo']),0,'L',true);
                    $this->MultiCell(70,4,utf8_decode("$ ". $fila['cantidad'].'.00'),0,'R',true);
                    $this->Ln(5);
                }
                $this->SetFont('Arial','B',13);
                $this->Ln(8);
                $total_EST=  mysqli_fetch_array(mysqli_query($enlace, "SELECT SUM(cantidad) AS precio FROM pagos WHERE id_user=$id_user AND corte = 0 AND tipo_cambio='Efectivo' AND tipo = 'Dispositivo'"));            
                $this->MultiCell(65,4,utf8_decode('SUBTOTAL: $'.$total_EST['precio'].'.00'),0,'R',true);
                $this->Ln(10);
            }
            //---------------BANCO-------------------------------------------
            
            $sql_bancoST = mysqli_query($enlace, "SELECT * FROM pagos WHERE id_user=$id_user AND corte = 0 AND tipo_cambio='Banco' AND tipo = 'Dispositivo'");
            $filas = mysqli_num_rows($sql_bancoST);
            if ($filas > 0) {
            
                $this->SetFont('Arial','B',12); 
                $this->Cell(20,4,'Banco: ',0,0,'C',true);
                $this->Ln(5);
                $this->SetFont('Arial','',11);

                while($fila = mysqli_fetch_array($sql_bancoST)){
                    //insertar pagos de corte...
                    $id_pago = $fila['id_pago'];
                    mysqli_query($enlace,"INSERT INTO detalles(id_corte, id_pago) VALUES ($corte, $id_pago )");
                    $this->SetX(6);
                    $this->MultiCell(70,4,utf8_decode("Cliente: # ".$fila['id_cliente'].'; '.$fila['descripcion'].'; Tipo: '.$fila['tipo']),0,'L',true);
                    $this->MultiCell(70,4,utf8_decode("$ ". $fila['cantidad'].'.00'),0,'R',true);
                    $this->Ln(5);
                }
                $this->SetFont('Arial','B',13);
                $this->Ln(8);
                $totalbancoST = mysqli_fetch_array(mysqli_query($enlace, "SELECT SUM(cantidad) AS precio FROM pagos WHERE id_user=$id_user AND corte = 0 AND tipo_cambio='Banco' AND tipo = 'Dispositivo'"));            
                $this->MultiCell(65,4,utf8_decode('SUBTOTAL: $'.$totalbancoST['precio'].'.00'),0,'R',true);
                $this->Ln(10);
            }

            $Todos_Pagos = mysqli_fetch_array(mysqli_query($enlace,"SELECT count(*) FROM pagos WHERE id_user=$id_user AND corte = 0" ));
            $this->Cell(60,4,'------------------------------------------',0,0,'C',true);
            $this->Ln(8);
            $this->Cell(60,4,'<< Deducibles >> ',0,0,'C',true);
            $this->Ln(6);
            $this->SetFont('Arial','',11);
            $this->Ln(6);
            $this->MultiCell(70,4,utf8_decode("Descripcion: ".$Deducible['descripcion']),0,'L',true);
            $this->MultiCell(70,4,utf8_decode("- $ ". $Deducible['cantidad'].'.00'),0,'R',true);
            $this->Ln(5);
            $this->SetFont('Arial','B',14);             
            $this->Cell(60,4,'------------------------------------------',0,0,'C',true);
            $this->Ln(8);
            $this->MultiCell(65,4,utf8_decode('Total de Pagos: '.$Todos_Pagos['count(*)']),0,'R',true);
            $this->Ln(4);

            $this->SetFont('Arial','',11);
            $Telefono = mysqli_num_rows(mysqli_query($enlace,"SELECT *  FROM pagos WHERE corte = 0 AND id_user = $id_user AND tipo IN ('Mes-Tel', 'Min-Extra')" ));
            if ($Telefono > 0) {
              $this->Cell(60,4,utf8_decode('Telefono = '.$Telefono),0,0,'C',true);
              $this->Ln(5);
            }
            $Mes_Internet = mysqli_num_rows(mysqli_query($enlace,"SELECT *  FROM pagos WHERE corte = 0 AND id_user = $id_user AND tipo = 'Mensualidad'" ));
            if ($Mes_Internet > 0) {
              $this->Cell(60,4,utf8_decode('Mes-Internet = '.$Mes_Internet),0,0,'C',true);
              $this->Ln(5);
            }
            $Abonos = mysqli_num_rows(mysqli_query($enlace,"SELECT *  FROM pagos WHERE corte = 0 AND id_user = $id_user AND tipo = 'Abono'" ));
            if ($Abonos > 0) {
              $this->Cell(60,4,utf8_decode('Abonos = '.$Abonos),0,0,'C',true);
              $this->Ln(5);
            }
            $AntInst = mysqli_num_rows(mysqli_query($enlace,"SELECT *  FROM pagos WHERE corte = 0 AND id_user = $id_user AND tipo = 'Anticipo'" ));
            if ($AntInst > 0) {
              $this->Cell(60,4,utf8_decode('Anticipo Inst. = '.$AntInst),0,0,'C',true);
              $this->Ln(5);
            }
            $AbonoInst = mysqli_num_rows(mysqli_query($enlace,"SELECT *  FROM pagos WHERE corte = 0 AND id_user = $id_user AND tipo = 'Abono Instalacion'" ));
            if ($AbonoInst > 0) {
              $this->Cell(60,4,utf8_decode('Abono Inst. = '.$AbonoInst),0,0,'C',true);
              $this->Ln(5);
            }
            $LiquidInst = mysqli_num_rows(mysqli_query($enlace,"SELECT *  FROM pagos WHERE corte = 0 AND id_user = $id_user AND tipo = 'Liquidacion'" ));
            if ($LiquidInst > 0) {
              $this->Cell(60,4,utf8_decode('Liquidacion Inst. = '.$LiquidInst),0,0,'C',true);
              $this->Ln(5);
            }
            $Reporte = mysqli_num_rows(mysqli_query($enlace,"SELECT *  FROM pagos WHERE corte = 0 AND id_user = $id_user AND tipo = 'Reporte'" ));
            if ($Reporte > 0) {
              $this->Cell(60,4,utf8_decode('Reportes = '.$Reporte),0,0,'C',true);
              $this->Ln(5);
            }
            $AntiDisp = mysqli_num_rows(mysqli_query($enlace,"SELECT *  FROM pagos WHERE corte = 0 AND id_user = $id_user AND tipo = 'Dispositivo' AND descripcion = 'Anticipo'" ));
            if ($AntiDisp > 0) {
              $this->Cell(60,4,utf8_decode('Anticipo Disp. = '.$AntiDisp),0,0,'C',true);
              $this->Ln(5);
            }
            $LiquidDisp = mysqli_num_rows(mysqli_query($enlace,"SELECT *  FROM pagos WHERE corte = 0 AND id_user = $id_user AND tipo = 'Dispositivo' AND descripcion = 'Liquidacion'" ));
            if ($LiquidDisp > 0) {
              $this->Cell(60,4,utf8_decode('Liquidacion Disp. = '.$LiquidDisp),0,0,'C',true);
              $this->Ln(5);
            }
            $Orden = mysqli_num_rows(mysqli_query($enlace,"SELECT *  FROM pagos WHERE corte = 0 AND id_user = $id_user AND tipo = 'Orden Servicio'" ));
            if ($Orden > 0) {
              $this->Cell(60,4,utf8_decode('Ordenes Serv. = '.$Orden),0,0,'C',true);
              $this->Ln(5);
            }
            $Otros = mysqli_num_rows(mysqli_query($enlace,"SELECT *  FROM pagos WHERE corte = 0 AND id_user = $id_user AND tipo = 'Otros Pagos'" ));
            if ($Otros > 0) {
              $this->Cell(60,4,utf8_decode('Otros Pagos = '.$Otros),0,0,'C',true);
              $this->Ln(5);
            }

            $this->SetFont('Arial','B',11);
            $this->Ln(9);
            if ($cantidad > 0) {
              $this->MultiCell(65,4,utf8_decode('TOTAL EFECTIVO: $'.$cantidad.'.00'),0,'L',true);
              $this->Ln(3);
            }
            if ($banco > 0) {
              $this->MultiCell(65,4,utf8_decode('TOTAL BANCO: $'.$banco.'.00'),0,'L',true);
              $this->Ln(3);
            }
            if ($credito > 0) {
              $this->MultiCell(65,4,utf8_decode('TOTAL CREDITO: $'.$credito.'.00'),0,'L',true);
              $this->Ln(3);
            }  
            $this->Ln(5); 
            $this->Cell(60,4,'Servicios Integrales de Computacion ',0,0,'C',true);
            $this->Ln(15);          
            mysqli_query($enlace,"UPDATE pagos SET corte=1 WHERE id_user=$id_user");
            mysqli_close($enlace);
        }
    }
    $pdf = new PDF('P', 'mm', array(80,297));
    $pdf->SetTitle('CORTE');
    $pdf->folioCliente();
    $pdf->Output('CORTE','I');
?>