<?php
include ('conexion.php');
include('is_logged.php');

$Id = $conn->real_escape_string($_POST['valorIdDispositivo']);
$Tipo_Cambio = $conn->real_escape_string($_POST['valorTipo_Cambio']);
$ReferenciaB = $conn->real_escape_string($_POST['valorRef']);

$dispositivo = mysqli_query($conn, "SELECT * FROM dispositivos WHERE id_dispositivo='$Id'");
$num_filas = mysqli_num_rows($dispositivo);
if ($num_filas > 0) {
    $disp = mysqli_fetch_array($dispositivo);

    date_default_timezone_set('America/Mexico_City');
    $FechaHoy = date('Y-m-d');
    $Hora = date('H:i:s');

    //DAR DE ALTA LOS PAGOS
    $id_User =  $_SESSION['user_id'];

    if ($disp['precio']==0) {
        $Tot = $disp['mano_obra']+$disp['t_refacciones'];
    }else{
        $Tot = $disp['precio'];
    }
    $sql = mysqli_query($conn, "SELECT * FROM pagos WHERE id_cliente = '$Id' AND descripcion = 'Anticipo' AND tipo = 'Dispositivo'");
    $Total_anti = 0;
      if (mysqli_num_rows($sql)>0) {
                    
        while ($anticipo = mysqli_fetch_array($sql)) {

          $Total_anti += $anticipo['cantidad'];
        }
      }
    $resto = $Tot-$Total_anti;
    //MANO DE ORBRA-----}
    if (mysqli_num_rows(mysqli_query($conn, "SELECT * FROM pagos WHERE id_cliente = $Id AND descripcion = 'Liquidacion' AND tipo = 'Dispositivo' AND tipo_cambio = '$Tipo_Cambio'")) == 0){
        $sql = "INSERT INTO pagos(id_cliente, descripcion, cantidad, fecha, hora, tipo, id_user, corte, corteP, tipo_cambio, Cotejado) VALUES ($Id, 'Liquidacion', '$resto', '$FechaHoy', '$Hora', 'Dispositivo', $id_User, 0, 0, '$Tipo_Cambio', 0)";
        if (mysqli_query($conn, $sql)){
          echo '<script> M.toast({html :"El pago se dio de alta.", classes: "rounded"});</script>';
          $ultimo =  mysqli_fetch_array(mysqli_query($conn, "SELECT MAX(id_pago) AS id FROM pagos WHERE id_cliente = $Id"));            
          $id_pago = $ultimo['id'];
          // Si el pago es de banco guardar la referencia....
          if (($Tipo_Campio == 'Banco' OR $Tipo_Campio == 'SAN') AND $ReferenciaB != '') {
            mysqli_query($conn,  "INSERT INTO referencias (id_pago, descripcion) VALUES ('$id_pago', '$ReferenciaB')");
          }
        }
    }
}

?>
  <script>    
    function imprimir() {
      M.toast({html: "Salida de dipositivo...", classes: "rounded"});
      var a = document.createElement("a");
      a.target = "_blank";
      a.href = "../php/Salida_SerTec.php?id=<?php echo $Id; ?>";
      a.click();
    };
  
    function ir() {
	  var b = document.createElement("b");
	  b.href = "../views/dispositivos.php";
	  b.click();
	};
  imprimir();
	ir();
  </script>
