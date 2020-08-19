<?php
#INCLUIMOS EL ARCHIVO CON LAS LIBRERIAS DE FPDF PARA PODER CREAR ARCHIVOS CON FORMATO PDF
include("../fpdf/fpdf.php");
#INCLUIMOS EL PHP DONDE VIENE LA INFORMACION DE LA CONEXION A LA BASE DE DATOS
include('../php/conexion.php');
$id_corte = $_GET['id'];//TOMAMOS EL ID DEL CORTE PREVIAMENTE CREADO PARA¨PODERLE ASIGNAR LOS PAGOS EN EL DETALLE
    
#CREAMOS LA CLASE DEL CONTENIDO DE NUESTRO PDF
class PDF extends FPDF{
    function folioCliente(){
        #METEMOS LAS BARIABLES CREADAS FUERA DE LA CLASE PDF DENTRO DE LA MISMA
        global $id_corte;
        global $conn;            
        #TOMAMOS LA INFORMACION DEL CORTE CON EL ID GUARDADO EN LA VARIABLE $corte QUE RECIBIMOS CON EL GET
        $corte = mysqli_fetch_array(mysqli_query($conn,"SELECT * FROM cortes WHERE id_corte = $id_corte"));
        $Fecha = $corte['fecha'];//FECHA DEL CORTE
        $id_user = $corte['usuario'];//CUENTA DE LA QUE SE HIZO EL CORTE
        #TOMAMOS LA INFORMACION DE USERS DEL USUARIO
        $cobrador = mysqli_query($conn, "SELECT * FROM users WHERE user_id = $id_user");
        #SI EXIXTE EN LA TABLA USER EL ID DEL USUARIO MOSTRAMOS SU NOMBRE SI NO NO SE MUESTRA NADA
        if ($cobrador->num_rows > 0){
              while ($row = $cobrador->fetch_assoc()){
                $nombre_cobrador = $row['firstname'].' '.$row['lastname'];
              }
        } else{
                $nombre_cobrador = "";
        } 
        #CHECAMOS TODOS LAS PAGO QUE SE REGISTRARON PARA ESTE CORTE EN LA TABLA DETALLES
        $detalles = mysqli_query($conn, "SELECT * FROM detalles WHERE id_corte = $id_corte");
        $sql_total = mysqli_query($conn, "SELECT SUM(cantidad) AS precio FROM detalles INNER JOIN pagos ON detalles.id_pago = pagos.id_pago WHERE detalles.id_corte = $id_corte AND pagos.tipo_cambio ='Efectivo'");
        $total = mysqli_fetch_array($sql_total);
        $totalbanco = mysqli_fetch_array(mysqli_query($conn, "SELECT SUM(cantidad) AS precio FROM detalles INNER JOIN pagos ON detalles.id_pago = pagos.id_pago WHERE detalles.id_corte = $id_corte AND pagos.tipo_cambio ='Banco'"));
        $totalcredito = mysqli_fetch_array(mysqli_query($conn, "SELECT SUM(cantidad) AS precio FROM detalles INNER JOIN pagos ON detalles.id_pago = pagos.id_pago WHERE detalles.id_corte = $id_corte AND pagos.tipo_cambio ='Credito'"));
        #TOMAMOS LA INFORMACION DEL DEDUCIBLE CON EL ID GUARDADO EN LA VARIABLE $corte QUE RECIBIMOS CON EL GET
        $sql_Deducible = mysqli_query($conn, "SELECT * FROM deducibles WHERE id_corte = '$id_corte'");  
        if (mysqli_num_rows($sql_Deducible) > 0) {
            $Deducible = mysqli_fetch_array($sql_Deducible);
            $Deducir = $Deducible['cantidad'];
        }else{
            $Deducir = 0;
        }
        #GUARDAMOS LOS TOTALES DE CADA TIPO DE PAGO EN UNA RESPETIVA VARIABLE         
        $cantidad = $total['precio']-$Deducir;
        $banco = $totalbanco['precio'];
        $credito = $totalcredito['precio'];
		$aux = mysqli_num_rows($detalles);
		if($aux>0){
		  // Colores de los bordes, fondo y texto
          $this->SetFillColor(255,255,255);
          $this->SetTextColor(0,0,0);
          $this->AddPage();
          $this->Image('../img/logo.jpg',28,4,20);
          $this->SetFont('Arial','B',13);
          $this->SetY(30);
          $this->Cell(20,4,'Corte De: ',0,0,'C',true);
          $this->Ln(5);
          $this->Cell(50,4,utf8_decode($nombre_cobrador),0,0,'C',true);
          $this->Ln(8);
          $this->Cell(32,4,'Folio: No. '.$id_corte,0,0,'C',true);
          $this->Ln(10);
          $this->Cell(90,4,'Fecha: '.$Fecha,0,0,'C',true);            
          $this->Ln(10);
          $this->SetFont('Arial','B',14);   
          $this->Cell(60,4,'<< Internet >>  ',0,0,'C',true);
          $this->Ln(6);
            //---------------EFECTIVO-------------------------------------------
            $sql_efectivoI = mysqli_query($conn, "SELECT * FROM detalles INNER JOIN pagos ON detalles.id_pago = pagos.id_pago WHERE detalles.id_corte = $id_corte AND pagos.tipo_cambio = 'Efectivo' AND pagos.tipo != 'Dispositivo' AND pagos.tipo != 'Orden Servicio'");
            $filas = mysqli_num_rows($sql_efectivoI);
           if ($filas > 0) {
              $this->SetFont('Arial','B',12);
              $this->Cell(20,4,'Efectivo: ',0,0,'C',true);
              $this->Ln(5);
              $this->SetFont('Arial','',11);   
              $TotalEI = 0;      
              while($fila = mysqli_fetch_array($sql_efectivoI)){                
                  $this->SetX(6);
                  $this->MultiCell(70,4,utf8_decode("Cliente: # " .$fila['id_cliente'].'; '.$fila['descripcion'].'; Tipo: '.$fila['tipo']),0,'L',true);
                  $this->MultiCell(70,4,utf8_decode("$ ". $fila['cantidad'].'.00'),0,'R',true);
                  $this->Ln(5);
                  $TotalEI += $fila['cantidad'];
              }
              $this->SetFont('Arial','B',13);
              $this->Ln(8);
              $this->MultiCell(65,4,utf8_decode('SUBTOTAL: $'.$TotalEI.'.00'),0,'R',true);
              $this->Ln(10);
           }
            //---------------BANCO-------------------------------------------
             $sql_bancoI = mysqli_query($conn, "SELECT * FROM detalles INNER JOIN pagos ON detalles.id_pago = pagos.id_pago WHERE detalles.id_corte = $id_corte AND pagos.tipo_cambio = 'Banco' AND pagos.tipo != 'Dispositivo' AND pagos.tipo != 'Orden Servicio'");
            $filas = mysqli_num_rows($sql_bancoI);
           if ($filas > 0) {
            
              $this->SetFont('Arial','B',12); 
              $this->Cell(20,4,'Banco: ',0,0,'C',true);
              $this->Ln(5);
              $this->SetFont('Arial','',11);
              $TotalBI = 0;
              while($fila = mysqli_fetch_array($sql_bancoI)){                
                  $this->SetX(6);
                  $this->MultiCell(70,4,utf8_decode("Cliente: # ".$fila['id_cliente'].'; '.$fila['descripcion'].'; Tipo: '.$fila['tipo']),0,'L',true);
                  $this->MultiCell(70,4,utf8_decode("$ ". $fila['cantidad'].'.00'),0,'R',true);
                  $this->Ln(5);
                  $TotalBI += $fila['cantidad'];
              }
              $this->SetFont('Arial','B',13);
              $this->Ln(8);
              $this->MultiCell(65,4,utf8_decode('SUBTOTAL: $'.$TotalBI.'.00'),0,'R',true);
              $this->Ln(10);
           }
           //---------------CREDITO-------------------------------------------

            $sql_creditoI = mysqli_query($conn, "SELECT * FROM detalles INNER JOIN pagos ON detalles.id_pago = pagos.id_pago WHERE detalles.id_corte = $id_corte AND pagos.tipo_cambio ='Credito' AND pagos.tipo != 'Dispositivo' AND pagos.tipo != 'Orden Servicio'");
            $filas = mysqli_num_rows($sql_creditoI);
          if ($filas > 0) {

            $this->SetFont('Arial','B',12);
            $this->Cell(20,4,'Credito: ',0,0,'C',true);
            $this->Ln(5);
            $this->SetFont('Arial','',11);
            
            while($fila = mysqli_fetch_array($sql_creditoI)){
                $this->SetX(6);
                $this->MultiCell(70,4,utf8_decode("Cliente: # " .$fila['id_cliente'].'; '.$fila['descripcion'].'; Tipo: '.$fila['tipo']),0,'L',true);
                $this->MultiCell(70,4,utf8_decode("$ ". $fila['cantidad'].'.00'),0,'R',true);
                $this->Ln(5);
            }
            $this->SetFont('Arial','B',13);
            $this->Ln(8);
            $total_CI=  mysqli_fetch_array(mysqli_query($conn, "SELECT SUM(cantidad) AS precio FROM detalles INNER JOIN pagos ON detalles.id_pago = pagos.id_pago WHERE detalles.id_corte = $id_corte AND pagos.tipo_cambio ='Credito' AND tipo != 'Dispositivo'"));
            $this->MultiCell(65,4,utf8_decode('SUBTOTAL: - $'.$total_CI['precio'].'.00'),0,'R',true);
            $this->Ln(10);
          }

            $this->SetFont('Arial','B',14); 
            $this->Cell(60,4,'------------------------------------------',0,0,'C',true);
            $this->Ln(10);           
            $this->Cell(60,4,'<< Orden Serv. >> ',0,0,'C',true);
            $this->Ln(6);
              //---------------EFECTIVO-------------------------------------------
            $sql_efectivoI = mysqli_query($conn, "SELECT * FROM detalles INNER JOIN pagos ON detalles.id_pago = pagos.id_pago WHERE detalles.id_corte = $id_corte AND pagos.tipo_cambio = 'Efectivo' AND pagos.tipo = 'Orden Servicio'");
            $filas = mysqli_num_rows($sql_efectivoI);
          if ($filas > 0) {
            $this->SetFont('Arial','B',12);
            $this->Cell(20,4,'Efectivo: ',0,0,'C',true);
            $this->Ln(5);
            $this->SetFont('Arial','',11);   
            $TotalEO = 0;      
            while($fila = mysqli_fetch_array($sql_efectivoI)){                
                $this->SetX(6);
                $this->MultiCell(70,4,utf8_decode("Cliente: # " .$fila['id_cliente'].'; '.$fila['descripcion'].'; Tipo: '.$fila['tipo']),0,'L',true);
                $this->MultiCell(70,4,utf8_decode("$ ". $fila['cantidad'].'.00'),0,'R',true);
                $this->Ln(5);
                $TotalEO += $fila['cantidad'];
            }
            $this->SetFont('Arial','B',13);
            $this->Ln(8);
            $this->MultiCell(65,4,utf8_decode('SUBTOTAL: $'.$TotalEO.'.00'),0,'R',true);
            $this->Ln(10);
          }
            //---------------BANCO-------------------------------------------
             $sql_bancoI = mysqli_query($conn, "SELECT * FROM detalles INNER JOIN pagos ON detalles.id_pago = pagos.id_pago WHERE detalles.id_corte = $id_corte AND pagos.tipo_cambio = 'Banco' AND pagos.tipo = 'Orden Servicio'");
            $filas = mysqli_num_rows($sql_bancoI);
          if ($filas > 0) {
            
            $this->SetFont('Arial','B',12); 
            $this->Cell(20,4,'Banco: ',0,0,'C',true);
            $this->Ln(5);
            $this->SetFont('Arial','',11);
            $TotalBO = 0;
            while($fila = mysqli_fetch_array($sql_bancoI)){                
                $this->SetX(6);
                $this->MultiCell(70,4,utf8_decode("Cliente: # ".$fila['id_cliente'].'; '.$fila['descripcion'].'; Tipo: '.$fila['tipo']),0,'L',true);
                $this->MultiCell(70,4,utf8_decode("$ ". $fila['cantidad'].'.00'),0,'R',true);
                $this->Ln(5);
                $TotalBO += $fila['cantidad'];
            }
            $this->SetFont('Arial','B',13);
            $this->Ln(8);
            $this->MultiCell(65,4,utf8_decode('SUBTOTAL: $'.$TotalBO.'.00'),0,'R',true);
            $this->Ln(10);
          }

            $this->SetFont('Arial','B',14); 
            $this->Cell(60,4,'------------------------------------------',0,0,'C',true);
            $this->Ln(10);           
            $this->Cell(60,4,'<< Serv. Tecnico >> ',0,0,'C',true);
            $this->Ln(6);
            //---------------EFECTIVO-------------------------------------------

            $sql_efectivoST = mysqli_query($conn, "SELECT * FROM detalles INNER JOIN pagos ON detalles.id_pago = pagos.id_pago WHERE detalles.id_corte = $id_corte AND pagos.tipo_cambio = 'Efectivo' AND pagos.tipo = 'Dispositivo'");
            $filas = mysqli_num_rows($sql_efectivoST);
          if ($filas > 0) {
            $this->SetFont('Arial','B',12);
            $this->Cell(20,4,'Efectivo: ',0,0,'C',true);
            $this->Ln(5);
            $this->SetFont('Arial','',11);         
            $TotalES = 0;
            while($fila = mysqli_fetch_array($sql_efectivoST)){                
                $this->SetX(6);
                $this->MultiCell(70,4,utf8_decode("Cliente: # " .$fila['id_cliente'].'; '.$fila['descripcion'].'; Tipo: '.$fila['tipo']),0,'L',true);
                $this->MultiCell(70,4,utf8_decode("$ ". $fila['cantidad'].'.00'),0,'R',true);
                $this->Ln(5);
                $TotalES += $fila['cantidad'];
            }
            $this->SetFont('Arial','B',13);
            $this->Ln(8);           
            $this->MultiCell(65,4,utf8_decode('SUBTOTAL: $'.$TotalES.'.00'),0,'R',true);
            $this->Ln(10);
          }
            //---------------BANCO-------------------------------------------
            
            $sql_bancoST = mysqli_query($conn, "SELECT * FROM detalles INNER JOIN pagos ON detalles.id_pago = pagos.id_pago WHERE detalles.id_corte = $id_corte AND pagos.tipo_cambio = 'Banco' AND pagos.tipo = 'Dispositivo'");
            $filas = mysqli_num_rows($sql_bancoST);
          if ($filas > 0) {            
            $this->SetFont('Arial','B',12); 
            $this->Cell(20,4,'Banco: ',0,0,'C',true);
            $this->Ln(5);
            $this->SetFont('Arial','',11);
            $TotalBS = 0;
            while($fila = mysqli_fetch_array($sql_bancoST)){                
                $this->SetX(6);
                $this->MultiCell(70,4,utf8_decode("Cliente: # ".$fila['id_cliente'].'; '.$fila['descripcion'].'; Tipo: '.$fila['tipo']),0,'L',true);
                $this->MultiCell(70,4,utf8_decode("$ ". $fila['cantidad'].'.00'),0,'R',true);
                $this->Ln(5);
                $TotalBS += $fila['cantidad'];
            }
            $this->SetFont('Arial','B',13);
            $this->Ln(8);            
            $this->MultiCell(65,4,utf8_decode('SUBTOTAL: $'.$TotalBS.'.00'),0,'R',true);
            $this->Ln(10);
          }

            $Todos_Pagos = mysqli_fetch_array(mysqli_query($conn,"SELECT count(*)  FROM detalles INNER JOIN pagos ON detalles.id_pago = pagos.id_pago WHERE detalles.id_corte = $id_corte" ));
            $this->Cell(60,4,'------------------------------------------',0,0,'C',true);
            $this->Ln(8);

            $this->Cell(60,4,'<< Deducibles >> ',0,0,'C',true);
            $this->Ln(6);
            if (mysqli_num_rows($sql_Deducible) > 0) {
              $this->SetFont('Arial','',11);
              $this->Ln(6);
              $this->MultiCell(70,4,utf8_decode("Descripcion: ".$Deducible['descripcion']),0,'L',true);
              $this->MultiCell(70,4,utf8_decode("- $ ". $Deducible['cantidad'].'.00'),0,'R',true);
              $this->Ln(5);
            }
            $this->SetFont('Arial','B',14);             
            $this->Cell(60,4,'------------------------------------------',0,0,'C',true);
            $this->Ln(5);
            $this->MultiCell(60,4,utf8_decode('Total de Pagos ('.$Todos_Pagos['count(*)'].'):'),0,'L',true);
            $this->Ln(4);

            $this->SetFont('Arial','',11);
            $Telefono = mysqli_num_rows(mysqli_query($conn,"SELECT *  FROM detalles INNER JOIN pagos ON detalles.id_pago = pagos.id_pago WHERE detalles.id_corte = $id_corte AND pagos.tipo IN ('Mes-Tel', 'Min-Extra')" ));
            if ($Telefono > 0) {
              $this->Cell(60,4,utf8_decode('Telefono = '.$Telefono),0,0,'C',true);
              $this->Ln(5);
            }
            $Mes_Internet = mysqli_num_rows(mysqli_query($conn,"SELECT *  FROM detalles INNER JOIN pagos ON detalles.id_pago = pagos.id_pago WHERE detalles.id_corte = $id_corte AND pagos.tipo = 'Mensualidad'" ));
            if ($Mes_Internet > 0) {
              $this->Cell(60,4,utf8_decode('Mes-Internet = '.$Mes_Internet),0,0,'C',true);
              $this->Ln(5);
            }
            $Abonos = mysqli_num_rows(mysqli_query($conn,"SELECT *  FROM detalles INNER JOIN pagos ON detalles.id_pago = pagos.id_pago WHERE detalles.id_corte = $id_corte AND pagos.tipo = 'Abono'" ));
            if ($Abonos > 0) {
              $this->Cell(60,4,utf8_decode('Abonos = '.$Abonos),0,0,'C',true);
              $this->Ln(5);
            }
            $AntInst = mysqli_num_rows(mysqli_query($conn,"SELECT *  FROM detalles INNER JOIN pagos ON detalles.id_pago = pagos.id_pago WHERE detalles.id_corte = $id_corte AND pagos.tipo = 'Anticipo'" ));
            if ($AntInst > 0) {
              $this->Cell(60,4,utf8_decode('Anticipo Inst. = '.$AntInst),0,0,'C',true);
              $this->Ln(5);
            }
            $AbonoInst = mysqli_num_rows(mysqli_query($conn,"SELECT *  FROM detalles INNER JOIN pagos ON detalles.id_pago = pagos.id_pago WHERE detalles.id_corte = $id_corte AND pagos.tipo = 'Abono Instalacion'" ));
            if ($AbonoInst > 0) {
              $this->Cell(60,4,utf8_decode('Abono Inst. = '.$AbonoInst),0,0,'C',true);
              $this->Ln(5);
            }
            $LiquidInst = mysqli_num_rows(mysqli_query($conn,"SELECT *  FROM detalles INNER JOIN pagos ON detalles.id_pago = pagos.id_pago WHERE detalles.id_corte = $id_corte AND pagos.tipo = 'Liquidacion'" ));
            if ($LiquidInst > 0) {
              $this->Cell(60,4,utf8_decode('Liquidacion Inst. = '.$LiquidInst),0,0,'C',true);
              $this->Ln(5);
            }
            $Reporte = mysqli_num_rows(mysqli_query($conn,"SELECT *  FROM detalles INNER JOIN pagos ON detalles.id_pago = pagos.id_pago WHERE detalles.id_corte = $id_corte AND pagos.tipo = 'Reporte'" ));
            if ($Reporte > 0) {
              $this->Cell(60,4,utf8_decode('Reportes = '.$Reporte),0,0,'C',true);
              $this->Ln(5);
            }
            $AntiDisp = mysqli_num_rows(mysqli_query($conn,"SELECT *  FROM detalles INNER JOIN pagos ON detalles.id_pago = pagos.id_pago WHERE detalles.id_corte = $id_corte AND pagos.tipo = 'Dispositivo' AND pagos.descripcion = 'Anticipo'" ));
            if ($AntiDisp > 0) {
              $this->Cell(60,4,utf8_decode('Anticipo Disp. = '.$AntiDisp),0,0,'C',true);
              $this->Ln(5);
            }
            $LiquidDisp = mysqli_num_rows(mysqli_query($conn,"SELECT *  FROM detalles INNER JOIN pagos ON detalles.id_pago = pagos.id_pago WHERE detalles.id_corte = $id_corte AND pagos.tipo = 'Dispositivo' AND pagos.descripcion = 'Liquidacion'" ));
            if ($LiquidDisp > 0) {
              $this->Cell(60,4,utf8_decode('Liquidacion Disp. = '.$LiquidDisp),0,0,'C',true);
              $this->Ln(5);
            }
            $Orden = mysqli_num_rows(mysqli_query($conn,"SELECT *  FROM detalles INNER JOIN pagos ON detalles.id_pago = pagos.id_pago WHERE detalles.id_corte = $id_corte AND pagos.tipo = 'Orden Servicio'"));
            if ($Orden > 0) {
              $this->Cell(60,4,utf8_decode('Ordenes Serv. = '.$Orden),0,0,'C',true);
              $this->Ln(5);
            }
            $Otros = mysqli_num_rows(mysqli_query($conn,"SELECT *  FROM detalles INNER JOIN pagos ON detalles.id_pago = pagos.id_pago WHERE detalles.id_corte = $id_corte AND pagos.tipo = 'Otros Pagos'" ));
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
        }
    }
}
    $pdf = new PDF('P', 'mm', array(80,297));
    $pdf->SetTitle('CORTE');
    $pdf->folioCliente();
    $pdf->Output('CORTE','I');
    mysqli_close($conn);
?>