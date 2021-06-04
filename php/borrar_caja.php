<?php
include('../php/is_logged.php');
include('../php/conexion.php');
include('../php/superAdmin.php');

$Id = $conn->real_escape_string($_POST['valorId']);

if(mysqli_query($conn, "DELETE FROM historila_caja_ch WHERE id = '$Id'")){
    echo '<script >M.toast({html:"Registro Borrado..", classes: "rounded"})</script>';
    ?>
    <script>
        setTimeout("location.href='../views/caja_chica.php'", 700);
    </script>
    <?php   
}else{
    echo '<script >M.toast({html:"Ocurrio un error...", classes: "rounded"})</script>';
}
?>