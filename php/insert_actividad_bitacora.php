<?php
session_start();
include('../php/conexion.php');
date_default_timezone_set('America/Mexico_City');

$Descripcion = $conn->real_escape_string($_POST['valorSolucion']);
$Apoyo = $conn->real_escape_string($_POST['valorApoyo']);
$Campo = $conn->real_escape_string($_POST['valorCampo']);
$Fechahoy = date('Y-m-d');
$Hora = date('H:i:s');
$id_user = $_SESSION['user_id'];

#VERIFICAMOS SI ENCONTRAMOS UN CLIENTE ESPECIAL CON LOS DATOS DEL USUARIO A CREAR LA ACTIVIDAD
if (mysqli_num_rows(mysqli_query($conn, "SELECT * FROM especiales WHERE referencia = 'Actividad' AND usuario = $id_user"))<=0) {
	#SI NO EXISTE EL CLIENTE ESPECIAL USUARIO COMO CLIENTE SE CREA EL CLIENTE
	$usuario = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM users WHERE user_id = '$id_user'"));
	$Nombre = 'SIC: '.$usuario['firstname'].' '.$usuario['lastname'];
	#CREAR EL CLIENTE INSTERT
	mysqli_query($conn, "INSERT INTO especiales (nombre, telefono, referencia, lugar, usuario) VALUES ('$Nombre', 'N/A', 'Actividad', 16, $id_user)");
}

#SELECIIONAMOS AL CLIENTE ESPECIAL (USUARIO YA ANTES REGISTRADO O CREADO)
$cliente = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM especiales WHERE referencia = 'Actividad' AND usuario = $id_user"));
$id_cliente = $cliente['id_cliente'];

#VERIFICAMOS QUE LA ACTIVIDAD NO HAYA SIDO CREADA PARA EVITAR DUPLICIDAD
if (mysqli_num_rows(mysqli_query($conn, "SELECT * FROM reportes WHERE descripcion = 'Actividad' AND registro = $id_user AND solucion = '$Descripcion' AND fecha = '$Fechahoy'"))<=0) {
	#SU NO FE CREADA LA CREAMOS INSERT
	$sql = "INSERT INTO reportes (id_cliente, descripcion, fecha, hora_registro, registro, solucion, atendido, fecha_solucion, hora_atendido, tecnico, apoyo, campo) VALUES ($id_cliente, 'Actividad', '$Fechahoy', '$Hora', $id_user, '$Descripcion', 1, '$Fechahoy', '$Hora', $id_user, $Apoyo, $Campo)";
	if(mysqli_query($conn, $sql)){
		echo  '<script>M.toast({html:"Actividad creada correctamente.", classes: "rounded"})</script>';	
		?>
	  	<script>
			setTimeout("location.href='../views/home.php'", 1000);
		</script>
	  	<?php
	}else{
		echo  '<script>M.toast({html:"Ha ocurrido un error.", classes: "rounded"})</script>';	
	}
}else{
	echo  '<script>M.toast({html:"Ya existe una actividad similar el dia de hoy por el mismo usuario.", classes: "rounded"})</script>';	
}
mysqli_close($conn);
?>  