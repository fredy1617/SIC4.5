<?php 
include('../php/conexion.php');
date_default_timezone_set('America/Mexico_City');
$IdReporte = $conn->real_escape_string($_POST['valorIdReporte']);
$Falla = $conn->real_escape_string($_POST['valorFalla']);
$Solucion = $conn->real_escape_string($_POST['valorSolucion']);
$Tecnico = $conn->real_escape_string($_POST['valorTecnico']);
$Atendido = $conn->real_escape_string($_POST['valorAtendido']);
$Fecha_visita = $conn->real_escape_string($_POST['valorFecha']);
$Apoyo = $conn->real_escape_string($_POST['valorApoyo']);
$Campo = $conn->real_escape_string($_POST['valorCampo']);
$FechaAtendido = date('Y-m-d');
$Hora = date('H:i:s');

$IdCliente = $conn->real_escape_string($_POST['valorIdCliente']);
$Nombre = $conn->real_escape_string($_POST ['valorNombre']);
$Telefono = $conn->real_escape_string($_POST['valorTelefono']);
$Direccion = $conn->real_escape_string($_POST['valorDierccion']);
$Referencia = $conn->real_escape_string($_POST['valorReferencia']);

#Tecnicos
$T1 = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM users WHERE user_id = $Tecnico"));
if ($Apoyo == 0) {
	$Tecnicos = $T1['user_name'];
}else{
	$A2 = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM users WHERE user_id = $Apoyo"));
	$Tecnicos = $T1['user_name'].', '.$A2['user_name'];
}


#--- MATERIALES ---
$Antena = $conn->real_escape_string($_POST['valorAntena']);
$Router = $conn->real_escape_string($_POST['valorRouter']);
$Cable = $conn->real_escape_string($_POST['valorCable']);
$Tubos = $conn->real_escape_string($_POST['valorTubos']);
$Bobina = $conn->real_escape_string($_POST['valorBobina']);
$Extras = $conn->real_escape_string($_POST['valorExtras']);
$Tipo = $conn->real_escape_string($_POST['valorTipo']);
  
$sql2= "UPDATE clientes SET nombre = '$Nombre', telefono = '$Telefono', direccion = '$Direccion', referencia='$Referencia' WHERE id_cliente=$IdCliente ";
if (mysqli_query($conn, $sql2)) {
  echo  '<script>M.toast({html:"Información actualizada.", classes: "rounded"})</script>';
}

if($Atendido == 'Sí'){
	$Atendido = '1';	
	$Atender_Visita = '1';
}else{
	$Atendido = '2';
	$Atender_Visita ='2';
} 	
$mas = "";
if ($Fecha_visita != 0) {
	$Atendido = '1';
	$reporte   = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM reportes WHERE id_reporte =$IdReporte"));
	$nombreTecnico  = mysqli_fetch_array(mysqli_query($conn,"SELECT * FROM users WHERE user_id = '$Tecnico'"));
	$Array = explode("*", $reporte['descripcion']);
    $descripcion = $Array[0];

    if (count($Array)>1) {
        $descripcion = $Array[1];
    }
	$Nombre = $nombreTecnico['firstname'];
	$mas= ", visita = 1, fecha_visita = '$Fecha_visita', descripcion = '".$Nombre." Tienes una visita el día de HOY. Problema : *".$descripcion." ' , atender_visita = '$Atender_Visita'";
}
$resultado = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM reportes WHERE id_reporte = $IdReporte"));
//Variable vacía (para evitar los E_NOTICE)
$mensaje = "";
	$sql = "UPDATE reportes SET falla = '$Falla', solucion = '$Solucion', tecnico = '$Tecnico', apoyo = '$Apoyo', atendido = '$Atendido', atender_visita = '$Atender_Visita',  fecha_solucion = '$FechaAtendido'".$mas." ,hora_atendido = '$Hora', campo = '$Campo' WHERE id_reporte = $IdReporte";
	if(mysqli_query($conn, $sql)){
		$mensaje = '<script>M.toast({html:"Reporte actualizado correctamente.", classes: "rounded"})</script>';
		if ($resultado['campo'] == 1 AND $Campo == 1) {
			if (mysqli_num_rows(mysqli_query($conn, "SELECT * FROM materiales WHERE id_cliente='$IdCliente' AND antena='$Antena' AND router='$Router' AND cable='$Cable' AND tubos='$Tubos' AND extras='$Extras' AND bobina='$Bobina' AND fecha='$FechaAtendido' AND usuarios='$Tecnicos' AND tipo='$Tipo'"))>0){
	 			echo '<script >M.toast({html:"Ya se encuentran valores similares registrados el dia de hoy.", classes: "rounded"})</script>';
			}else{
				$Sql_Mat = "INSERT INTO materiales(id_cliente, antena, router, cable, tubos, extras, bobina, fecha, usuarios, tipo) VALUES('$IdCliente','$Antena', '$Router', '$Cable', '$Tubos', '$Extras', '$Bobina', '$FechaAtendido', '$Tecnicos', '$Tipo')";
				if(mysqli_query($conn, $Sql_Mat)){
					echo '<script>M.toast({html:"Material insertado.", classes: "rounded"})</script>';	
				}else{
					echo '<script>M.toast({html:"Ocurrio un error Material.", classes: "rounded"})</script>';
				}
			}
		}
		echo '<script>
				location.href="../views/reportes.php";
			 </script>';
	}else{
		$mensaje = '<script>M.toast({html:"Ha ocurrido un error UPDATE.", classes: "rounded"})</script>';	
	}

echo $mensaje;
echo mysqli_error($conn);
mysqli_close($conn);
?>