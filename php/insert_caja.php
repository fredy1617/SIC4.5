<?php
session_start();
date_default_timezone_set('America/Mexico_City');
include('conexion.php');

$Cantidad = $conn->real_escape_string($_POST['valorCantidad']);
$Descripcion = $conn->real_escape_string($_POST['valorDescripcion']);
$Tipo = $conn->real_escape_string($_POST['valorTipo']);
$Fecha_Hoy = date('Y-m-d');
$Hora = date('H:i:s');
$id_user = $_SESSION['user_id'];

if (mysqli_num_rows(mysqli_query($conn, "SELECT * FROM historila_caja_ch WHERE cantidad = '$Cantidad' AND descripcion = '$Descripcion' AND tipo = '$Tipo' AND fecha='$Fecha_Hoy'"))>0) {
	echo "<script>M.toast({html: 'Ya se encuentra unn registro con los mismos valores el dia de hoy.', classes: 'rounded'})</script>";
}else{
	// SACAMOS LA SUMA DE TODOS LOS EGRESOSO E INGRESOSO DE LA CAJA CHICA
    $Suma_Ingresos = mysqli_fetch_array(mysqli_query($conn, "SELECT SUM(cantidad) AS suma FROM historila_caja_ch WHERE tipo = 'Ingreso'"));
    $Suma_Egresoso = mysqli_fetch_array(mysqli_query($conn, "SELECT SUM(cantidad) AS suma FROM historila_caja_ch WHERE tipo = 'Egreso'"));
    //SE HACE EL CALCULO DEL TOTAL DE LA CAJA CHICA
    $Total = $Suma_Ingresos['suma']-$Suma_Egresoso['suma'];
    if (($Total-$Cantidad)<0 AND $Tipo == 'Egreso') {
		echo '<script>M.toast({html:"No puede tener un egreso mayor a la cantidad que existe en caja!!!.", classes: "rounded"})</script>';
    }else{
		$sql = "INSERT INTO historila_caja_ch(cantidad, descripcion, fecha, hora, tipo, usuario) VALUES ('$Cantidad', '$Descripcion', '$Fecha_Hoy', '$Hora', '$Tipo', $id_user)";
		if (mysqli_query($conn, $sql)) {
			echo '<script>M.toast({html:"El registro se dió de alta satisfcatoriamente.", classes: "rounded"})</script>';
		}else{
			echo '<script>M.toast({html:"Ocurrio un error...", classes: "rounded"})</script>';
		}
	}
}
mysqli_close($conn);
?>
<script>
	setTimeout("location.href='../views/caja_chica.php'", 1000);
</script>