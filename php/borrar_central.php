<?php
include('../php/is_logged.php');
include('../php/conexion.php');
include('../php/superAdmin.php');

$IdCentral = $conn->real_escape_string($_POST['valorIdCentral']);

if(mysqli_query($conn, "DELETE FROM centrales WHERE id = '$IdCentral'")){
    echo '<script >M.toast({html:"Central Borrada..", classes: "rounded"})</script>';
    ?>
<script>
	var a = document.createElement("a");
	a.href = "../views/centrales.php";
	a.click();   
</script>
	<?php	
}else{
    echo '<script >M.toast({html:"Ocurrio un error...", classes: "rounded"})</script>';
}
?>