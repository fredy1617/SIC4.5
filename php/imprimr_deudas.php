<?php
include('../fpdf/fpdf.php');
include('../php/conexion.php');

class PDF extends FPDF{
    //Cabecera de página
    function Header(){ }
    function body(){
        global $conn;
        global $listado;

        $sql = mysqli_query($conn, "SELECT * FROM deudas WHERE liquidada = 0 ORDER BY fecha_deuda");
        $this->SetFont('Arial','B',18);
        $this->MultiCell(194,4, utf8_decode('REPORTE DE DEUDAS'),0,'C',false);
        $this->Ln(8);

        $this->SetFont('Arial','B',10);
        while($resultados = mysqli_fetch_array($sql)){
            $id_cliente = $resultados['id_cliente'];
            $cosnulta = mysqli_query($conn,"SELECT * FROM clientes WHERE id_cliente=$id_cliente");
            if (mysqli_num_rows($cosnulta)<=0) {
                $cosnulta = mysqli_query($conn,"SELECT * FROM especiales WHERE id_cliente=$id_cliente");
            } 
            $cliente = mysqli_fetch_array($cosnulta);
            $id_comunidad = $cliente['lugar'];
            $Comunidad = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM comunidades WHERE id_comunidad = $id_comunidad"));   
            $id_usuario = $resultados['usuario'];
            $usuario = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM users WHERE user_id = $id_usuario"));

            $this->MultiCell(194,4, utf8_decode('NO.'.$id_cliente. ', NOMBRE: '.$cliente['nombre'].', TELEFONO: '.$cliente['telefono']),0,'L',false);
             $this->Ln(2);
             $this->MultiCell(194,4, utf8_decode('COMUNIDAD: '.$Comunidad['nombre']. ', USUARIO: '.$usuario['firstname']),0,'L',false);
             $this->Ln(2);
             $this->MultiCell(194,4, utf8_decode('FECHA DEUDA: '.$resultados['fecha_deuda']. ', CANTIDAD: '.$resultados['cantidad']. ', DESCRIPCION: '.$resultados['descripcion']),0,'L',false);

            $this->MultiCell(180,1, utf8_decode('
                -------------------------------------------------------------------------------------------------------------------------------------------'),0,'L',false);
            $this->Ln(3);
        }
        mysqli_close($conn);
    }
    function footer(){ }
}

//Creación del objeto de la clase heredada
$pdf=new PDF();
$pdf->AddPage();
$pdf->body();
$pdf->setTitle('DEUDAS');
//Aquí escribimos lo que deseamos mostrar...
$pdf->Output();
?>