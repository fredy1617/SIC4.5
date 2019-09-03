<?php 
include('../php/conexion.php');
include('is_logged.php');
date_default_timezone_set('America/Mexico_City');

$id_user = $_SESSION['user_id'];
$Nombres = $conn->real_escape_string($_POST['valorNombres']);
$Telefono = $conn->real_escape_string($_POST['valorTelefono']);
$Comunidad = $conn->real_escape_string($_POST['valorComunidad']);
$Direccion = $conn->real_escape_string($_POST['valorDireccion']);
$Referencia = $conn->real_escape_string($_POST['valorReferencia']);
$Paquete = $conn->real_escape_string($_POST['valorPaquete']);
$Anticipo = $conn->real_escape_string($_POST['valorAnticipo']);
$CostoTotal = $conn->real_escape_string($_POST['valorCostoTotal']);
$Tipo_Campio = $conn->real_escape_string($_POST['valorTipo']);
$Servicio = $conn->real_escape_string($_POST['valorServicio']);
$Fecha_hoy = date('Y-m-d');

$user = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM users WHERE user_id = '$id_user'"));
$Usuario = $user['firstname'];


if($Anticipo > $CostoTotal){
    echo  '<script >M.toast({html:"Solo puedes descontar $'.$CostoTotal.'.00 pesos.", classes: "rounded"})</script>';
}else{
	if(mysqli_num_rows(mysqli_query($conn, "SELECT * FROM clientes WHERE nombre='$Nombres' AND telefono='$Telefono' AND lugar='$Comunidad' AND direccion='$Direccion' AND referencia='$Referencia'"))>0){
	 		echo '<script >M.toast({html:"Ya se encuentra un cliente con los mismos datos registrados.", classes: "rounded"})</script>';
	 	}else{
	 		if ($Servicio == "Telefonia") {				
				$sql = "INSERT INTO clientes (nombre, telefono, lugar, direccion, referencia, total, dejo, paquete, fecha_registro, registro, servicio) 
				VALUES('$Nombres', '$Telefono', '$Comunidad', '$Direccion', '$Referencia', '$CostoTotal', '$Anticipo', '$Paquete', '$Fecha_hoy','$Usuario','$Servicio')";
			}else{
				$TipoInt = $conn->real_escape_string($_POST['valorTipoInst']);
				$Contrato = 0;
				$Prepago = 1;
				if ($TipoInt == 1) {
					$Contrato = 1;
					$Prepago = 0;
				}
				$sql = "INSERT INTO clientes (nombre, telefono, lugar, direccion, referencia, total, dejo, paquete, fecha_registro, registro, servicio, contrato, Prepago) 
				VALUES('$Nombres', '$Telefono', '$Comunidad', '$Direccion', '$Referencia', '$CostoTotal', '$Anticipo', '$Paquete', '$Fecha_hoy','$Usuario','$Servicio', '$Contrato', '$Prepago')";
			}
	 		
			if(mysqli_query($conn, $sql)){
				echo '<script >M.toast({html:"La instalación se dió de alta satisfactoriamente.", classes: "rounded"})</script>';	
		    if ($Anticipo != 0) {	
		    	$rs = mysqli_query($conn, "SELECT MAX(id_cliente) AS id FROM clientes");
		        $row = mysqli_fetch_row($rs);
				$IdCliente = $row[0];
				$Descripcion = "Anticipo de Instalación";
				$Tipo = "Anticipo";
				if(mysqli_num_rows(mysqli_query($conn, "SELECT * FROM pagos WHERE id_cliente = $IdCliente AND descripcion = '$Descripcion' AND cantidad='$Anticipo'"))>0){
					echo '<script>M.toast({html:"Ya se encuentra un pago registrado con los mismos valores.", classes: "rounded"})</script>';
				}else{
				$sql2 = "INSERT INTO pagos (id_cliente, descripcion, cantidad, fecha, tipo, id_user, corte, tipo_cambio) VALUES ($IdCliente, '$Descripcion', '$Anticipo', '$Fecha_hoy', '$Tipo', $id_user, 0, '$Tipo_Campio')";
				if(mysqli_query($conn, $sql2)){
					echo '<script>M.toast({html:"El pago se dió de alta satisfcatoriamente.", classes: "rounded"})</script>';
				}
				}
			}
				echo "<script>window.open('../php/folioCliente.php', '_blank')</script>";
			}else{
				echo '<script >M.toast({html:"Ha ocurrido un error.", classes: "rounded"})</script>';	
			}
	 	}
}



mysqli_close($conn);
?>