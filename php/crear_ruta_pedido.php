<?php
#INCLUIMOS EL ARCHIVO CON LA CONEXION A LA BASE DE DATOS
include('../php/conexion.php');
#INCLUIMOS EL PHP DONDE VIENE LA INFORMACION DEL INICIO DE SESSION
include('is_logged.php');
$id_user = $_SESSION['user_id'];
date_default_timezone_set('America/Mexico_City');
$Fecha = date('Y-m-d'); 
$Hora = date('H:i:s');
$Responsable = $conn->real_escape_string($_POST['valorResponsable']);
$Acompañante = $conn->real_escape_string($_POST['valorAcompañante']);
$Vehiculo = $conn->real_escape_string($_POST['valorVehiculo']);
$Bobina = $conn->real_escape_string($_POST['valorBobina']);
$Vale = $conn->real_escape_string($_POST['valorVale']);
$aux= mysqli_num_rows(mysqli_query($conn, "SELECT * FROM rutas WHERE fecha = '$Fecha' AND estatus = 0 AND responsable='$Responsable'  AND acompanante='$Acompañante'"));
if($aux<=0 or $aux==null){
	if (mysqli_query($conn, "INSERT INTO rutas(fecha, hora, responsable, acompanante) VALUES ('$Fecha', '$Hora', '$Responsable', '$Acompañante')")) {
		echo '<script>M.toast({html : "Se creo la ruta correctamente.", classes: "rounded"})</script>';
		$ultimo =  mysqli_fetch_array(mysqli_query($conn, "SELECT MAX(id_ruta) AS id FROM rutas WHERE estatus=0"));            

	    $ultima_ruta = $ultimo['id'];
		//GUARDAR REPORTE DE RUTA
		mysqli_query($conn, "INSERT INTO reporte_rutas(id_ruta, vehiculo, bobina, vale) VALUES ('$ultima_ruta', '$Vehiculo', '$Bobina', '$Vale')");

		//modificar pendientes y reportes agregar id_ruta
	    mysqli_query($conn, "UPDATE tmp_pendientes SET ruta_inst = $ultima_ruta WHERE ruta_inst = 0 AND usuario = $id_user");
	    mysqli_query($conn, "UPDATE tmp_reportes SET ruta = $ultima_ruta WHERE ruta = 0 AND usuario = $id_user");
	    
	    $Nombre = 'Ruta No.'.$ultima_ruta;

		$sql = "INSERT INTO pedidos (nombre, id_orden, fecha, hora, fecha_requerido, usuario) VALUES('$Nombre', '$ultima_ruta', '$Fecha', '$Hora', '2000-01-01', '$id_user')";
		if(mysqli_query($conn, $sql)){
			echo '<script>M.toast({html :"el pedido se registró satisfactoriamente.", classes: "rounded"})</script>';
		    $ultimo =  mysqli_fetch_array(mysqli_query($conn, "SELECT MAX(folio) AS folio FROM pedidos WHERE usuario = $id_user"));            
		    $folio = $ultimo['folio'];
		    ?>
		    <script>
		        var a = document.createElement("a");
		        a.href = "../views/detalles_pedido_r.php?folio="+<?php echo $folio; ?>;
		        a.click();
		    </script>
		    <?php
		}else{
			echo '<script>M.toast({html :"Ha ocurrido un error.", classes: "rounded"})</script>';
		    ?>
		    <script>
		        setTimeout("location.href='menu_rutas.php", 500);
		    </script>
		    <?php	
		}
	}else{
		echo '<script>M.toast({html : "Ocurrio un error en la creación.", classes: "rounded"})</script>';
	}
}else{
	echo '<script>M.toast({html : "Ya se encuentra una ruta registrada con los mismos valores el día de hoy.", classes: "rounded"})</script>';
}
?>