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
        $id_User = $fila['recibe'];
        if ($id_User == NULL) {
            $id_User =  $_SESSION['user_id'];
        }
        $User = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM users WHERE user_id = '$id_User'"));

        $registro = $User['firstname'].' '.$User['lastname'];

        // Colores de los bordes, fondo y texto
        $this->SetFillColor(255,255,255);
        $this->SetTextColor(0,0,0);
        $this->AddPage();
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
        $this->MultiCell(70,4,utf8_decode('REGISTRÓ: '.$registro),0,'L',true);
        $this->Ln($salto);
        $this->SetX(6);
        $this->MultiCell(70,4,utf8_decode('TEL. SIC: 9356286'),0,'L',true);
        $this->Ln($salto);
        $this->SetX(6);
        $this->MultiCell(70,4,utf8_decode('DISPOSITIVO: '.$fila['tipo'].' '.$fila['marca']),0,'L',true);
        $this->Ln($salto);
        $this->SetX(6);
        $this->MultiCell(70,4,utf8_decode('MODELO: '.$fila['modelo']),0,'L',true);
        $this->Ln($salto);
        $this->SetX(6);
        if ($fila['extras'] == NULL) {
            $this->MultiCell(70,4,utf8_decode('MAS: color '.$fila['color'].', con cable(s) de '.$fila['cables']),0,'L',true);
        }else{
          $this->MultiCell(70,4,utf8_decode('MAS: '.$fila['extras']),0,'L',true);
        }
        $this->Ln($salto);
        $this->SetX(6);
        $this->MultiCell(70,4,utf8_decode('FALLA: '.$fila['falla']),0,'L',true);
        $this->Ln($salto);
        $this->SetX(6);
        $this->Ln($salto);
        $this->SetX(6);
        $this->MultiCell(70,4,utf8_decode('------------------------------------------------------'),0,'L',true);
        $this->Ln($salto);
        $this->SetX(6);
        $this->MultiCell(70,4,utf8_decode('ESTATUS: '.$fila['estatus']),0,'L',true);
        $this->Ln($salto);
        $this->SetX(6);
        $this->MultiCell(70,4,utf8_decode('OBSERVACIONES: '.$fila['observaciones']),0,'L',true);
        $this->Ln($salto);
        $this->SetX(6);
        $this->MultiCell(70,4,utf8_decode('------------------------------------------------------'),0,'L',true);
        
        $SqlRefacciones = mysqli_query($conn, "SELECT * FROM refacciones WHERE id_dispositivo = '$id_dispositivo'");
        $filas = mysqli_num_rows($SqlRefacciones);
        $sub = 0;
        if ($filas > 0) {
            $this->Ln(5);
            $this->SetFont('Arial','B',13);
            $this->Cell(20,4,'Refacciones: ',0,0,'C',true);
            $this->Ln(5);
            $this->SetFont('Arial','',10);
            $this->Ln(5);
            
            while($refaccion = mysqli_fetch_array($SqlRefacciones)){
                $this->SetX(6);
                $this->MultiCell(70,4,utf8_decode(" - ". $refaccion['descripcion']),0,'L',true);
                $this->MultiCell(70,4,utf8_decode("$ ". $refaccion['cantidad'].'.00'),0,'R',true);
                $sub=$sub+$refaccion['cantidad'];
            }
            }
            $sql = mysqli_query($conn, "SELECT * FROM pagos WHERE id_cliente = '$id_dispositivo' AND descripcion = 'Anticipo' AND tipo = 'Dispositivo'");
            $Total_anti = 0;
            if (mysqli_num_rows($sql)>0) {
                            
                while ($anticipo = mysqli_fetch_array($sql)) {
                  $Total_anti += $anticipo['cantidad'];
                }
            }
            if ($fila['precio'] == 0) {
                $this->SetFont('Arial','B',10);
                $this->MultiCell(70,4,utf8_decode("SUBTOTAL"),0,'L',true);
                $this->MultiCell(70,4,utf8_decode("$ ".$fila['t_refacciones'].".00"),0,'R',true);
                $Total=$fila['mano_obra']+$fila['t_refacciones']-$Total_anti;
                $this->MultiCell(70,4,utf8_decode("Mano de Obra"),0,'L',true);
                $this->MultiCell(70,4,utf8_decode("$ ".$fila['mano_obra'].".00"),0,'R',true);
                $this->MultiCell(70,4,utf8_decode("Anticipó"),0,'L',true);
                $this->MultiCell(70,4,utf8_decode("- $ ".$Total_anti.".00"),0,'R',true);
                $this->SetFont('Arial','B',12);
                $this->Ln(5);
                $this->MultiCell(70,4,utf8_decode('TOTAL: $'.$Total.'.00'),0,'R',true);
                $this->Ln(10);
            }else{
                $this->SetFont('Arial','B',10);
                $this->MultiCell(70,4,utf8_decode("SUBTOTAL"),0,'L',true);
                $this->MultiCell(70,4,utf8_decode("$ ".$sub.".00"),0,'R',true);
                $mano=$fila['precio']-$sub;
                $this->MultiCell(70,4,utf8_decode("Mano de Obra"),0,'L',true);
                $this->MultiCell(70,4,utf8_decode("$ ".$mano.".00"),0,'R',true);
                $this->MultiCell(70,4,utf8_decode("Anticipó"),0,'L',true);
                $this->MultiCell(70,4,utf8_decode("- $ ".$Total_anti.".00"),0,'R',true);
                $this->SetFont('Arial','B',12);
                $this->Ln(5);
                $Total=$fila['precio']-$Total_anti;
                $this->MultiCell(70,4,utf8_decode('TOTAL: $'.$Total.'.00'),0,'R',true);
                $this->Ln(10);
            }
        $this->SetX(6);
        $this->SetFont('Arial','',10);
        $this->MultiCell(70,4,utf8_decode('_________________________________'),0,'L',false);
        $this->SetX(6);
        $this->MultiCell(70,7,utf8_decode('Firma del Tecnico ('.$tecnico['user_name'].')'),0,'C',false);

        $this->SetFont('Arial','B',7); 
        $this->SetX(5);
        $this->MultiCell(69,4,utf8_decode('ADVERTENCIA:
            1.- PASADOS 30 DÍAS NO SOMOS RESPONSABLES DE LOS EQUIPOS. 
            2.- EN SOFTWARE (PROGRAMAS) NO HAY GARANTÍA.
            3.- SIN ESTE TICKET NO SE ACEPTAN RECLAMACIONES.'),1,'L',true);

    }

    function folioCliente2(){
        global $id_dispositivo;
        global $conn;
        $listado = mysqli_query($conn, "SELECT * FROM dispositivos WHERE id_dispositivo=$id_dispositivo");
        $num_filas = mysqli_num_rows($listado);
        $fila = mysqli_fetch_array($listado);
        $id_tecnico = $fila['tecnico'];
        $tecnico = mysqli_fetch_array(mysqli_query($conn,"SELECT * FROM users WHERE user_id=$id_tecnico"));  $id_User = $fila['recibe'];
        if ($id_User == NULL) {
            $id_User =  $_SESSION['user_id'];
        }
        $User = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM users WHERE user_id = '$id_User'"));

        $registro = $User['firstname'].' '.$User['lastname'];

        // Colores de los bordes, fondo y texto
        $this->SetFillColor(255,255,255);
        $this->SetTextColor(0,0,0);
        $this->AddPage();
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
        $this->MultiCell(70,4,utf8_decode('REGISTRÓ: '.$registro),0,'L',true);
        $this->Ln($salto);
        $this->SetX(6);
        $this->MultiCell(70,4,utf8_decode('TEL. SIC: 9356286'),0,'L',true);
        $this->Ln($salto);
        $this->SetX(6);
        $this->MultiCell(70,4,utf8_decode('DISPOSITIVO: '.$fila['tipo'].' '.$fila['marca']),0,'L',true);
        $this->Ln($salto);
        $this->SetX(6);
        $this->MultiCell(70,4,utf8_decode('MODELO: '.$fila['modelo']),0,'L',true);
        $this->Ln($salto);
        $this->SetX(6);
        if ($fila['extras'] == NULL) {
            $this->MultiCell(70,4,utf8_decode('MAS: color '.$fila['color'].', con cable(s) de '.$fila['cables']),0,'L',true);
        }else{
          $this->MultiCell(70,4,utf8_decode('MAS: '.$fila['extras']),0,'L',true);
        }
        $this->Ln($salto);
        $this->SetX(6);
        $this->MultiCell(70,4,utf8_decode('FALLA: '.$fila['falla']),0,'L',true);
        $this->Ln($salto);
        $this->SetX(6);
        $this->Ln($salto);


        $this->SetX(6);
        $this->MultiCell(70,4,utf8_decode('------------------------------------------------------'),0,'L',true);
        $this->Ln($salto);
        $this->SetX(6);
        $this->MultiCell(70,4,utf8_decode('TÉCNICO: '.$tecnico['user_name']),0,'L',true);
        $this->Ln($salto);
        $this->SetX(6);
        $this->MultiCell(70,4,utf8_decode('ESTATUS: '.$fila['estatus']),0,'L',true);
        $this->Ln($salto);
        $this->SetX(6);
        $this->MultiCell(70,4,utf8_decode('OBSERVACIONES: '.$fila['observaciones']),0,'L',true);
         $this->Ln($salto);
        $this->SetX(6);
        $this->MultiCell(70,4,utf8_decode('------------------------------------------------------'),0,'L',true);
        
        $SqlRefacciones = mysqli_query($conn, "SELECT * FROM refacciones WHERE id_dispositivo = '$id_dispositivo'");
        $filas = mysqli_num_rows($SqlRefacciones);
        $sub = 0;
        if ($filas > 0) {
            $this->Ln(5);
            $this->SetFont('Arial','B',13);
            $this->Cell(20,4,'Refacciones: ',0,0,'C',true);
            $this->Ln(5);
            $this->SetFont('Arial','',10);
            $this->Ln(5);
            
            while($refaccion = mysqli_fetch_array($SqlRefacciones)){
                $this->SetX(6);
                $this->MultiCell(70,4,utf8_decode(" - ". $refaccion['descripcion']),0,'L',true);
                $this->MultiCell(70,4,utf8_decode("$ ". $refaccion['cantidad'].'.00'),0,'R',true);
                $sub=$sub+$refaccion['cantidad'];
            }
        }
            $sql = mysqli_query($conn, "SELECT * FROM pagos WHERE id_cliente = '$id_dispositivo' AND descripcion = 'Anticipo' AND tipo = 'Dispositivo'");
            $Total_anti = 0;
            if (mysqli_num_rows($sql)>0) {
                            
                while ($anticipo = mysqli_fetch_array($sql)) {
                  $Total_anti += $anticipo['cantidad'];
                }
            }
            if ($fila['precio'] == 0) {
                $this->SetFont('Arial','B',10);
                $this->MultiCell(70,4,utf8_decode("SUBTOTAL"),0,'L',true);
                $this->MultiCell(70,4,utf8_decode("$ ".$fila['t_refacciones'].".00"),0,'R',true);
                $Total=$fila['mano_obra']+$fila['t_refacciones']-$Total_anti;
                $this->MultiCell(70,4,utf8_decode("Mano de Obra"),0,'L',true);
                $this->MultiCell(70,4,utf8_decode("$ ".$fila['mano_obra'].".00"),0,'R',true);
                $this->MultiCell(70,4,utf8_decode("Anticipó"),0,'L',true);
                $this->MultiCell(70,4,utf8_decode("- $ ".$Total_anti.".00"),0,'R',true);
                $this->SetFont('Arial','B',12);
                $this->Ln(5);
                $this->MultiCell(70,4,utf8_decode('TOTAL: $'.$Total.'.00'),0,'R',true);
                $this->Ln(10);
            }else{
                $this->SetFont('Arial','B',10);
                $this->MultiCell(70,4,utf8_decode("SUBTOTAL"),0,'L',true);
                $this->MultiCell(70,4,utf8_decode("$ ".$sub.".00"),0,'R',true);
                $mano=$fila['precio']-$sub;
                $this->MultiCell(70,4,utf8_decode("Mano de Obra"),0,'L',true);
                $this->MultiCell(70,4,utf8_decode("$ ".$mano.".00"),0,'R',true);
                $this->MultiCell(70,4,utf8_decode("Anticipó"),0,'L',true);
                $this->MultiCell(70,4,utf8_decode("- $ ".$Total_anti.".00"),0,'R',true);
                $this->SetFont('Arial','B',12);
                $this->Ln(5);
                $Total=$fila['precio']-$Total_anti;
                $this->MultiCell(70,4,utf8_decode('TOTAL: $'.$Total.'.00'),0,'R',true);
                $this->Ln(10);
            }
        $this->SetX(6);
        $this->SetFont('Arial','',10);
        $this->MultiCell(70,4,utf8_decode('_________________________________'),0,'L',false);
        $this->SetX(6);
        $this->MultiCell(70,7,utf8_decode('Firma de Conformidad'),0,'C',false);

        $this->SetFont('Arial','B',7); 
        $this->SetX(5);
        $this->MultiCell(69,4,utf8_decode('ADVERTENCIA:
            1.- PASADOS 30 DÍAS NO SOMOS RESPONSABLES DE LOS EQUIPOS. 
            2.- EN SOFTWARE (PROGRAMAS) NO HAY GARANTÍA.
            3.- SIN ESTE TICKET NO SE ACEPTAN RECLAMACIONES.'),1,'L',true);

    }
}

global $id_dispositivo;
global $conn;
$listado = mysqli_query($conn, "SELECT * FROM dispositivos WHERE id_dispositivo=$id_dispositivo");
$fila = mysqli_fetch_array($listado);
$pdf = new PDF('P', 'mm', array(80,297));
$pdf->SetTitle('Folio_'.$id_dispositivo.'_'.$fila['nombre'].'_'.'_'.$fila['marca'].'_'.$fila['modelo'].'_color_'.$fila['color']);
$pdf->folioCliente();
$pdf->folioCliente2();
$pdf->Output('Folio_'.$id_dispositivo.'_'.$fila['nombre'].'_'.'_'.$fila['marca'].'_'.$fila['modelo'].'_color_'.$fila['color'],'I');
$listado = mysqli_query($conn, "SELECT * FROM dispositivos WHERE id_dispositivo=$id_dispositivo");
$num_filas = mysqli_num_rows($listado);
if ($num_filas > 0) {
    date_default_timezone_set('America/Mexico_City');
    $FechaSalida = date('Y-m-d');
    mysqli_query ($conn, "UPDATE dispositivos SET  estatus='Entregado', fecha_salida='$FechaSalida' WHERE id_dispositivo='$id_dispositivo'");
}
?>