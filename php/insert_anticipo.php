<?php
session_start();
date_default_timezone_set('America/Mexico_City');
include('conexion.php');

$Monto = $conn->real_escape_string($_POST['valorMonto']);
$IdDispositivo = $conn->real_escape_string($_POST['valorIdDispositivo']);
$Tipo_Cambio = $conn->real_escape_string($_POST['valorTipo_Cambio']);
$Fecha_Hoy = date('Y-m-d');
$Hora = date('H:i:s');
$id_user = $_SESSION['user_id'];

if (mysqli_num_rows(mysqli_query($conn, "SELECT * FROM pagos WHERE id_cliente = $IdDispositivo AND descripcion = 'Anticipo' AND tipo = 'Dispositivo' AND fecha='$Fecha_Hoy' AND cantidad='$Monto'"))>0) {
	echo "<script>M.toast({html: 'Ya se encuentra un abono registrado con los mismos valores el dia de hoy.', classes: 'rounded'})</script>";
}else{
	$sql = "INSERT INTO pagos(id_cliente, descripcion, cantidad, fecha, hora, tipo, id_user, corte, corteP, tipo_cambio, Cotejado) VALUES ($IdDispositivo, 'Anticipo', '$Monto', '$Fecha_Hoy', '$Hora', 'Dispositivo', $id_user, 0, 0, '$Tipo_Cambio', 0)";
	if (mysqli_query($conn, $sql)) {
		echo '<script>M.toast({html:"El abono se dió de alta satisfcatoriamente.", classes: "rounded"})</script>';
		$rs = mysqli_fetch_row(mysqli_query($conn, "SELECT MAX(id_pago) AS id FROM pagos"));
        $id = $rs[0];
	?>
	  <script>
	    var a = document.createElement("a");
	      a.target = "_blank";
	      a.href = "../php/anticipo_dispositivo.php?IdPago="+<?php echo $id; ?>;
	      a.click();
	  </script>
	  <?php
	}else{
		echo '<script>M.toast({html:"Ocurrio un error...", classes: "rounded"})</script>';
	}
}
mysqli_close($conn);
?>