<?php
include('../php/conexion.php');
$id_pago = $_GET['IdPago'];

//Incluimos la libreria fpdf
include("../fpdf/fpdf.php");
include("is_logged.php");

class PDF extends FPDF{
	function folioCliente(){
		global $id_pago;
		global $conn;

		$fila = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM pagos WHERE id_pago = $id_pago"));
		$id_user = $fila['id_user'];
		$user = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM users WHERE user_id=$id_user"));
		$id_cliente = $fila['id_cliente'];
		$tipo_pago = $fila['tipo'];
        if ((mysqli_num_rows(mysqli_query($conn, "SELECT * FROM clientes WHERE id_cliente = $id_cliente"))) == 0) {
            $cliente = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM especiales WHERE id_cliente = $id_cliente"));
        }else{
            $cliente = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM clientes WHERE id_cliente = $id_cliente"));
        }
		 // Colores de los bordes, fondo y texto
            $this->SetFillColor(255,255,255);
            $this->SetTextColor(0,0,0);
            $this->AddPage();
            global $title;
            global $pass;
            $this->Image('../img/logo_ticket.jpg',28,4,20);
            $this->SetFont('Arial','B',13);
            $this->SetY(30);
            $this->Cell(90,4,'Fecha: '.$fila['fecha'],0,0,'C',true);
            $this->SetFont('Arial','B',10);
            $this->Ln(5);
            $this->Cell(20,4,utf8_decode('Folio: '.$id_pago),0,0,'L',true);$this->SetFont('Arial','',10);
            $this->Ln(5);
            $this->Cell(20,4,utf8_decode('No. Cliente: '.$fila['id_cliente']),0,0,'L',true);
            $this->Ln(5);
            $this->MultiCell(60,4,utf8_decode('Nombre:'.$cliente['nombre']),0,'L',true);
            $this->Ln(1);
            $this->MultiCell(60,4,utf8_decode('Descripción: '.$fila['descripcion']),0,'L',true);
            $this->Ln(1);
            $this->Cell(20,4,utf8_decode('Cantidad: $'.$fila['cantidad'].'.00'),0,0,'L',true);
            if (($id_user == 47 OR $id_user == 42 OR $id_user == 31 OR $id_user == 52) AND $tipo_pago != 'Otros Pagos') {
                $this->Ln(5);
                $this->Cell(20,4,utf8_decode('Comisión: + $10.00'),0,0,'L',true);   
            }
            $this->Ln(5);
            $this->Cell(20,4,utf8_decode('Tipo: '.$fila['tipo']),0,0,'L',true);
            $this->Ln(5);
            if ($tipo_pago == 'Abono' AND $fila['descripcion'] != 'Abono de instalacion') {
                // SACAMOS LA SUMA DE TODAS LAS DEUDAS Y ABONOS ....
                $deuda = mysqli_fetch_array(mysqli_query($conn, "SELECT SUM(cantidad) AS suma FROM deudas WHERE id_cliente = $id_cliente"));
                $abono = mysqli_fetch_array(mysqli_query($conn, "SELECT SUM(cantidad) AS suma FROM pagos WHERE id_cliente = $id_cliente AND tipo = 'Abono'"));
                  //COMPARAMOS PARA VER SI LOS VALORES ESTAN VACIOS::
                if ($deuda['suma'] == "") {
                    $deuda['suma'] = 0;
                }elseif ($abono['suma'] == "") {
                    $abono['suma'] = 0;
                }
                //SE RESTAN DEUDAS DE ABONOS 
                $Saldo = $abono['suma']-$deuda['suma'];
                $msj = 'Saldo a Favor: $';
                if ($Saldo < 0) {
                    $Saldo = $Saldo*(-1);
                    $msj = 'Saldo a Pagar: $';
                }

                $this->Cell(20,4,utf8_decode($msj.$Saldo.'.00'),0,0,'L',true);
                $this->Ln(5);
            }
            $this->MultiCell(60,4,utf8_decode('Atendió: '.$user['firstname'].' '.$user['lastname']),0,'L',true);
            $this->Ln(1);
            $this->SetFont('Arial','B',9);
            if ($tipo_pago == 'Mensualidad' ){
                $this->MultiCell(60,7,utf8_decode('GRACIAS. RECORDARLE QUE SU PRÓXIMO PAGO DEBERÁ SER ANTES DEL: '.$cliente['fecha_corte'].
                    '
RECOMENDACIONES:
1.- Contar con línea regulada "regulador de corriente".
2.- No modificar orden del cableado.
3.- No presionar botón de reset de los equipos.
4.- En caso de falla comunicarse al 433 935 62 86.'),1,'C',true);
            }else if ($tipo_pago == 'Reporte' OR $tipo_pago == 'Liquidacion') {
                $this->MultiCell(60,7,utf8_decode('GRACIAS POR SU PAGO.
RECOMENDACIONES:
1.- Contar con línea regulada "regulador de corriente".
2.- No modificar orden del cableado.
3.- No presionar botón de reset de los equipos.
4.- En caso de falla comunicarse al 433 935 62 86.'),1,'C',true);
            }else{
                $this->MultiCell(60,7,utf8_decode('GRACIAS POR SU PAGO; MÁS QUE TECNOLOGÍA SOMOS COMUNICACIÓN.'),1,'C',true);
            }
            mysqli_close($conn);
        }
    }

    $pdf = new PDF('P', 'mm', array(80,297));
    $pdf->SetTitle('Pago');
    $pdf->folioCliente();
    $pdf->Output('Pago','I');
?>