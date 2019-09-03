<?php 
include('../php/conexion.php');
date_default_timezone_set('America/Mexico_City');

$Nombres = $conn->real_escape_string($_POST['valorNombres']);
$Telefono = $conn->real_escape_string($_POST['valorTelefono']);
$Comunidad = $conn->real_escape_string($_POST['valorComunidad']);
$Descripcion = $conn->real_escape_string($_POST['valorDescripcion']);
$Referencia = $conn->real_escape_string($_POST['valorReferencia']);
$Usuario = $conn->real_escape_string($_POST['valorUsuario']);
$Registro = date('Y-m-d');

//Variable vacía (para evitar los E_NOTICE)
$mensaje = "";
	if (isset($Nombres)) {
	 	$sql_consulta = "SELECT * FROM especiales WHERE nombre='$Nombres' AND telefono='$Telefono' AND lugar='$Comunidad'";
	 	$consultaBusqueda = mysqli_query($conn, $sql_consulta);
	 	if(mysqli_num_rows($consultaBusqueda)>0){
	 		$mensaje = '<script >M.toast({html:"Ya se encuentra un cliente con los mismos datos registrados.", classes: "rounded"})</script>';
	 	}else{
			//o $consultaBusqueda sea igual a nombre + (espacio) + apellido
			$sql = "INSERT INTO especiales (nombre, telefono, lugar, referencia, usuario) 
				VALUES('$Nombres', '$Telefono', '$Comunidad',  '$Referencia','$Usuario')";
			if(mysqli_query($conn, $sql)){
				$mensaje = '<script >M.toast({html:"Se registro el cliente especial satisfactoriamente.", classes: "rounded"})</script>';
				$ultimo =  mysqli_fetch_array(mysqli_query($conn, "SELECT MAX(id_cliente) AS id FROM especiales"));            
        		$IdCliente = $ultimo['id'];
        		echo $IdCliente;
        		$Descripcion2= "'Reporte Especial: ".$Descripcion."'";
				$sql = "INSERT INTO reportes (id_cliente, descripcion, fecha) VALUES ($IdCliente, ".$Descripcion2.", '$Registro')";
				if(mysqli_query($conn, $sql)){
					?>
				  <script>
					  var a = document.createElement("a");
					    a.href = "../views/reportes.php";
					    a.click();   
				  </script>
				  <?php
				}else{
					$mensaje = '<script>M.toast(html:"Ha ocurrido un error.", classes: "rounded")</script>';	
				}
			}else{
				$mensaje = '<script >M.toast({html:"Ha ocurrido un error.", classes: "rounded"})</script>';	
			}
		}
	}

echo $mensaje;
mysqli_close($conn);
?>