<?php
    //Incluimos la libreria fpdf
    include("../fpdf/fpdf.php");
    include('is_logged.php');
    function sendMessage($id, $msj, $website){
        $url = $website.'/sendMessage?chat_id='.$id.'&parse_mode=HTML&text='.urlencode($msj);
        file_get_contents($url);
    }

    $pass="root";
    $id_user = $_SESSION['user_id'];
    //Incluimos el archivo de conexion a la base de datos
    class PDF extends FPDF{
        function folioCliente()
        {
            global $id_user;
            global $pass;
            $bot_Token = '918836101:AAGGaH2MIoTjqdhOmRs_34G1Yjgx5VkwgFI';
            $id_Chat = '1087049979';
            $id_Chat2 = '1080437366';
            $id_Chat3 = '1140290694';
            $website = 'https://api.telegram.org/bot'.$bot_Token;
            $enlace = mysqli_connect("localhost", "root", $pass, "servintcomp");
            
            $cobrador = mysqli_fetch_array(mysqli_query($enlace, "SELECT * FROM users WHERE user_id = $id_user"));
            $Todos_Pagos = mysqli_fetch_array(mysqli_query($enlace,"SELECT count(*) FROM pagos WHERE id_user=$id_user AND corte = 0" ));
            $sql_total = mysqli_query($enlace, "SELECT SUM(cantidad) AS precio FROM pagos WHERE id_user=$id_user AND corte = 0 AND tipo_cambio='Efectivo'");
            $total = mysqli_fetch_array($sql_total);
            $totalbanco = mysqli_fetch_array(mysqli_query($enlace, "SELECT SUM(cantidad) AS precio FROM pagos WHERE id_user=$id_user AND corte = 0 AND tipo_cambio='Banco'"));
            $totalcredito = mysqli_fetch_array(mysqli_query($enlace, "SELECT SUM(cantidad) AS precio FROM pagos WHERE id_user=$id_user AND corte = 0 AND tipo_cambio='Credito'"));
            date_default_timezone_set('America/Mexico_City');
            $Fecha_hoy = date('Y-m-d');
            $cantidad=$total['precio'];
            $banco = $totalbanco['precio'];
            $credito = $totalcredito['precio'];

            $corte = 0;
            //Insertar corte.....
            if ($cantidad != "" OR $banco != "" OR $credito != "") {
                if ($banco == "") {
                    $banco = 0;
                }
                if ($cantidad == "") {
                    $cantidad = 0;
                }
                if ($credito == "") {
                    $credito = 0;
                }
                mysqli_query($enlace,"INSERT INTO cortes(usuario, fecha, cantidad, banco) VALUES ($id_user, '$Fecha_hoy', '$cantidad', '$banco')");
                $ultimo =  mysqli_fetch_array(mysqli_query($enlace, "SELECT MAX(id_corte) AS id FROM cortes WHERE usuario=$id_user"));           
           	    $corte = $ultimo['id'];
                $Mensaje = "Corte en el sistema del dia: ".$Fecha_hoy.". \nCon folio: <b>".$corte."</b> y usuario: <b>'".$cobrador['firstname']."(".$cobrador['user_name'].")"."'</b> con las cantidades totales de: \n  <b>Banco = $".$banco.". \n  Efectivo = $".$cantidad.". \n  Credito = $".$credito.".</b>";
                sendMessage($id_Chat, $Mensaje, $website);
                sendMessage($id_Chat2, $Mensaje, $website);
                sendMessage($id_Chat3, $Mensaje, $website);
            }
            
            $nombre_cobrador = $cobrador['firstname'].' '.$cobrador['lastname'];
              
            // Colores de los bordes, fondo y texto
            $this->SetFillColor(255,255,255);
            $this->SetTextColor(0,0,0);
            $this->AddPage();
            global $title;
            global $pass;
            $this->Image('../img/logo_ticket.jpg',28,4,20);
            $this->SetFont('Arial','B',13);
            $this->SetY(30);
            $this->Cell(20,4,'Corte De: ',0,0,'C',true);
            $this->Ln(5);
            $this->Cell(50,4,utf8_decode($nombre_cobrador),0,0,'C',true);
            $this->Ln(8);
            $this->Cell(32,4,'Folio: No. '.$corte,0,0,'C',true);
            $this->Ln(10);
            $this->Cell(90,4,'Fecha: '.Date('d-m-Y'),0,0,'C',true);            
            $this->Ln(10);
            $this->SetFont('Arial','B',14);            
            $this->Cell(60,4,'<< Internet >>  ',0,0,'C',true);
            $this->Ln(8);
            //---------------EFECTIVO-------------------------------------------

            $sql_efectivoI = mysqli_query($enlace, "SELECT * FROM pagos WHERE id_user=$id_user AND corte = 0 AND tipo_cambio='Efectivo' AND tipo != 'Dispositivo'");
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
                $this->MultiCell(70,4,utf8_decode("Cliente: # " .$fila['id_cliente'].'; '.$fila['descripcion']),0,'L',true);
                $this->MultiCell(70,4,utf8_decode("$ ". $fila['cantidad'].'.00'),0,'R',true);
                $this->Ln(5);
            }
            $this->SetFont('Arial','B',13);
            $this->Ln(8);
            $total_EI=  mysqli_fetch_array(mysqli_query($enlace, "SELECT SUM(cantidad) AS precio FROM pagos WHERE id_user=$id_user AND corte = 0 AND tipo_cambio='Efectivo' AND tipo != 'Dispositivo'"));
            $this->MultiCell(65,4,utf8_decode('TOTAL: $'.$total_EI['precio'].'.00'),0,'R',true);
            $this->Ln(10);
        }
            //---------------BANCO-------------------------------------------
            
            $sql_bancoI = mysqli_query($enlace, "SELECT * FROM pagos WHERE id_user=$id_user AND corte = 0 AND tipo_cambio='Banco' AND tipo != 'Dispositivo'");
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
                $this->MultiCell(70,4,utf8_decode("Cliente: # ".$fila['id_cliente'].'; '.$fila['descripcion']),0,'L',true);
                $this->MultiCell(70,4,utf8_decode("$ ". $fila['cantidad'].'.00'),0,'R',true);
                $this->Ln(5);
            }
            $this->SetFont('Arial','B',13);
            $this->Ln(8);
            $totalbancoI = mysqli_fetch_array(mysqli_query($enlace, "SELECT SUM(cantidad) AS precio FROM pagos WHERE id_user=$id_user AND corte = 0 AND tipo_cambio='Banco' AND tipo != 'Dispositivo'"));
            $this->MultiCell(65,4,utf8_decode('TOTAL: $'.$totalbancoI['precio'].'.00'),0,'R',true);
            $this->Ln(10);
            }


            //---------------CREDITO-------------------------------------------

            $sql_creditoI = mysqli_query($enlace, "SELECT * FROM pagos WHERE id_user=$id_user AND corte = 0 AND tipo_cambio='Credito' AND tipo != 'Dispositivo'");
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
                $this->MultiCell(70,4,utf8_decode("Cliente: # " .$fila['id_cliente'].'; '.$fila['descripcion']),0,'L',true);
                $this->MultiCell(70,4,utf8_decode("$ ". $fila['cantidad'].'.00'),0,'R',true);
                $this->Ln(5);
            }
            $this->SetFont('Arial','B',13);
            $this->Ln(8);
            $total_CI=  mysqli_fetch_array(mysqli_query($enlace, "SELECT SUM(cantidad) AS precio FROM pagos WHERE id_user=$id_user AND corte = 0 AND tipo_cambio='Credito' AND tipo != 'Dispositivo'"));
            $this->MultiCell(65,4,utf8_decode('TOTAL: - $'.$total_CI['precio'].'.00'),0,'R',true);
            $this->Ln(10);
        }

            $this->SetFont('Arial','B',14); 

            $this->Cell(60,4,'------------------------------------------',0,0,'C',true);
            $this->Ln(15);           
            $this->Cell(60,4,'<< Serv. Tecnico >> ',0,0,'C',true);
            $this->Ln(8);
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
                $this->MultiCell(70,4,utf8_decode("Cliente: # " .$fila['id_cliente'].'; '.$fila['descripcion']),0,'L',true);
                $this->MultiCell(70,4,utf8_decode("$ ". $fila['cantidad'].'.00'),0,'R',true);
                $this->Ln(5);
            }
            $this->SetFont('Arial','B',13);
            $this->Ln(8);
            $total_EST=  mysqli_fetch_array(mysqli_query($enlace, "SELECT SUM(cantidad) AS precio FROM pagos WHERE id_user=$id_user AND corte = 0 AND tipo_cambio='Efectivo' AND tipo = 'Dispositivo'"));            
            $this->MultiCell(65,4,utf8_decode('TOTAL: $'.$total_EST['precio'].'.00'),0,'R',true);
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
                $this->MultiCell(70,4,utf8_decode("Cliente: # ".$fila['id_cliente'].'; '.$fila['descripcion']),0,'L',true);
                $this->MultiCell(70,4,utf8_decode("$ ". $fila['cantidad'].'.00'),0,'R',true);
                $this->Ln(5);
            }
            $this->SetFont('Arial','B',13);
            $this->Ln(8);
            $totalbancoST = mysqli_fetch_array(mysqli_query($enlace, "SELECT SUM(cantidad) AS precio FROM pagos WHERE id_user=$id_user AND corte = 0 AND tipo_cambio='Banco' AND tipo = 'Dispositivo'"));            
            $this->MultiCell(65,4,utf8_decode('TOTAL: $'.$totalbancoST['precio'].'.00'),0,'R',true);
            $this->Ln(10);
            }
            $this->MultiCell(65,4,utf8_decode('Total de Pagos: '.$Todos_Pagos['count(*)']),0,'R',true);
            $this->Ln(10);

            $this->SetFont('Arial','',11);
           
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
            $this->Cell(60,4,'Servicios Integrales de Computacion ',0,0,'C',true);
            $this->SetFont('Arial','B',12);
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