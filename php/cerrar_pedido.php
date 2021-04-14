<?php
date_default_timezone_set('America/Mexico_City');
include('../php/conexion.php');
$folio = $conn->real_escape_string($_POST['folio']);
$Fecha_hoy = date('Y-m-d');//CREAMOS UNA FECHA DEL DIA EN CURSO SEGUN LA ZONA HORARIA

if(mysqli_query($conn, "UPDATE pedidos SET cerrado = 1, fecha_cerrado = '$Fecha_hoy' WHERE folio = '$folio'")){
		echo '<script>M.toast({html:"Pedido actualizado correctamente..", classes: "rounded"})</script>';
}else{
		echo '<script>M.toast({html:"Ocurrio un error!", classes: "rounded"})</script>';
}

$Pedido = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM pedidos WHERE folio = $folio"));
$id_orden = $Pedido['id_orden'];
if ($id_orden<5000) {
	?>
	<script>
		id = <?php echo $id_orden; ?>;    
		var a = document.createElement("a");
		    a.target="_blank"
		    a.href = "../php/ruta.php?id="+id;
		    a.click();
	</script>  
	<?php
}
?>
<script>
	var a = document.createElement("a");
	 a.href = "../views/pedidos.php";
	 a.click();
</script>  