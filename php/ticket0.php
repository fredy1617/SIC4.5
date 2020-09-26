<?php
#INCLUIMOS EL ARCHIVO CON LA CONEXION A LA BASE DE DATPS
include('../php/conexion.php');
$id_cliente = $_GET['Id'];//TOMAMOS EL ID DEL CLIENTE PREVIAMENTE CREADO PARA PODERLE VER SU INFORMACION

#INCLUIMOS EL ARCHIVO CON LAS LIBRERIAS DE FPDF PARA PODER CREAR ARCHIVOS CON FORMATO PDF
include("../fpdf/fpdf.php");
#INCLUIMOS EL PHP DONDE VIENE LA INFORMACION DEL INICIO DE SESSION
include('is_logged.php');

#CREAMOS LA CLASE DEL CONTENIDO DE NUESTRO PDF
class PDF extends FPDF{
	function folioCliente(){
        #METEMOS LAS BARIABLES CREADAS FUERA DE LA CLASE PDF DENTRO DE LA MISMA
		global $id_cliente;
		global $conn;

		$fila = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM clientes WHERE id_cliente = $id_cliente"));
		$id_user = $_SESSION['user_id'];
		$user = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM users WHERE user_id=$id_user"));
		// Colores de los bordes, fondo y texto
            $this->SetFillColor(255,255,255);
            $this->SetTextColor(0,0,0);
            $this->AddPage();
            $this->Image('../img/logo_ticket.jpg',28,4,20);
            $this->SetFont('Arial','B',13);
            $this->SetY(30);
            date_default_timezone_set('America/Mexico_City');
            $Hoy = date('Y-m-d');
            $this->Cell(90,4,'Fecha: '.$Hoy,0,0,'C',true);
            $this->SetFont('Arial','',10);
            $this->Ln(5);
            $this->Cell(20,4,utf8_decode('No. Cliente: '.$fila['id_cliente']),0,0,'L',true);
            $this->Ln(5);
            $this->MultiCell(60,4,utf8_decode('Nombre:'.$fila['nombre']),0,'L',true);
            $this->Ln(1);
            $this->MultiCell(60,4,utf8_decode('Descripción: Solucion de reporte'),0,'L',true);
            $this->Ln(1);
            $this->MultiCell(60,4,utf8_decode('Tipo: ESTE SERVICIO NO GENERO NINGUN COSTO'),0,'L',true);
            $this->Ln(1);
            $this->MultiCell(60,4,utf8_decode('Atendió: '.$user['firstname'].' '.$user['lastname']),0,'L',true);
            $this->Ln(2);
            $this->SetFont('Arial','B',9);
            $this->MultiCell(60,7,utf8_decode('MÁS QUE TECNOLOGÍA SOMOS COMUNICACIÓN.
RECOMENDACIONES:
1.- Contar con línea regulada "regulador de corriente".
2.- No modificar orden del cableado.
3.- No presionar botón de reset de los equipos.
4.- En caso de falla comunicarse al 433 935 62 86.'),1,'C',true);
            mysqli_close($conn);
        }
    }

    $pdf = new PDF('P', 'mm', array(80,297));
    $pdf->SetTitle('Pago');
    $pdf->folioCliente();
    $pdf->Output('Pago','I');
?>