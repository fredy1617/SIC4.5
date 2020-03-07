<?php
session_start();
include('../php/conexion.php');
date_default_timezone_set('America/Mexico_City');

$Nombre = $conn->real_escape_string($_POST ['valorNombre']);
$Telefono = $conn->real_escape_string($_POST['valorTelefono']);
$Direccion = $conn->real_escape_string($_POST['valorDireccion']);
$Referencia = $conn->real_escape_string($_POST['valorReferencia']);
$Coordenadas = $conn->real_escape_string($_POST['valorCoordenada']);
$Descripcion = $conn->real_escape_string($_POST['valorDescripcion']);
$IdCliente = $conn->real_escape_string($_POST['valorIdCliente']);
$Fecha = date('Y-m-d');
$Hora = date('H:i:s');
$id_user = $_SESSION['user_id'];

  
$sql2= "UPDATE clientes SET nombre = '$Nombre', telefono = '$Telefono', direccion = '$Direccion', referencia='$Referencia', coordenadas = '$Coordenadas' WHERE id_cliente=$IdCliente ";
if (mysqli_query($conn, $sql2)) {
  echo  '<script>M.toast({html:"Información actualizada.", classes: "rounded"})</script>';
}
//o $consultaBusqueda sea igual a nombre + (espacio) + apellido
$sql = "INSERT INTO reportes (id_cliente, descripcion, fecha, hora_registro, registro) VALUES ($IdCliente, '$Descripcion', '$Fecha', '$Hora', $id_user)";
if(mysqli_query($conn, $sql)){
	?>
  <script>    
    var a = document.createElement("a");
      a.href = "../views/reportes.php";
      a.click();
  </script>
  <?php
}else{
	echo  '<script>M.toast({html:"Ha ocurrido un error.", classes: "rounded"})</script>';	
}

mysqli_close($conn);
?>  