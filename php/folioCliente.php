<?php
//Incluimos la libreria fpdf
include("../fpdf/fpdf.php");
include('is_logged.php');
$pass='root';
//Incluimos el archivo de conexion a la base de datos
class PDF extends FPDF{
    function folioCliente()
    {
        global $pass;
        $enlace = mysqli_connect("localhost", "root", $pass, "servintcomp");
        $rs = mysqli_query($enlace, "SELECT MAX(id_cliente) AS id FROM clientes");
        $row = mysqli_fetch_row($rs);
        $id = $row[0];
        $listado = mysqli_query($enlace, "SELECT * FROM clientes WHERE id_cliente='$id'");
        $num_filas = mysqli_num_rows($listado);
        $fila = mysqli_fetch_array($listado);
        $id_comunidad = $fila['lugar'];
        $comunidad = mysqli_fetch_array(mysqli_query($enlace, "SELECT * FROM comunidades WHERE id_comunidad='$id_comunidad'"));
        
        // Colores de los bordes, fondo y texto
        $this->SetFillColor(255,255,255);
        $this->SetTextColor(0,0,0);
        $this->AddPage();
        global $title;
        global $pass;
        $this->Image('../img/logo_ticket.jpg',28,4,20);
        $this->SetFont('Arial','B',13);
        $this->SetY(30);
        $this->SetX(14);
        $this->Cell(20,4,'No. Cliente: '.$fila['id_cliente'],0,0,'C',true);
        $this->SetFont('Arial','',13);

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
        $this->MultiCell(70,4,utf8_decode('TEL. SIC: 4339356286'),0,'L',true);
        $this->Ln($salto);
        $this->SetX(6);
        $this->MultiCell(70,4,utf8_decode('TEL. CLIENTE: '.$fila['telefono']),0,'L',true);
        $this->Ln($salto);
        $this->SetX(6);
        $this->MultiCell(70,4,utf8_decode('LUGAR: '.$comunidad['nombre']),0,'L',true);
        $this->Ln($salto);
        $this->SetX(6);
        $this->MultiCell(70,4,utf8_decode('REFERENCIA: '.$fila['referencia']),0,'L',true);
        $this->Ln($salto);
        $this->SetX(6);
        $this->MultiCell(70,4,utf8_decode('TOTAL: '.$fila['total']),0,'L',true);
        $this->Ln($salto);
        $this->SetX(6);
        $this->MultiCell(70,4,utf8_decode('DEJÓ: '.$fila['dejo']),0,'L',true);
        $this->Ln($salto);
        $this->SetX(6);
        $this->MultiCell(70,4,utf8_decode('RESTA: '.($fila['total']-$fila['dejo'])),0,'L',true);
        $this->Ln(10);
        $this->SetX(6);
        $this->MultiCell(70,4,utf8_decode('_________________________________'),0,'L',false);
        $this->SetX(6);
        $this->MultiCell(70,7,utf8_decode('FIRMA'),0,'C',false);

        $this->SetFont('Arial','B',7); 
        $this->SetX(5);

        mysqli_close($enlace);
    }
}

$pdf = new PDF('P', 'mm', array(80,297));
$pdf->SetTitle('INSTALACION');
$pdf->folioCliente();
$pdf->Output('INSTALACION','I');
?>