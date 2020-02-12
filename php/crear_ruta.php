<?php
include('../php/conexion.php');
date_default_timezone_set('America/Mexico_City');

$Tecnicos = $conn->real_escape_string($_POST['valorTecnicos']);
$Vehiculo = $conn->real_escape_string($_POST['valorVehiculo']);
$Bobina = $conn->real_escape_string($_POST['valorBobina']);
$Vale = $conn->real_escape_string($_POST['valorVale']);
$Fecha = date('Y-m-d'); 
$aux= mysqli_num_rows(mysqli_query($conn, "SELECT * FROM rutas WHERE fecha = '$Fecha' AND estatus = 0 AND tecnicos='$Tecnicos' "));
if($aux<=0 or $aux==null){
if (mysqli_query($conn, "INSERT INTO rutas(fecha, tecnicos) VALUES ('$Fecha', '$Tecnicos')")) {
	echo '<script>M.toast({html : "Se creo la ruta correctamente.", classes: "rounded"})</script>';
	$ultimo =  mysqli_fetch_array(mysqli_query($conn, "SELECT MAX(id_ruta) AS id FROM rutas WHERE estatus=0"));            

    $ultima_ruta = $ultimo['id'];
	//GUARDAR REPORTE DE RUTA
	mysqli_query($conn, "INSERT INTO reporte_rutas(id_ruta, vehiculo, bobina, vale) VALUES ('$ultima_ruta', '$Vehiculo', '$Bobina', '$Vale')");

	//modificar pendientes y reportes agregar id_ruta
    mysqli_query($conn, "UPDATE tmp_pendientes SET ruta_inst = $ultima_ruta WHERE ruta_inst=0");
    mysqli_query($conn, "UPDATE tmp_reportes SET ruta = $ultima_ruta WHERE ruta=0");
	?>
	<script>    
	    var a = document.createElement("a");
	      a.target="_blank"
	      a.href = "../php/ruta.php";
	      a.click();
	</script>
	<?php
}else{
	echo '<script>M.toast({html : "Ocurrio un error en la creación.", classes: "rounded"})</script>';
}}else{
	echo '<script>M.toast({html : "Ya se encuentra una ruta registrada con los mismos valores el día de hoy.", classes: "rounded"})</script>';
}

?>