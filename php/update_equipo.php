<?php 
include('../php/conexion.php');
date_default_timezone_set('America/Mexico_City');
include('is_logged.php');

$id_user = $_SESSION['user_id'];
$IdEquipo = $conn->real_escape_string($_POST['valorIdEquipo']);
$Nombre = $conn->real_escape_string($_POST['valorNombre']);
$Marca = $conn->real_escape_string($_POST['valorMarca']);
$Modelo = $conn->real_escape_string($_POST['valorModelo']);
$IP = $conn->real_escape_string($_POST['valorIP']);
$Descripcion = $conn->real_escape_string($_POST['valorDescripcion']);
$Status = $conn->real_escape_string($_POST['valorEstatus']);
$Razon = $conn->real_escape_string($_POST ['valorRazon']);
$Modificacion = $conn->real_escape_string($_POST['valorModificacion']);
$IdCentral = $conn->real_escape_string($_POST ['valorIdCentral']);
 
$sql2= "UPDATE equipos SET nombre = '$Nombre', marca = '$Marca', modelo = '$Modelo', ip='$IP', descripcion='$Descripcion', status='$Status', modificacion = '$Modificacion', razon='$Razon', usuario='$id_user' WHERE id=$IdEquipo ";
if (mysqli_query($conn, $sql2)) {
  echo  '<script>M.toast({html:"Información actualizada.", classes: "rounded"})</script>';
}else{
  echo  '<script>M.toast({html:"Ocurrio un error.", classes: "rounded"})</script>';
}

mysqli_close($conn);
?>
<script>    
    function atras(id_central) {
      setTimeout("location.href='../views/equipos.php?id=<?php echo $IdCentral; ?>'", 800);
    };
    atras(<?php echo $IdCentral; ?>);
</script>