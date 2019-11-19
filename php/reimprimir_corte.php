<?php
include('../php/conexion.php');
$id_corte = $_GET['id'];
//Incluimos la libreria fpdf
include("../fpdf/fpdf.php");
include("is_logged.php");
$pass="root";
class PDF extends FPDF{
    function folioCliente(){
        global $id_corte;
        global $pass;
        global $conn;            
        $corte = mysqli_fetch_array(mysqli_query($conn,"SELECT * FROM cortes WHERE id_corte = $id_corte"));
        $Fecha = $corte['fecha'];
        $id_user = $corte['usuario'];
        $cobrador = mysqli_query($conn, "SELECT * FROM users WHERE user_id = $id_user");

        if ($cobrador->num_rows > 0){
              while ($row = $cobrador->fetch_assoc()){
                $nombre_cobrador = $row['firstname'].' '.$row['lastname'];
              }
        } else{
                $nombre_cobrador = "";
        } 
        $detalles = mysqli_query($conn, "SELECT * FROM detalles WHERE id_corte = $id_corte");
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
          $this->Ln(10);
          $this->Cell(90,4,'Fecha: '.$Fecha,0,0,'C',true);            
          $this->Ln(10);
          $this->SetFont('Arial','B',14);    

          $corte = $id_corte;      
 
          $this->Cell(60,4,'<< Internet >>  ',0,0,'C',true);
          $this->Ln(8);
            //---------------EFECTIVO-------------------------------------------
            $sql_efectivoI = mysqli_query($conn, "SELECT * FROM detalles INNER JOIN pagos ON detalles.id_pago = pagos.id_pago WHERE detalles.id_corte = $id_corte AND pagos.tipo_cambio = 'Efectivo' AND pagos.tipo != 'Dispositivo'");
            $filas = mysqli_num_rows($sql_efectivoI);
           if ($filas > 0) {
            $this->SetFont('Arial','B',12);
            $this->Cell(20,4,'Efectivo: ',0,0,'C',true);
            $this->Ln(5);
            $this->SetFont('Arial','',11);   
            $TotalEI = 0;      
            while($fila = mysqli_fetch_array($sql_efectivoI)){                
                $this->SetX(6);
                $this->MultiCell(70,4,utf8_decode("Cliente: # " .$fila['id_cliente'].'; '.$fila['descripcion']),0,'L',true);
                $this->MultiCell(70,4,utf8_decode("$ ". $fila['cantidad'].'.00'),0,'R',true);
                $this->Ln(5);
                $TotalEI += $fila['cantidad'];
            }
            $this->SetFont('Arial','B',13);
            $this->Ln(8);
            $this->MultiCell(65,4,utf8_decode('TOTAL: $'.$TotalEI.'.00'),0,'R',true);
            $this->Ln(10);
           }
            //---------------BANCO-------------------------------------------
             $sql_bancoI = mysqli_query($conn, "SELECT * FROM detalles INNER JOIN pagos ON detalles.id_pago = pagos.id_pago WHERE detalles.id_corte = $id_corte AND pagos.tipo_cambio = 'Banco' AND pagos.tipo != 'Dispositivo'");
            $filas = mysqli_num_rows($sql_bancoI);
           if ($filas > 0) {
            
            $this->SetFont('Arial','B',12); 
            $this->Cell(20,4,'Banco: ',0,0,'C',true);
            $this->Ln(5);
            $this->SetFont('Arial','',11);
            $TotalBI = 0;
            while($fila = mysqli_fetch_array($sql_bancoI)){                
                $this->SetX(6);
                $this->MultiCell(70,4,utf8_decode("Cliente: # ".$fila['id_cliente'].'; '.$fila['descripcion']),0,'L',true);
                $this->MultiCell(70,4,utf8_decode("$ ". $fila['cantidad'].'.00'),0,'R',true);
                $this->Ln(5);
                $TotalBI += $fila['cantidad'];
            }
            $this->SetFont('Arial','B',13);
            $this->Ln(8);
            $this->MultiCell(65,4,utf8_decode('TOTAL: $'.$TotalBI.'.00'),0,'R',true);
            $this->Ln(10);
           }

            $this->SetFont('Arial','B',14); 
            $this->Cell(60,4,'------------------------------------------',0,0,'C',true);
            $this->Ln(15);           
            $this->Cell(60,4,'<< Serv. Tecnico >> ',0,0,'C',true);
            $this->Ln(8);
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
                $this->MultiCell(70,4,utf8_decode("Cliente: # " .$fila['id_cliente'].'; '.$fila['descripcion']),0,'L',true);
                $this->MultiCell(70,4,utf8_decode("$ ". $fila['cantidad'].'.00'),0,'R',true);
                $this->Ln(5);
                $TotalES += $fila['cantidad'];
            }
            $this->SetFont('Arial','B',13);
            $this->Ln(8);           
            $this->MultiCell(65,4,utf8_decode('TOTAL: $'.$TotalES.'.00'),0,'R',true);
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
                $this->MultiCell(70,4,utf8_decode("Cliente: # ".$fila['id_cliente'].'; '.$fila['descripcion']),0,'L',true);
                $this->MultiCell(70,4,utf8_decode("$ ". $fila['cantidad'].'.00'),0,'R',true);
                $this->Ln(5);
                $TotalBS += $fila['cantidad'];
            }
            $this->SetFont('Arial','B',13);
            $this->Ln(8);            
            $this->MultiCell(65,4,utf8_decode('TOTAL: $'.$TotalBS.'.00'),0,'R',true);
            $this->Ln(10);
           }

            $this->SetFont('Arial','',11);
            $this->Cell(60,4,'Servicios Integrales de Computacion ',0,0,'C',true);
        }
    }
}
    $pdf = new PDF('P', 'mm', array(80,297));
    $pdf->SetTitle('CORTE');
    $pdf->folioCliente();
    $pdf->Output('CORTE','I');
    mysqli_close($conn);

?>