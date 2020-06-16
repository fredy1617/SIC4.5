<?php 
include('../php/conexion.php');
date_default_timezone_set('America/Mexico_City');
$IdReporte = $conn->real_escape_string($_POST['valorIdReporte']);
$Falla = $conn->real_escape_string($_POST['valorFalla']);
$Solucion = $conn->real_escape_string($_POST['valorSolucion']);
$Tecnico = $conn->real_escape_string($_POST['valorTecnico']);
$Atendido = $conn->real_escape_string($_POST['valorAtendido']);
$Fecha_visita = $conn->real_escape_string($_POST['valorFecha']);
$Apoyo = $conn->real_escape_string($_POST['valorApoyo']);
$Tipo_Campio = $conn->real_escape_string($_POST['valorTipo_Cambio']);
$Campo = $conn->real_escape_string($_POST['valorCampo']);
$FechaAtendido = date('Y-m-d');
$Hora = date('H:i:s');

$IdCliente = $conn->real_escape_string($_POST['valorIdCliente']);
$Nombre = $conn->real_escape_string($_POST ['valorNombre']);
$Telefono = $conn->real_escape_string($_POST['valorTelefono']);
$Direccion = $conn->real_escape_string($_POST['valorDierccion']);
$Referencia = $conn->real_escape_string($_POST['valorReferencia']);

#Tecnicos
$T1 = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM users WHERE user_id = $Tecnico"));
if ($Apoyo == 0) {
	$Tecnicos = $T1['user_name'];
}else{
	$A2 = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM users WHERE user_id = $Apoyo"));
	$Tecnicos = $T1['user_name'].', '.$A2['user_name'];
}


#--- MATERIALES ---
$Antena = $conn->real_escape_string($_POST['valorAntena']);
$Router = $conn->real_escape_string($_POST['valorRouter']);
$Cable = $conn->real_escape_string($_POST['valorCable']);
$Tubos = $conn->real_escape_string($_POST['valorTubos']);
$ManoObra = $conn->real_escape_string($_POST['valorManoObra']);
$Extras = $conn->real_escape_string($_POST['valorExtras']);
$Tipo = $conn->real_escape_string($_POST['valorTipo']);

#------------COSTO DEL SERVICIO--------------
$Costo = $conn->real_escape_string($_POST['valorCosto']);
  
$sql2= "UPDATE clientes SET nombre = '$Nombre', telefono = '$Telefono', direccion = '$Direccion', referencia='$Referencia' WHERE id_cliente=$IdCliente ";
if (mysqli_query($conn, $sql2)) {
  echo  '<script>M.toast({html:"Información actualizada.", classes: "rounded"})</script>';
}
	
$Atender_Visita = $Atendido;

