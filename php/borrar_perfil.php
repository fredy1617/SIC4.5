<?php
include('../php/is_logged.php');
include('../php/conexion.php');
include('../php/superAdmin.php');

$Id = $conn->real_escape_string($_POST['valorId']);

if(mysqli_query($conn, "DELETE FROM perfiles WHERE id = '$Id'")){
    echo '<script >M.toast({html:"Perfil Borrado...", classes: "rounded"})</script>';	
}else{
    echo '<script >M.toast({html:"Ocurrio un error...", classes: "rounded"})</script>';
}
?>
<script>
	var a = document.createElement("a");
	a.href = "../views/perfiles.php";
	a.click();
</script>