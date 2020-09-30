<?php
#INCLUIMOS EL ARCHIVO CON LA CONEXION A LA BASE DE DATPS
include('../php/conexion.php');
$id = $_GET['id'];//TOMAMOS EL ID DEL CORTE PREVIAMENTE CREADO PARA PODERLE VER SU INFORMACION

#INCLUIMOS EL ARCHIVO CON LAS LIBRERIAS DE FPDF PARA PODER CREAR ARCHIVOS CON FORMATO PDF
include("../fpdf/fpdf.php");
#INCLUIMOS EL PHP DONDE VIENE LA INFORMACION DEL INICIO DE SESSION
include('is_logged.php');

#CREAMOS LA CLASE DEL CONTENIDO DE NUESTRO PDF
class PDF extends FPDF{
    function folioCliente(){
        #METEMOS LAS BARIABLES CREADAS FUERA DE LA CLASE PDF DENTRO DE LA MISMA
        global $id;
		global $ver;
		global $conn;
        #TOMAMOS LA INFORMACION DEL CORTE CON EL ID GUARDADO EN LA VARIABLE $id QUE RECIBIMOS CON EL GET
        $corte = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM cortes WHERE id_corte = $id"));
		$id_user = $corte['usuario'];
        #TOMAMOS LA INFORMACION DEL COBRADOR
		$user = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM users WHERE user_id=$id_user"));
		#------ INICIAMOS LA CREACION Y FORMTO DEL PDF -------- 
            // Colores de los bordes, fondo y texto
            $this->SetFillColor(255,255,255);
            $this->SetTextColor(0,0,0);
            $this->AddPage();
            global $title;
            global $pass;
            $this->Image('../img/logo_ticket.jpg',28,4,20);
            $this->SetFont('Arial','B',13);
            $this->SetY(30);
            $this->Cell(85,4,'Fecha: '.$corte['fecha'],0,0,'C',true);
            $this->Ln(10);
            $this->MultiCell(60,4,utf8_decode('Corte: '.$id),0,'L',true);
            $this->SetFont('Arial','',11);
            $this->Ln(4);
            $this->MultiCell(60,4,utf8_decode('Cobrador: '.$user['firstname'].' '.$user['lastname']),0,'L',true);
            $entrega = $corte['cantidad'];
            $this->Ln(4);
            $this->MultiCell(60,4,utf8_decode('Corte: '.$corte['cantidad']),0,'L',true);
            $sql_deducible = mysqli_query($conn, "SELECT * FROM deducibles WHERE id_corte = $id");
            if (mysqli_num_rows($sql_deducible) > 0) {
                $deducible = mysqli_fetch_array($sql_deducible);
                $entrega = $entrega - $deducible['cantidad'];
                $this->Ln(3);
                $this->MultiCell(60,4,utf8_decode('Deducible(s): $'.$deducible['cantidad'].'.00'),0,'L',true);
                $this->Ln(1);
                $this->MultiCell(60,4,utf8_decode('('.$deducible['descripcion'].').'),0,'L',true);
            }
            $sql_deuda =mysqli_query($conn, "SELECT * FROM deudas_cortes WHERE id_corte = $id AND cobrador = $id_user");
            if (mysqli_num_rows($sql_deuda) > 0) {
                $deuda = mysqli_fetch_array($sql_deuda);
                $entrega = $entrega-$deuda['cantidad'];
                $this->Ln(3);
                $this->MultiCell(60,4,utf8_decode('Adeudo: $'.$deuda['cantidad'].'.00'),0,'L',true);
            }
            $this->Ln(4);
            $this->MultiCell(60,4,utf8_decode('Corte Realizado Por: '.$corte['realizo']),0,'L',true);
            $this->SetFont('Arial','B',12);
            $this->Ln(4);
            $this->MultiCell(65,4,utf8_decode('ENTREGO: $ '.$entrega.'.00'),0,'R',true);
            $this->Ln(6);
            $this->SetFont('Arial','',10);
            $this->MultiCell(70,4,utf8_decode('_________________________________'),0,'L',false);
            $this->SetX(6);
            $this->MultiCell(70,7,utf8_decode('Firma de conformidad'),0,'C',false);
            $this->SetFont('Arial','B',9);
            $this->MultiCell(60,7,utf8_decode('MÁS QUE TECNOLOGÍA SOMOS COMUNICACIÓN.

CONFIRMACION DE CORTE!.'),1,'C',true);
            mysqli_close($conn);
        }
    }

    $pdf = new PDF('P', 'mm', array(80,297));
    $pdf->SetTitle('Corte');
    $pdf->folioCliente();
    $pdf->Output('Corte','I');
?>