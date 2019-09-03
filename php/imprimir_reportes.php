<?php
include('../fpdf/fpdf.php');
include('../php/conexion.php');

class PDF extends FPDF{
   //Cabecera de página
   function Header(){

    }

    function body(){
        global $conn;

        date_default_timezone_set('America/Mexico_City');
        $Hoy = date('Y-m-d');
        $sql_rep = mysqli_query($conn, "SELECT * FROM reportes  WHERE (fecha_visita = '$Hoy'  AND atender_visita = 0) OR (fecha_visita < '$Hoy' AND atender_visita = 0 AND visita = 1) OR atendido != 1 OR atendido IS NULL  ORDER BY fecha");

        $this->SetFont('Arial','B',18);
        $this->MultiCell(194,4, utf8_decode('REPORTES PENDIENTES'),0,'C',false);
        $this->Ln(8);

        $this->SetFont('Arial','B',10);
        while($resultados = mysqli_fetch_array($sql_rep)){
            $id_cliente = $resultados['id_cliente'];
            $sql = mysqli_query($conn, "SELECT * FROM clientes WHERE id_cliente=$id_cliente");
            $filas = mysqli_num_rows($sql);
            if ($filas == 0) {
              $sql = mysqli_query($conn, "SELECT * FROM especiales WHERE id_cliente=$id_cliente");
            }
            $cliente = mysqli_fetch_array($sql);
            $id_comunidad = $cliente['lugar'];
            $Comunidad = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM comunidades WHERE id_comunidad = $id_comunidad"));   

            $this->MultiCell(194,4, utf8_decode('NO.'.$id_cliente. ', NOMBRE: '.$cliente['nombre'].', TELEFONO: '.$cliente['telefono']),0,'L',false);
             $this->Ln(2);
             $this->MultiCell(194,4, utf8_decode('COMUNIDAD: '.$Comunidad['nombre']. ', DIRECCIÓN: '.$cliente['direccion']),0,'L',false);
             $this->Ln(2);
             $this->MultiCell(194,4, utf8_decode('REFERENCIA: '.$cliente['referencia']),0,'L',false);
             $this->Ln(2);
             $this->MultiCell(194,4, utf8_decode('DESCRIPCIÓN DEL REPORTE: '.$resultados['descripcion']),0,'L',false);
             $this->Ln(2);
             $this->MultiCell(194,4, utf8_decode('FECHA DE REPORTE: '.$resultados['fecha']),0,'L',false);

            $this->MultiCell(180,1, utf8_decode('
                -------------------------------------------------------------------------------------------------------------------------------------------'),0,'L',false);

            $this->Ln(3);

        }
        


        mysqli_close($conn);

    }

    function footer(){

    }
}

//Creación del objeto de la clase heredada
$pdf=new PDF();
$pdf->AddPage();
$pdf->body();
$pdf->setTitle('REPORTES PENDIENTES');
//Aquí escribimos lo que deseamos mostrar...
$pdf->Output();
?>