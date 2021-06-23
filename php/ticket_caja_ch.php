<?php
include('../php/conexion.php');
$id = $_GET['Id'];

//Incluimos la libreria fpdf
include("../fpdf/fpdf.php");
include("is_logged.php");

class PDF extends FPDF{
	function folioCliente(){
		global $id;
		global $conn;

		$fila = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM historila_caja_ch WHERE id = $id"));
        $id_user = $fila['usuario'];
        $user = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM users WHERE user_id=$id_user"));

		 // Colores de los bordes, fondo y texto
            $this->SetFillColor(255,255,255);
            $this->SetTextColor(0,0,0);
            $this->AddPage();
            $this->Image('../img/logo_ticket.jpg',28,4,20);
            $this->SetFont('Arial','B',12);
            $this->SetY(30);
            $this->Ln(3);
            $this->Cell(80,4,'Fecha: '.$fila['fecha'].' '.$fila['hora'],0,0,'C',true);
            $this->SetFont('Arial','B',10);
            $this->Ln(7);
            $this->Cell(20,4,utf8_decode('Folio: '.$id),0,0,'L',true);
            $this->SetFont('Arial','',10);
            $this->Ln(5);
            $this->MultiCell(60,4,utf8_decode('Usuario: '.$user['firstname']),0,'L',true);
            $this->Ln(1);
            $this->Cell(20,4,utf8_decode('Tipo: '.$fila['tipo']),0,0,'L',true);
            $this->Ln(5);
            $this->MultiCell(60,4,utf8_decode('Descripcion:'.$fila['descripcion']),0,'L',true);
            $this->Ln(6);
            $this->SetFont('Arial','B',11);
            $this->Cell(90,4,utf8_decode('Cantidad: $'.$fila['cantidad'].'.00'),0,0,'C',true);
            $this->Ln(12);
            $this->SetFont('Arial','',10);
            $this->MultiCell(70,4,utf8_decode('_________________________________'),0,'L',false);
            $this->SetX(6);
            $this->MultiCell(70,7,utf8_decode('Firma de recibido'),0,'C',false);
            $this->Ln(7);
            $this->MultiCell(70,4,utf8_decode('_________________________________'),0,'L',false);
            $this->SetX(6);
            $this->MultiCell(70,7,utf8_decode('Firma de entregado'),0,'C',false);
            $this->Ln(2);
            $this->SetFont('Arial','B',9);
            $this->MultiCell(60,7,utf8_decode('ACCION DE CAJA CHICA
Servicios Integrales de Computaciòn.'),1,'C',true); 
            mysqli_close($conn);
        }
    }
    $pdf = new PDF('P', 'mm', array(80,297));
    $pdf->SetTitle('Caja Chica');
    $pdf->folioCliente();
    $pdf->Output('Caja Chica','I');
?>