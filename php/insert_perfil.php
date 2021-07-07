<?php 
#INCLUIMOS EL ARCHIVO QUE CONTIENEN LA CONEXION A LA BASE DE DATOS
include('../php/conexion.php');
#MANDAMOS LLAMAR LA SESSION QUE ES DONDE TENEMOS LA INFORMACION DEL USUARIO LOGEADO
session_start();
$id_user = $_SESSION['user_id'];//ASIGNAMOS A UNA BARIABLE EL ID DEL USUARIO LOGUEADO

#RECIBIMOS LAS VARIABLES POR METODO POST DEL ARCHIVO perfiles.php DEL FORMULARIO
$Nombre = $conn->real_escape_string($_POST['valorNombre']);
$Descripcion = $conn->real_escape_string($_POST['valorDescripcion']);
$Precio = $conn->real_escape_string($_POST['valorPrecio']);

#VERIFICAMOS SI NO HAY UN PERFIL CON EL MISMO NOMBRE 
if(mysqli_num_rows(mysqli_query($conn, "SELECT * FROM perfiles WHERE nombre='$Nombre'"))>0){
    echo '<script>M.toast({html :"Ya se encuentra un perfil con el mismo nombre.", classes: "rounded"})</script>';
}else{
    //o $consultaBusqueda sea igual a nombre + (espacio) + apellido
    $sql = "INSERT INTO perfiles (nombre, descripcion, costo, usuario) VALUES('$Nombre', '$Descripcion', '$Precio', $id_user)";
    if(mysqli_query($conn, $sql)){
    	echo '<script>M.toast({html :"El perfil se registró satisfactoriamente.", classes: "rounded"})</script>';
        ?>
          <script>    
              setTimeout("location.href='../views/perfiles.php'", 800);
          </script>
        <?php
    }else{
    	echo '<script>M.toast({html :"Ha ocurrido un error.", classes: "rounded"})</script>';	
    }
}
?>