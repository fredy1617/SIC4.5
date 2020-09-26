<?php
#INCLUIMOS EL ARCHIVO CON LA CONEXION A LA BASE DE DATPS
include('../php/conexion.php');
#INCLUIMOS EL ARCHIVO CON LAS LIBRERIAS DE FPDF PARA PODER CREAR ARCHIVOS CON FORMATO PDF
include("../fpdf/fpdf.php");
#INCLUIMOS EL PHP DONDE VIENE LA INFORMACION DEL INICIO DE SESSION
include('is_logged.php');

$id_dispositivo =$_GET['id'];

#CREAMOS LA CLASE DEL CONTENIDO DE NUESTRO PDF
class PDF extends FPDF{
    function folioCliente(){
        #METEMOS LAS BARIABLES CREADAS FUERA DE LA CLASE PDF DENTRO DE LA MISMA
        global $id_dispositivo;
        global $conn;
        
        $listado = mysqli_query($conn, "SELECT * FROM dispositivos WHERE id_dispositivo=$id_dispositivo");
        $num_filas = mysqli_num_rows($listado);
        $fila = mysqli_fetch_array($listado);
        $id_tecnico = $fila['tecnico'];
        $tecnico = mysqli_fetch_array(mysqli_query($conn,"SELECT * FROM users WHERE user_id=$id_tecnico"));

        // Colores de los bordes, fondo y texto
        $this->SetFillColor(255,255,255);
        $this->SetTextColor(0,0,0);
        $this->AddPage();
        global $title;
        global $pass;
        $this->Image('../img/logo_ticket.jpg',28,4,20);
        $this->SetFont('Arial','B',13);
        $this->SetY(30);
        $this->SetX(6);
        $this->Cell(20,4,'Folio: '.$fila['id_dispositivo'],0,0,'C',true);
        $this->SetFont('Arial','',13);
        $this->SetY(30);
        $this->SetX(30);
        $this->Cell(40,4,'Fecha: '.$fila['fecha'],0,0,'C',true);

        //Variable salto de linea
        $salto=0;

        $this->SetFont('Arial','',10); 
        $this->SetY(39);
        $this->SetX(6);
        $this->MultiCell(70,4,utf8_decode('CLIENTE: '.$fila['nombre']),0,'L',true);
        $this->Ln($salto);
        $this->SetX(6);
        $this->MultiCell(70,4,utf8_decode('REGISTRÓ: '.$_SESSION['user_name']),0,'L',true);
        $this->Ln($salto);
        $this->SetX(6);
        $this->MultiCell(70,4,utf8_decode('TEL. SIC: 9356286'),0,'L',true);
        $this->Ln($salto);
        $this->SetX(6);
        $this->MultiCell(70,4,utf8_decode('MARCA: '.$fila['marca']),0,'L',true);
        $this->Ln($salto);
        $this->SetX(6);
        $this->MultiCell(70,4,utf8_decode('MODELO: '.$fila['modelo']),0,'L',true);
        $this->Ln($salto);
        $this->SetX(6);
        $this->MultiCell(70,4,utf8_decode('COLOR: '.$fila['color']),0,'L',true);
        $this->Ln($salto);
        $this->SetX(6);
        $this->MultiCell(70,4,utf8_decode('FALLA: '.$fila['falla']),0,'L',true);
        $this->Ln($salto);
        $this->SetX(6);
        $this->MultiCell(70,4,utf8_decode('NOTA AL RECIBIR: '.$fila['cables']),0,'L',true);
        $this->Ln(10);
        $this->SetX(6);
        $this->MultiCell(70,4,utf8_decode('_________________________________'),0,'L',false);
        $this->SetX(6);
        $this->MultiCell(70,7,utf8_decode('FIRMA'),0,'C',false);

        $this->SetFont('Arial','B',7); 
        $this->SetX(5);
        $this->MultiCell(69,4,utf8_decode('ADVERTENCIA:
    1.- PASADOS 30 DÍAS NO SOMOS RESPONSABLES DE LOS EQUIPOS. 
    2.- EN SOFTWARE (PROGRAMAS) NO HAY GARANTÍA.
    3.- SIN ESTE TICKET, NO SE HARÁ LA ENTREGA DEL EQUIPO.'),1,'L',true);
    }
    }
global $conn;
global $id_dispositivo;

$listado = mysqli_query($conn, "SELECT * FROM dispositivos WHERE id_dispositivo=$id_dispositivo");
$num_filas = mysqli_num_rows($listado);
$fila = mysqli_fetch_array($listado);

$pdf = new PDF('P', 'mm', array(80,297));
$pdf->SetTitle('Folio_'.$fila['id_dispositivo'].'_'.$fila['nombre'].'_'.'_'.$fila['marca'].'_'.$fila['modelo'].'_color_'.$fila['color']);
$pdf->folioCliente();
$pdf->Output('Folio_'.$fila['id_dispositivo'].'_'.$fila['nombre'].'_'.'_'.$fila['marca'].'_'.$fila['modelo'].'_color_'.$fila['color'],'I');
?>