$mas = "";
if ($Fecha_visita != 0) {
	$Atendido = '1';
	$reporte   = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM reportes WHERE id_reporte =$IdReporte"));
	$nombreTecnico  = mysqli_fetch_array(mysqli_query($conn,"SELECT * FROM users WHERE user_id = '$Tecnico'"));
	$Array = explode("*", $reporte['descripcion']);
    $descripcion = $Array[0];

    if (count($Array)>1) {
        $descripcion = $Array[1];
    }
	$Nombre = $nombreTecnico['firstname'];
	$mas= ", visita = 1, fecha_visita = '$Fecha_visita', descripcion = '".$Nombre." Tienes una visita el día de HOY. Problema : *".$descripcion." ' , atender_visita = '$Atender_Visita'";
}
$resultado = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM reportes WHERE id_reporte = $IdReporte"));
//Variable vacía (para evitar los E_NOTICE)
$mensaje = "";
	$sql = "UPDATE reportes SET falla = '$Falla', solucion = '$Solucion', tecnico = '$Tecnico', apoyo = '$Apoyo', atendido = '$Atendido', atender_visita = '$Atender_Visita',  fecha_solucion = '$FechaAtendido'".$mas." ,hora_atendido = '$Hora', campo = '$Campo' WHERE id_reporte = $IdReporte";
	if(mysqli_query($conn, $sql)){
		$mensaje = '<script>M.toast({html:"Reporte actualizado correctamente.", classes: "rounded"})</script>';
		if ($resultado['campo'] == 1 AND $Campo == 1 AND $Atendido == 1) {
			if ($Costo != '' OR $Costo > 0) {
				# Insertar pago e imprimir ticket
				$Mat = '';
				if ($ManoObra != '') { $Mano = 'MANO DE OBRA ('.$ManoObra.')'; }
				if ($Antena != '') { $Mat.= $Antena.', '; }
				if ($Router != '') { $Mat.= $Router.', '; }
				if ($Cable > 0) { $Mat.= $Cable.' m de Cable,'; }
				if ($Tubos > 0) { $Mat.= $Tubos.' Tubo(s), '; }
				if ($Extras != '') { $Mat.= $Extras; }
				if ($Mat != '') {
					$Material = 'MATERIAL('.$Mat.')';
				}
				$Descripcion = $Mano.'; '.$Material;
				$sql = "INSERT INTO pagos (id_cliente, descripcion, cantidad, fecha, tipo, id_user, corte, tipo_cambio) VALUES ($IdCliente, '$Descripcion', '$Costo', '$FechaAtendido', 'Reporte', $Tecnico, 0, '$Tipo_Campio')";
				if ($Tipo_Campio == "Credito") {
					$mysql = "INSERT INTO deudas(id_cliente, cantidad, fecha_deuda, tipo, descripcion, usuario) VALUES ($IdCliente, '$Costo', '$FechaAtendido', 'Reporte', '$Descripcion', $Tecnico)";
					mysqli_query($conn,$mysql);
					$ultimo =  mysqli_fetch_array(mysqli_query($conn, "SELECT MAX(id_deuda) AS id FROM deudas WHERE id_cliente = $IdCliente"));            
					$id_deuda = $ultimo['id'];
					$sql = "INSERT INTO pagos (id_cliente, descripcion, cantidad, fecha, tipo, id_user, corte, tipo_cambio, id_deuda) VALUES ($IdCliente, '$Descripcion', '$Costo', '$FechaAtendido', 'Reporte', $Tecnico, 0, '$Tipo_Campio', $id_deuda)";
				}
				if(mysqli_query($conn, $sql)){
					echo '<script>M.toast({html:"El pago se dió de alta satisfcatoriamente.", classes: "rounded"})</script>';
					$ultimo =  mysqli_fetch_array(mysqli_query($conn, "SELECT MAX(id_pago) AS id FROM pagos WHERE id_cliente = $IdCliente"));           
					$id_pago = $ultimo['id'];
					?>
					<script>
					id_pago = <?php echo $id_pago; ?>;
					var a = document.createElement("a");
					    a.target = "_blank";
						a.href = "../php/imprimir.php?IdPago="+id_pago;
						a.click();
					</script>
   					<?php 
				}else{
					echo '<script>M.toast({html:"Ocurrio un error en el pago.", classes: "rounded"})</script>';
				}
			}else{
				#Imprimir ticket SIN COSTO
				?>
				<script>
					IdCliente = <?php echo $IdCliente; ?>;
					var a = document.createElement("a");
					    a.target = "_blank";
						a.href = "../php/ticket0.php?Id="+IdCliente;
						a.click();
				</script>
   				<?php 
			}
			if (mysqli_num_rows(mysqli_query($conn, "SELECT * FROM materiales WHERE id_cliente='$IdCliente' AND antena='$Antena' AND router='$Router' AND cable='$Cable' AND tubos='$Tubos' AND extras='$Extras' AND fecha='$FechaAtendido' AND usuarios='$Tecnicos' AND tipo='$Tipo'"))>0){
	 			echo '<script >M.toast({html:"Ya se encuentran valores similares registrados el dia de hoy.", classes: "rounded"})</script>';
			}else{
				if ($IdCliente > 10000) {
					$Es = 'Especial o Mantenimiento';
				}else{
					$Es = 'Reporte';
				}
				$Sql_Mat = "INSERT INTO materiales(id_cliente, antena, router, cable, tubos, extras, fecha, usuarios, tipo, es) VALUES('$IdCliente','$Antena', '$Router', '$Cable', '$Tubos', '$Extras', '$FechaAtendido', '$Tecnicos', '$Tipo', '$Es')";
				if(mysqli_query($conn, $Sql_Mat)){
					echo '<script>M.toast({html:"Material insertado.", classes: "rounded"})</script>';	
				}else{
					echo '<script>M.toast({html:"Ocurrio un error Material.", classes: "rounded"})</script>';
				}
			}
		}
		echo '<script>
				location.href="../views/reportes.php";
			 </script>';
	}else{
		$mensaje = '<script>M.toast({html:"Ha ocurrido un error UPDATE.", classes: "rounded"})</script>';	
	}

echo $mensaje;
echo mysqli_error($conn);
mysqli_close($conn);
?>