<?php
//Incluimos la libreria fpdf
include("../fpdf/fpdf.php");
include('is_logged.php');
$pass='root';
$id_dispositivo = $_GET['id'];
//Incluimos el archivo de conexion a la base de datos
class PDF extends FPDF{
    function folioCliente()
    {
        global $pass;
        global $id_dispositivo;
        $enlace = mysqli_connect("localhost", "root", $pass, "servintcomp");
        $listado = mysqli_query($enlace, "SELECT * FROM dispositivos WHERE id_dispositivo='$id_dispositivo'");
        $num_filas = mysqli_num_rows($listado);
        $fila = mysqli_fetch_array($listado);
        $id_User = $_SESSION['user_id'];
        $User = mysqli_fetch_array(mysqli_query($enlace, "SELECT * FROM users WHERE user_id = '$id_User'"));
            
        
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
        $this->Ln($salto);
        $this->SetX(6);
        $this->MultiCell(70,4,utf8_decode('TEL. SIC: 4339356286'),0,'L',true);
        $this->Ln($salto);
        $this->SetX(6);
        $this->MultiCell(70,4,utf8_decode('TEL. CLIENTE: '.$fila['telefono']),0,'L',true);
        $this->Ln($salto);
        $this->SetX(6);
        $this->MultiCell(70,4,utf8_decode('DISPOSITIVO: '.$fila['tipo']),0,'L',true); 
        $this->Ln($salto);
        $this->SetX(6);
        $this->MultiCell(70,4,utf8_decode('MARCA: '.$fila['marca']),0,'L',true);
        $this->Ln($salto);
        $this->SetX(6);
        $this->MultiCell(70,4,utf8_decode('MODELO: '.$fila['modelo']),0,'L',true);
        $this->Ln($salto);
        $this->SetX(6);
        $this->MultiCell(70,4,utf8_decode('CONTRASEÑA: '.$fila['contra']),0,'L',true);
        $this->Ln($salto);
        $this->SetX(6);
        $this->MultiCell(70,4,utf8_decode('MAS: '.$fila['extras']),0,'L',true);
        $this->Ln($salto);
        $this->SetX(6);
        $this->MultiCell(70,4,utf8_decode('FALLA: '.$fila['falla']),0,'L',true);
        $this->Ln(8);
        $this->SetX(6);
        $this->MultiCell(70,4,utf8_decode('_________________________________'),0,'L',false);
        $this->SetX(6);
        $this->MultiCell(70,7,utf8_decode('Firma Recibido ('.$User['firstname'].' '.$User['lastname'].')'),0,'C',false);

        $this->SetFont('Arial','B',7); 
        $this->SetX(5);
        $this->MultiCell(69,4,utf8_decode('ADVERTENCIA:
    1.- PASADOS 30 DÍAS NO SOMOS RESPONSABLES DE LOS EQUIPOS. 
    2.- EN SOFTWARE (PROGRAMAS) NO HAY GARANTÍA.
    3.- SIN ESTE TICKET, NO SE HARÁ LA ENTREGA DEL EQUIPO.'),1,'L',true);

       
        mysqli_close($enlace);
    }
    function folioCliente2()
    {
        global $pass;        
        global $id_dispositivo;
        $enlace = mysqli_connect("localhost", "root", $pass, "servintcomp");
        $listado = mysqli_query($enlace, "SELECT * FROM dispositivos WHERE id_dispositivo='$id_dispositivo'");
        $num_filas = mysqli_num_rows($listado);
        $fila = mysqli_fetch_array($listado);
        $id_User = $fila['recibe'];
        $User = mysqli_fetch_array(mysqli_query($enlace, "SELECT * FROM users WHERE user_id = '$id_User'"));

            
        
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
        $this->MultiCell(70,4,utf8_decode('DISPOSITIVO: '.$fila['tipo']),0,'L',true);
        $this->Ln($salto);
        $this->SetX(6);
        $this->MultiCell(70,4,utf8_decode('REGISTRÓ: '.$User['firstname'].' '.$User['lastname']),0,'L',true);
        $this->Ln($salto);
        $this->SetX(6);
        $this->MultiCell(70,4,utf8_decode('TEL. SIC: 4339356286'),0,'L',true);
        $this->Ln($salto);
        $this->SetX(6);
        $this->MultiCell(70,4,utf8_decode('TEL. CLIENTE: '.$fila['telefono']),0,'L',true);
        $this->Ln($salto);
        $this->SetX(6);
        $this->MultiCell(70,4,utf8_decode('MARCA: '.$fila['marca']),0,'L',true);
        $this->Ln($salto);
        $this->SetX(6);
        $this->MultiCell(70,4,utf8_decode('MODELO: '.$fila['modelo']),0,'L',true);
        $this->Ln($salto);
        $this->SetX(6);
        $this->MultiCell(70,4,utf8_decode('CONTRASEÑA: '.$fila['contra']),0,'L',true);
        $this->Ln($salto);
        $this->SetX(6);
        $this->MultiCell(70,4,utf8_decode('MAS: '.$fila['extras']),0,'L',true);
        $this->Ln($salto);
        $this->SetX(6);
        $this->MultiCell(70,4,utf8_decode('FALLA: '.$fila['falla']),0,'L',true);
        $this->Ln(8);
        $this->SetX(6);
        $this->MultiCell(70,4,utf8_decode('_________________________________'),0,'L',false);
        $this->SetX(6);
        $this->MultiCell(70,7,utf8_decode('Firma de Autorización'),0,'C',false);

        $this->SetFont('Arial','B',7); 
        $this->SetX(5);
        $this->MultiCell(69,4,utf8_decode('ADVERTENCIA:
    1.- PASADOS 30 DÍAS NO SOMOS RESPONSABLES DE LOS EQUIPOS. 
    2.- EN SOFTWARE (PROGRAMAS) NO HAY GARANTÍA.
    3.- SIN ESTE TICKET, NO SE HARÁ LA ENTREGA DEL EQUIPO.'),1,'L',true);

       
        mysqli_close($enlace);
    }
    }
global $pass;
global $id_dispositivo;
$enlace = mysqli_connect("localhost", "root", $pass, "servintcomp");
$listado = mysqli_query($enlace, "SELECT * FROM dispositivos WHERE id_dispositivo='$id_dispositivo'");
$num_filas = mysqli_num_rows($listado);
$fila = mysqli_fetch_array($listado);

$pdf = new PDF('P', 'mm', array(80,297));
$pdf->SetTitle('Folio_'.$fila['id_dispositivo'].'_'.$fila['nombre'].'_'.$fila['tipo'].'_'.$fila['marca'].'_'.$fila['modelo']);

$pdf->folioCliente();
$pdf->folioCliente2();
$pdf->Output('Folio_'.$fila['id_dispositivo'].'_'.$fila['nombre'].'_'.$fila['tipo'].'_'.$fila['marca'].'_'.$fila['modelo'],'I');
?>