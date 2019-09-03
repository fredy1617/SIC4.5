<?php
include('../php/conexion.php');

$Imagen = addslashes(file_get_contents($_FILES['Imagen']['tmp_name']));
$Cantidad = $_POST['cantidad'];
$Precio = $_POST['precio'];
$Descripcion = $_POST['descripcion'];

if (mysqli_query($conn, "INSERT INTO productos (precio, cantidad, descripcion, imagen) VALUES ('$Precio', '$Cantidad', '$Descripcion', '$Imagen')")) {

	$datos = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM productos LIMIT 1"));
	echo "Se inserto esta madre";
?>
<h3><?php echo $datos['descripcion']; ?></h3>
<img src="data:image/jpg;base64,<?php echo base64_encode($datos['imagen']); ?>" />
<?php 
}
 ?>