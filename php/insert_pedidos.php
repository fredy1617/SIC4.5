<?php 
#INCLUIMOS EL ARCHIVO CON LA CONEXION A LA BASE DE DATOS
include('../php/conexion.php');
session_start();
date_default_timezone_set('America/Mexico_City');
$Nombre = $conn->real_escape_string($_POST['valorNombre']);
$Orden = $conn->real_escape_string($_POST['valorOrden']);
$id_user = $_SESSION['user_id'];
$Fecha_hoy = date('Y-m-d');
$Hora = date('H:i:s');

$sql_pedido = "SELECT * FROM pedidos WHERE nombre = '$Nombre' AND fecha = '$Fecha_hoy'";
if(mysqli_num_rows(mysqli_query($conn, $sql_pedido))>0){
    echo '<script>M.toast({html :"Ya se encuentra un pedidio con la misa informacion.", classes: "rounded"})</script>';
}else{
//o $consultaBusqueda sea igual a nombre + (espacio) + apellido
$sql = "INSERT INTO pedidos (nombre, id_orden, fecha, hora, usuario) VALUES('$Nombre', '$Orden', '$Fecha_hoy', '$Hora', '$id_user')";
if(mysqli_query($conn, $sql)){
	echo '<script>M.toast({html :"el pedido se registró satisfactoriamente.", classes: "rounded"})</script>';
    $ultimo =  mysqli_fetch_array(mysqli_query($conn, "SELECT MAX(folio) AS folio FROM pedidos WHERE usuario = $id_user"));            
    $folio = $ultimo['folio'];
    ?>
    <script>
        var a = document.createElement("a");
        a.href = "../views/detalles_pedido.php?folio="+<?php echo $folio; ?>;
        a.click();
    </script>
    <?php
}else{
	echo '<script>M.toast({html :"Ha ocurrido un error.", classes: "rounded"})</script>';
    ?>
    <script>
        setTimeout("location.href='pedidos.php", 500);
    </script>
    <?php	
}
}
mysqli_close($conn);
?>