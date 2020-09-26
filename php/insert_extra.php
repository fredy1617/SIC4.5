<?php
session_start();
#DEFINIMOS UNA ZONA HORARIA
date_default_timezone_set('America/Mexico_City');
#INCLUIMOS LA CONEXION A LA BASE DE DATOS PARA PODER HACER CUALQUIER MODIFICACION, INSERCION O SELECCION
include('../php/conexion.php');

$Cantidad = $conn->real_escape_string($_POST['valorCantidad']);
$IdOrden = $conn->real_escape_string($_POST['valorIdOrden']);
$Descripcion = $conn->real_escape_string($_POST['valorDescripcion']);
$Fecha_Hoy = date('Y-m-d');

if (mysqli_num_rows(mysqli_query($conn, "SELECT * FROM orden_extras WHERE id_orden = $IdOrden AND descripcion = '$Descripcion' AND cantidad = '$Cantidad' AND fecha='$Fecha_Hoy'"))>0) {
	echo "<script>M.toast({html: 'Ya se encuentra un extra con la misma informacion registrado.', classes: 'rounded'})</script>";
}else{
	$sql = "INSERT INTO orden_extras (id_orden, descripcion, cantidad, fecha) VALUES ($IdOrden, '$Descripcion', '$Cantidad', '$Fecha_Hoy')";
	if (mysqli_query($conn, $sql)) {
		echo '<script>M.toast({html:"El extra se dió de alta satisfcatoriamente.", classes: "rounded"})</script>';
		
	}else{
		echo '<script>M.toast({html:"Ocurrio un error...", classes: "rounded"})</script>';
	}
}
$Extras = mysqli_query($conn, "SELECT * FROM orden_extras WHERE id_orden = $IdOrden");
echo '<b class = "col s2">Extra(s): </b>';
if (mysqli_num_rows($Extras) > 0) {
	echo '<table class = "col s6">
			<thead>
			  <tr>
				<th>Descripcion</th>
				<th>Cantida</th>
			  </tr>
			</thead>
			<tbody>';
	while ($extra = mysqli_fetch_array($Extras)) {
		echo '<tr>
				<td>'.$extra['descripcion'].'</td>
				<td> $'.$extra['cantidad'].'</td>
			  </tr>';
	}
	echo '  </tbody>
		  </table>';
}
mysqli_close($conn);
?>