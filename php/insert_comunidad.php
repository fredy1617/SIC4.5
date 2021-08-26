<?php 
include('../php/conexion.php');
date_default_timezone_set('America/Mexico_City');
$Nombre = $conn->real_escape_string($_POST['valorNombre']);
$Instalacion = $conn->real_escape_string($_POST['valorInstalacion']);
$Servidor = $conn->real_escape_string($_POST['valorServidor']);
$Municipio = $conn->real_escape_string($_POST['valorMunicipio']);

//Variable vacía (para evitar los E_NOTICE)
$mensaje = "";

$sql_comunidad = "SELECT * FROM comunidades WHERE nombre='$Nombre'";
if(mysqli_num_rows(mysqli_query($conn, $sql_comunidad))>0){
    $mensaje = '<script>M.toast({html :"Ya se encuentra una comunidad con el mismo nombre.", classes: "rounded"})</script>';
}else{
    //o $consultaBusqueda sea igual a nombre + (espacio) + apellido
    $sql = "INSERT INTO comunidades (nombre, municipio, instalacion, servidor) VALUES('$Nombre', '$Municipio', '$Instalacion', '$Servidor')";
    if(mysqli_query($conn, $sql)){
        echo '<script>M.toast({html :"La comunidad se registró satisfactoriamente.", classes: "rounded"})</script>';
        ?>
        <script>    
            setTimeout("location.href='../views/comunidades.php'", 800);
        </script>
        <?php
    }else{
        echo '<script>M.toast({html :"Ha ocurrido un error.", classes: "rounded"})</script>';   
    }
}
mysqli_close($conn);
?>