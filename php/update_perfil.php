<?php 
#INCLUIMOS EL ARCHIVO QUE CONTIENEN LA CONEXION A LA BASE DE DATOS
include('../php/conexion.php');
#MANDAMOS LLAMAR LA SESSION QUE ES DONDE TENEMOS LA INFORMACION DEL USUARIO LOGEADO
session_start();
$id_user = $_SESSION['user_id'];//ASIGNAMOS A UNA BARIABLE EL ID DEL USUARIO LOGUEADO

#RECIBIMOS LAS VARIABLES POR METODO POST DEL ARCHIVO perfiles.php DEL FORMULARIO
$Id = $conn->real_escape_string($_POST['valorId']);
$Nombre = $conn->real_escape_string($_POST['valorNombre']);
$Descripcion = $conn->real_escape_string($_POST['valorDescripcion']);
$Precio = $conn->real_escape_string($_POST['valorPrecio']);

//o $consultaBusqueda sea igual a nombre + (espacio) + apellido
$sql = "UPDATE perfiles SET nombre='$Nombre', descripcion='$Descripcion', costo ='$Precio', usuario = '$id_user' WHERE id='$Id'";
if(mysqli_query($conn, $sql)){
	echo '<script>M.toast({html:"El perfil se actualizó correctamente.", classes: "rounded"})</script>';
	?>
	<script>
	  var a = document.createElement("a");
		a.href = "../views/perfiles.php";
		a.click();   
	</script>
	<?php
}else{
	echo '<script>M.toast({html:"Ha ocurrido un error.", classes: "rounded"})</script>';	
}

mysqli_close($conn);
?>