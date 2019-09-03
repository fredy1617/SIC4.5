<?php
    //Incluimos la libreria fpdf
    include("../fpdf/fpdf.php");
    include('is_logged.php');
    $pass="root";
    $id_user = $_SESSION['user_id'];
    //Incluimos el archivo de conexion a la base de datos
    class PDF extends FPDF
    {
        function folioCliente()
        {
            global $id_user;
            global $pass;
            $enlace = mysqli_connect("localhost", "root", $pass, "servintcomp");
            $listado = mysqli_query($enlace, "SELECT * FROM pagos WHERE id_user=$id_user AND corte = 0 AND tipo_cambio='Efectivo'");
            $cobrador = mysqli_query($enlace, "SELECT * FROM users WHERE user_id = $id_user");

            $sql_total = mysqli_query($enlace, "SELECT SUM(cantidad) AS precio FROM pagos WHERE id_user=$id_user AND corte = 0 AND tipo_cambio='Efectivo'");
            $total = mysqli_fetch_array($sql_total);
            $sql_banco = mysqli_query($enlace, "SELECT * FROM pagos WHERE id_user=$id_user AND corte = 0 AND tipo_cambio='Banco'");
            $totalbanco = mysqli_fetch_array(mysqli_query($enlace, "SELECT SUM(cantidad) AS precio FROM pagos WHERE id_user=$id_user AND corte = 0 AND tipo_cambio='Banco'"));
            date_default_timezone_set('America/Mexico_City');
            $Fecha_hoy = date('Y-m-d');
            $cantidad=$total['precio'];
            $banco = $totalbanco['precio'];

            //Insertar corte.....
            if ($cantidad != "" OR $banco != "") {
                mysqli_query($enlace,"INSERT INTO cortes(usuario, fecha, cantidad, banco) VALUES ($id_user, '$Fecha_hoy', '$cantidad', '$banco')");
            }
            if ($cobrador->num_rows > 0){
              while ($row = $cobrador->fetch_assoc()){
                $nombre_cobrador = $row['firstname'].' '.$row['lastname'];
              }
            } else{
                $nombre_cobrador = "";
            }   
            $ultimo =  mysqli_fetch_array(mysqli_query($enlace, "SELECT MAX(id_corte) AS id FROM cortes WHERE usuario=$id_user"));           
            $corte = $ultimo['id'];
            // Colores de los bordes, fondo y texto
            $this->SetFillColor(255,255,255);
            $this->SetTextColor(0,0,0);
            $this->AddPage();
            global $title;
            global $pass;
                       $this->Image('../img/logo.jpg',28,4,20);
            $this->SetFont('Arial','B',13);
            $this->SetY(30);
            $this->Cell(20,4,'Corte De: ',0,0,'C',true);
            $this->Ln(5);
            $this->Cell(50,4,utf8_decode($nombre_cobrador),0,0,'C',true);
            $this->Ln(10);
            $this->Cell(90,4,'Fecha: '.Date('d-m-Y'),0,0,'C',true);            
            $this->Ln(10);
            $filas = mysqli_num_rows($listado);
            if ($filas > 0) {
             
            $this->Cell(20,4,'Efectivo: ',0,0,'C',true);
            $this->Ln(5);
            $this->SetFont('Arial','',11);
            
            while($fila = mysqli_fetch_array($listado)){
                //insertar pagos de corte...
                $id_pago = $fila['id_pago'];
                mysqli_query($enlace,"INSERT INTO detalles(id_corte, id_pago) VALUES ($corte, $id_pago )");
                $this->SetX(6);
                $this->MultiCell(70,4,utf8_decode("Cliente: # " .$fila['id_cliente'].'; '.$fila['descripcion']),0,'L',true);
                $this->MultiCell(70,4,utf8_decode("$ ". $fila['cantidad'].'.00'),0,'R',true);
                $this->Ln(5);
            }
            $this->SetFont('Arial','B',13);
            $this->Ln(10);
            $this->MultiCell(65,4,utf8_decode('TOTAL: $'.$total['precio'].'.00'),0,'R',true);
            $this->Ln(10);
        }
            //---------------BANCO-------------------------------------------
            
            $filas = mysqli_num_rows($sql_banco);
            if ($filas > 0) {
             
            $this->Cell(20,4,'Banco: ',0,0,'C',true);
            $this->Ln(5);
            $this->SetFont('Arial','',11);

            while($fila = mysqli_fetch_array($sql_banco)){
                //insertar pagos de corte...
                $id_pago = $fila['id_pago'];
                mysqli_query($enlace,"INSERT INTO detalles(id_corte, id_pago) VALUES ($corte, $id_pago )");
                $this->SetX(6);
                $this->MultiCell(70,4,utf8_decode("Cliente: # ".$fila['id_cliente'].'; '.$fila['descripcion']),0,'L',true);
                $this->MultiCell(70,4,utf8_decode("$ ". $fila['cantidad'].'.00'),0,'R',true);
                $this->Ln(5);
            }
            $this->SetFont('Arial','B',13);
            $this->Ln(10);
            $this->MultiCell(65,4,utf8_decode('TOTAL: $'.$totalbanco['precio'].'.00'),0,'R',true);
            $this->Ln(10);
            }

            $this->SetFont('Arial','',11);
            $this->Cell(60,4,'Servicios Integrales de Computacion ',0,0,'C',true);

            mysqli_query($enlace,"UPDATE pagos SET corte=1 WHERE id_user=$id_user");
            mysqli_close($enlace);
        }
    }
    $pdf = new PDF('P', 'mm', array(80,297));
    $pdf->SetTitle('CORTE');
    $pdf->folioCliente();
    $pdf->Output('CORTE','I');
?>