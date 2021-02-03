<?php
include('../fpdf/fpdf.php');
include('../php/conexion.php');
$folio = $_GET['folio'];

class PDF extends FPDF{
   //Cabecera de página
    function Header(){    }

    function body(){
        global $conn;
        global $folio;

        date_default_timezone_set('America/Mexico_City');
        $Hoy = date('Y-m-d');
        $Pedido = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM pedidos WHERE folio = $folio"));
        $detalles_pedido = mysqli_query($conn, "SELECT * FROM detalles_pedidos WHERE folio = $folio");
        $Estatus = '';
        if ($Pedido['cerrado'] == 0 AND $Pedido['estatus'] == 'No Autorizado') {
            $Estatus = 'Pendiente';
        }else if ($Pedido['cerrado'] == 1 AND $Pedido['estatus'] == 'No Autorizado') {
            $Estatus = 'Cerrado';
        }else{
            $Estatus = $Pedido['estatus'];
        }
        $this->SetFont('Arial','B',18);
        $this->MultiCell(194,4, utf8_decode('PEDIDO No. '.$folio),0,'C',false);
        $this->Ln(8);
        $this->SetFont('Arial','B',10);
        $this->MultiCell(180,10,utf8_decode('       Cliente: '.$Pedido['nombre'].'
        Orden: '.$Pedido['id_orden'].'                                         Estatus: '.$Estatus.'
        Fecha de Creación: '.$Pedido['fecha'].'             Hora de Creación: '.$Pedido['hora'].'
        Fecha Cerrado: '.$Pedido['fecha_cerrado'].'                    Fecha Autorizacion: '.$Pedido['fecha_autorizado'].'
        Fecha Completado: '.$Pedido['fecha_completo']),1,'L',false);
        $this->Ln(8);
        $this->MultiCell(180,1, utf8_decode('
                -------------------------------------------------------------------------------------------------------------------------------------------'),0,'L',false);
        $this->Ln(2);
        while($material = mysqli_fetch_array($detalles_pedido)){ 

            $this->MultiCell(194,4, utf8_decode('MATERIAL: '.$material['descripcion']),2,'L',false);
            $this->Ln(2);
            $this->MultiCell(194,4, utf8_decode('OBSERVACIÓN: '.$material['observacion']),0,'L',false);
            $this->Ln(2);
            $this->MultiCell(180,1, utf8_decode('
                -------------------------------------------------------------------------------------------------------------------------------------------'),0,'L',false);
            $this->Ln(2);
        }
        mysqli_close($conn);
    }

    function footer(){    }
}

//Creación del objeto de la clase heredada
$pdf=new PDF();
$pdf->AddPage();
$pdf->body();
$pdf->setTitle('PEDIDO No. '.$folio);
//Aquí escribimos lo que deseamos mostrar...
$pdf->Output();
?>