<?php
include('../php/conexion.php');
$id_ficha = $_GET['Ficha'];

//Incluimos la libreria fpdf
include("../fpdf/fpdf.php");
include("is_logged.php");

class PDF extends FPDF{
	function folioCliente(){
		global $id_ficha;
		global $conn;

		$ficha = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM fichas WHERE id_ficha = $id_ficha"));
		$id_user = $ficha['usuario'];
		$user = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM users WHERE user_id=$id_user"));
		 // Colores de los bordes, fondo y texto
            $this->SetFillColor(255,255,255);
            $this->SetTextColor(0,0,0);
            $this->AddPage();
            global $title;
            global $pass;
            $this->Image('../img/logo.jpg',28,4,20);
            $this->SetFont('Arial','B',14);
            $this->SetY(30);
            $this->Cell(90,4,'Fecha: '.$ficha['fecha'],0,0,'C',true);
            $this->SetFont('Arial','',12);
            $this->Ln(10);
            $this->Cell(20,4,utf8_decode('No. Ficha: '.$id_ficha),0,0,'L',true);
            $this->Ln(5);
            $this->MultiCell(60,4,utf8_decode('Atendió: '.$user['firstname'].' '.$user['lastname']),0,'L',true);
            $this->Ln(5);
            $this->SetFont('Arial','B',10);
                
            $this->MultiCell(60,7,utf8_decode('Usuario: '.$ficha['usuario_ficha']),1,'L',true);
            $this->MultiCell(60,7,utf8_decode('Contraseña: '.$ficha['password']),1,'L',true);
            $this->Ln(10);
            $this->SetFont('Arial','B',12);
            $this->MultiCell(60,7,utf8_decode(' 1.- Ficha no! transferible.  2.- Ficha valida por 1 hora de consumo de internet.'),1,'L',true);
            $this->Ln(5);
            mysqli_close($conn);
        }
    }

    $pdf = new PDF('P', 'mm', array(80,297));
    $pdf->SetTitle('Pago');
    $pdf->folioCliente();
    $pdf->Output('Pago','I');
?>