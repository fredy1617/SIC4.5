<?php 
session_start();
date_default_timezone_set('America/Mexico_City');
include('conexion.php');

$id_user = $_SESSION['user_id'];

$IDComunidad = $conn->real_escape_string($_POST['valorComunidad']);
$Descripcion = $conn->real_escape_string($_POST['valorDescripcion']);
$Referencia = $conn->real_escape_string($_POST['valorReferencia']);
$Comunidad = mysqli_fetch_array(mysqli_query($conn,"SELECT * FROM comunidades WHERE id_comunidad = '$IDComunidad'"));
$Nombres = 'SIC-'.$Comunidad['nombre'];
$Registro = date('Y-m-d');
$Hora = date('H:i:s');
$Descripcion= "'Mantenimiento: ".$Descripcion."'";

if (isset($Nombres)) {
	$sql_cliente = mysqli_query($conn, "SELECT * FROM especiales WHERE nombre='$Nombres' AND telefono='4339256286' AND lugar='$IDComunidad'");
	if(mysqli_num_rows($sql_cliente)>0){
	 	echo '<script >M.toast({html:"Ya se encuentra un cliente con los mismos datos registrados.", classes: "rounded"})</script>';
	 	$encontro =  mysqli_fetch_array($sql_cliente);            
        $IdCliente = $encontro['id_cliente'];
		$sql = "INSERT INTO reportes (id_cliente, descripcion, fecha, hora_registro, registro) VALUES ($IdCliente, ".$Descripcion.", '$Registro', '$Hora', '$id_user')";
		if(mysqli_query($conn, $sql)){
			?>
			<script>
				var a = document.createElement("a");
				a.href = "../views/mantenimiento.php";
				a.click();
				</script>
			<?php
		}else{
			echo '<script>M.toast(html:"Ha ocurrido un error.", classes: "rounded")</script>';	
		}
	}else{
		$sql = "INSERT INTO especiales (nombre, telefono, lugar, referencia, usuario, mantenimiento) 
				VALUES('$Nombres', '4339256286', '$IDComunidad',  '$Referencia','$id_user', '1')";
		if(mysqli_query($conn, $sql)){
			echo '<script >M.toast({html:"Se registro el cliente especial satisfactoriamente.", classes: "rounded"})</script>';
			$ultimo =  mysqli_fetch_array(mysqli_query($conn, "SELECT MAX(id_cliente) AS id FROM especiales"));            
        	$IdCliente = $ultimo['id'];
			$sql = "INSERT INTO reportes (id_cliente, descripcion, fecha, hora_registro, registro) VALUES ($IdCliente, ".$Descripcion.", '$Registro', '$Hora', '$id_user')";
			if(mysqli_query($conn, $sql)){
				?>
				<script>
					var a = document.createElement("a");
					a.href = "../views/mantenimiento.php";
					a.click();
				</script>
				<?php
			}else{
				echo '<script>M.toast(html:"Ha ocurrido un error.", classes: "rounded")</script>';	
			}
		}else{
			echo '<script >M.toast({html:"Ha ocurrido un error.", classes: "rounded"})</script>';	
		}
	}
}
mysqli_close($conn);
?>