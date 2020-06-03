<?php
include('../php/conexion.php');
$id_pago = $_GET['IdPago'];

//Incluimos la libreria fpdf
include("../fpdf/fpdf.php");
include("is_logged.php");

class PDF extends FPDF{
	function folioCliente(){
		global $id_pago;
		global $conn;

		$fila = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM pagos WHERE id_pago = $id_pago"));
		$id_user = $fila['id_user'];
		$user = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM users WHERE user_id=$id_user"));
		$id_cliente = $fila['id_cliente'];

		$dispositivo = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM dispositivos WHERE id_dispositivo = $id_cliente"));
		if ($dispositivo['tipo'] == "") {
			$disp = $dispositivo['marca'];
		}else{
			$disp = $dispositivo['tipo'];
		}
		 // Colores de los bordes, fondo y texto
            $this->SetFillColor(255,255,255);
            $this->SetTextColor(0,0,0);
            $this->AddPage();
            global $title;
            global $pass;
            $this->Image('../img/logo_ticket.jpg',28,4,20);
            $this->SetFont('Arial','B',13);
            $this->SetY(30);
            $this->Cell(90,4,'Fecha: '.$fila['fecha'],0,0,'C',true);
            $this->SetFont('Arial','',10);
            $this->Ln(5);
            $this->Cell(20,4,utf8_decode('No. Folio: '.$fila['id_cliente']),0,0,'L',true);
            $this->Ln(5);
            $this->MultiCell(60,4,utf8_decode('CLIENTE:'.$dispositivo['nombre']),0,'L',true);
            $this->Ln(1);
            $this->MultiCell(60,4,utf8_decode('DISPOSITIVO:'.$disp),0,'L',true);
            $this->Ln(1);            
            $this->MultiCell(70,4,utf8_decode('TEL. SIC: 4339356286'),0,'L',true);
            $this->Ln(1);
            $this->MultiCell(60,4,utf8_decode('DESCRIPCION: '.$fila['descripcion'].' '.$fila['tipo']),0,'L',true);
            $this->Ln(1);
            $this->Cell(20,4,utf8_decode('CANTIDAD: $'.$fila['cantidad']),0,0,'L',true);
                if ($dispositivo['precio']==0) {
                    $Tot = $dispositivo['mano_obra']+$dispositivo['t_refacciones'];
                 }else{
                    $Tot = $dispositivo['precio'];
                 }
                  $sql = mysqli_query($conn, "SELECT * FROM pagos WHERE id_cliente = '$id_cliente' AND descripcion = 'Anticipo' AND tipo = 'Dispositivo'");
                  $Total_anti = 0;
                  if (mysqli_num_rows($sql)>0) {
                    
                    while ($anticipo = mysqli_fetch_array($sql)) {

                      $Total_anti += $anticipo['cantidad'];
                    }
                  }
                 $resto = $Tot-$Total_anti;
                
            $this->Ln(5);
            $this->MultiCell(60,4,utf8_decode('RESTA: $'.$resto),0,'L',true);
            $this->Ln(1);
            $this->MultiCell(60,4,utf8_decode('ATENDIO: '.$user['firstname'].' '.$user['lastname']),0,'L',true);
            $this->Ln(2);
            $this->SetFont('Arial','B',7); 
	        $this->SetX(5);
	        $this->MultiCell(69,4,utf8_decode('ADVERTENCIA:
		    1.- PASADOS 30 DÍAS NO SOMOS RESPONSABLES DE LOS EQUIPOS. 
		    2.- EN SOFTWARE (PROGRAMAS) NO HAY GARANTÍA.
		    3.- SIN ESTE TICKET, NO SE ACEPTAN RECLAMACIONES DE ANTICIPO.'),1,'L',true);
            

            mysqli_close($conn);
        }
    }

    $pdf = new PDF('P', 'mm', array(80,297));
    $pdf->SetTitle('Pago');
    $pdf->folioCliente();
    $pdf->Output('Pago','I');
?>