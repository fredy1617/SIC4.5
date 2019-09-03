<?php
date_default_timezone_set('America/Mexico_City');
include('../php/conexion.php');
include('is_logged.php');
include('../escpos/autoload.php'); //Nota: si renombraste la carpeta a algo diferente de "ticket" cambia el nombre en esta línea
use Mike42\Escpos\Printer;
use Mike42\Escpos\EscposImage;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
$filtrarNombres = $_POST['valorNombres'];
$filtrarTelefono = $_POST['valorTelefono'];
$filtrarMarca = $_POST['valorMarca'];
$filtrarModelo = $_POST['valorModelo'];
$filtrarColor = $_POST['valorTipo'];
$filtrarContra = $_POST['valorContra'];
$filtrarFalla = $_POST['valorFalla'];
$filtrarExtra = $_POST['valorExtras'];


//Filtro anti-XSS
$caracteres_malos = array("<", ">", "\"", "'", "/", "<", ">", "'", "/");
$caracteres_buenos = array("& lt;", "& gt;", "& quot;", "& #x27;", "& #x2F;", "& #060;", "& #062;", "& #039;", "& #047;");

$Nombres = str_replace($caracteres_malos, $caracteres_buenos, $filtrarNombres);
$Telefono = str_replace($caracteres_malos, $caracteres_buenos, $filtrarTelefono);
$Marca = str_replace($caracteres_malos, $caracteres_buenos, $filtrarMarca);
$Modelo = str_replace($caracteres_malos, $caracteres_buenos, $filtrarModelo);
$Tipo = str_replace($caracteres_malos, $caracteres_buenos, $filtrarColor);
$Contra = str_replace($caracteres_malos, $caracteres_buenos, $filtrarContra);
$Falla = str_replace($caracteres_malos, $caracteres_buenos, $filtrarFalla);
$Extra = str_replace($caracteres_malos, $caracteres_buenos, $filtrarExtra);
$id_user = $_SESSION['user_id'];
$Fecha = date('Y-m-d');

$mensaje = '';

if(mysqli_num_rows(mysqli_query($conn, "SELECT * FROM dispositivos WHERE nombre='$Nombres' AND telefono='$Telefono' AND marca='$Marca' AND modelo='$Modelo' AND contra='$Contra' AND falla='$Falla'  AND fecha='$Fecha'"))>0){
	$mensaje = "<script>M.toast({html: 'No puedes registrar los mismos datos 2 veces hoy.', classes: 'rounded'})</script>";
}else{
	if(mysqli_query($conn, "INSERT INTO dispositivos (tecnico, nombre, telefono, marca, modelo, contra, falla, fecha, estatus, recibe, tipo, extras) VALUES (0, '$Nombres', '$Telefono', '$Marca', '$Modelo', '$Contra', '$Falla', '$Fecha', 'Pendiente', '$id_user', '$Tipo', '$Extra')")){
		$mensaje = "<script>M.toast({html: 'Registro exitoso.', classes: 'rounded'})</script>";
		echo "<script>window.open('../php/folioEntrada.php', '_blank')</script>";
	}else{
		$mensaje = "<script>M.toast({html: 'Ha ocurrido un error.', classes: 'rounded'})</script>";
	}
}
echo $mensaje;
?>
