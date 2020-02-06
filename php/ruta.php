<?php
include('../fpdf/fpdf.php');
include('../php/conexion.php');

class PDF extends FPDF{
   //Cabecera de página
   function Header(){

    }

    function body(){
        global $conn;
        global $listado;

        $ultimo =  mysqli_fetch_array(mysqli_query($conn, "SELECT MAX(id_ruta) AS id FROM rutas WHERE estatus=0"));            

        $ultima_ruta = $ultimo['id'];
        $this->SetFont('Arial','B',18);
        $this->MultiCell(194,4, utf8_decode('RUTA INSTALACIONES No.'.$ultima_ruta),0,'C',false);

        $resultado = mysqli_query($conn, "SELECT * FROM tmp_pendientes WHERE ruta_inst = $ultima_ruta");
        $aux = 0;
        $this->SetFont('Arial','B',10);
        while($listado = mysqli_fetch_array($resultado)){
            $id_comunidad = $listado['lugar'];
            $sql_comunidad = mysqli_fetch_array(mysqli_query($conn,"SELECT nombre FROM comunidades WHERE id_comunidad=$id_comunidad"));
            
            $id_paquete = $listado['paquete'];
            $paquete = mysqli_fetch_array(mysqli_query($conn, "SELECT subida, bajada, mensualidad FROM paquetes WHERE id_paquete=$id_paquete"));
            $id_cliente = $listado['id_cliente'];
            $cliente = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM clientes WHERE id_cliente=$id_cliente"));

            $this->MultiCell(194,4, utf8_decode('NO. CLIENTE: '.$listado['id_cliente']),0,'L',false);
            $this->MultiCell(194,4, utf8_decode('NOMBRE: '.$listado['nombre']),0,'L',false);
            $this->MultiCell(194,4, utf8_decode('SERVICIO: '.$cliente['servicio']),0,'L',false);
            $this->MultiCell(194,4, utf8_decode('TELÉFONO: '.$listado['telefono']),0,'L',false);
            $this->MultiCell(194,4, utf8_decode('DIRECCIÓN: '.$listado['direccion']),0,'L',false);
            $this->MultiCell(194,4, utf8_decode('LUGAR: '.$sql_comunidad['nombre']),0,'L',false);
            $this->MultiCell(194,4, utf8_decode('REFERENCIA: '.$listado['referencia']),0,'L',false);
            $this->MultiCell(194,4, utf8_decode('TOTAL: $'.$listado['total'].'.00'),0,'L',false);
            $this->MultiCell(194,4, utf8_decode('DEJO: $'.$listado['dejo'].'.00'),0,'L',false);
            $this->MultiCell(194,4, utf8_decode('A PAGAR: $'.$listado['pagar'].'.00'),0,'L',false);
            $this->MultiCell(194,4, utf8_decode('PAQUETE: (Subida/Bajada/Mensualidad)'.$paquete['subida']."/".$paquete['bajada']."/$".$paquete['mensualidad'].".00"),0,'L',false);
            $this->MultiCell(194,4, utf8_decode('REGISTRO: '.$listado['fecha_registro']),0,'L',false);
            $this->MultiCell(180,1, utf8_decode('
                -------------------------------------------------------------------------------------------------------------------------------------------------'),0,'L',false);
        }
        

        $this->Ln(10);
        $this->SetFont('Arial','B',18);
        $this->MultiCell(194,4, utf8_decode('RUTA REPORTES No.'.$ultima_ruta),0,'C',false);

        $resultado = mysqli_query($conn, "SELECT * FROM tmp_reportes WHERE ruta = $ultima_ruta");
        $aux = 0;
        $this->SetFont('Arial','B',10);
        while($listado = mysqli_fetch_array($resultado)){
//Buscar Reporte
            $id_reporte = $listado['id_reporte'];
            $reporte = mysqli_fetch_array(mysqli_query($conn,"SELECT * FROM reportes WHERE id_reporte='$id_reporte'"));
//Buscar Cliente
            $id_cliente = $reporte['id_cliente'];
            $sql = mysqli_query($conn, "SELECT * FROM clientes WHERE id_cliente=$id_cliente");
            $filas = mysqli_num_rows($sql);
            if ($filas == 0) {
                $sql = mysqli_query($conn, "SELECT * FROM especiales WHERE id_cliente=$id_cliente");
            }
            $cliente = mysqli_fetch_array($sql);
//Buscar Comunidad
            $id_comunidad = $cliente['lugar'];
            $sql_comunidad = mysqli_fetch_array(mysqli_query($conn,"SELECT nombre FROM comunidades WHERE id_comunidad='$id_comunidad'"));

            $this->MultiCell(194,4, utf8_decode('NO. CLIENTE: '.$cliente['id_cliente']),0,'L',false);
            $this->MultiCell(194,4, utf8_decode('NOMBRE: '.$cliente['nombre']),0,'L',false);
            $this->MultiCell(194,4, utf8_decode('TELÉFONO: '.$cliente['telefono']),0,'L',false);
            $this->MultiCell(194,4, utf8_decode('DIRECCIÓN: '.$cliente['direccion']),0,'L',false);
            $this->MultiCell(194,4, utf8_decode('LUGAR: '.$sql_comunidad['nombre']),0,'L',false);
            $this->MultiCell(194,4, utf8_decode('COORDENADAS: '.$cliente['coordenadas']),0,'L',false);
            $this->MultiCell(194,4, utf8_decode('REFERENCIA: '.$cliente['referencia']),0,'L',false);
            $this->MultiCell(194,4, utf8_decode('DESCRIPCIÓN DEL REPORTE: '.$reporte['descripcion']),0,'L',false);
            $this->MultiCell(194,4, utf8_decode('FECHA DE REPORTE: '.$reporte['fecha']),0,'L',false);
            $this->MultiCell(180,1, utf8_decode('
                -------------------------------------------------------------------------------------------------------------------------------------------------'),0,'L',false);

        }

        $this->Ln(12);
        $this->SetX(19);
        $this->SetFont('Arial','B',10);
        $rep_ruta = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM reporte_rutas WHERE id_ruta = $ultima_ruta"));
        $ruta = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM rutas WHERE id_ruta = $ultima_ruta"));
        if ($rep_ruta['bobina'] == 1 AND $rep_ruta['vale'] == 1){
            $this->MultiCell(154,15,utf8_decode('1.- OTORGAR BOBINA NUEVA PARA LA RUTA ('.$ultima_ruta.') A NOMBRE DE: '.$ruta['tecnicos'].'
2.- OTORGAR VALE DE GASOLINA PARA LA RUTA ('.$ultima_ruta.') EN EL VEHICULO: '.$rep_ruta['vehiculo']),1,'C',false);
        }else if ($rep_ruta['bobina'] == 1) {
            $this->MultiCell(154,10,utf8_decode('1.- OTORGAR BOBINA NUEVA PARA LA RUTA ('.$ultima_ruta.') A NOMBRE DE: '.$ruta['tecnicos']),1,'C',false);
        }else if ($rep_ruta['vale'] == 1) {
            $this->MultiCell(154,10,utf8_decode('1.- OTORGAR VALE DE GASOLINA PARA LA RUTA ('.$ultima_ruta.') EN EL VEHICULO: '.$rep_ruta['vehiculo']),1,'C',false);
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
$pdf->setTitle('RUTA REDES');
//Aquí escribimos lo que deseamos mostrar...
$pdf->Output();
?>