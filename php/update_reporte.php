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
$Coordenada = $conn->real_escape_string($_POST['valorCoordenada']);

#Tecnicos
$T1 = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM users WHERE user_id = $Tecnico"));
if ($Apoyo == 0) {
	$Tecnicos = $T1['user_name'];
}else{
	$A2 = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM users WHERE user_id = $Apoyo"));
	$Tecnicos = $T1['user_name'].', '.$A2['user_name'];
}
  
$sql2= "UPDATE clientes SET nombre = '$Nombre', telefono = '$Telefono', direccion = '$Direccion', referencia='$Referencia', coordenadas = '$Coordenada' WHERE id_cliente=$IdCliente ";
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

$sql = "UPDATE reportes SET falla = '$Falla', solucion = '$Solucion', tecnico = '$Tecnico', apoyo = '$Apoyo', atendido = '$Atendido', atender_visita = '$Atender_Visita',  fecha_solucion = '$FechaAtendido'".$mas." ,hora_atendido = '$Hora', campo = '$Campo' WHERE id_reporte = $IdReporte";
if(mysqli_query($conn, $sql)){
		echo '<script>M.toast({html:"Reporte actualizado correctamente.", classes: "rounded"})</script>';
		if ($IdCliente > 10000) {
			$Es = 'Mantenimiento';
			$ir ='mantenimiento.php';
		}else{
			$Es = 'Reporte';
			$ir = 'reportes.php';
		}
		if ($Campo == 1 AND $Atendido == 2) {
			$sql2 = "UPDATE reportes SET fecha_d = '$FechaAtendido', hora_d = '$Hora', tecnico_d = '$Tecnico' WHERE id_reporte = '$IdReporte'";
			if(mysqli_query($conn, $sql2)){
				echo '<script>M.toast({html:"Diagnosticado y enviado a campo.", classes: "rounded"})</script>';
			}
		}
		if ($resultado['campo'] == 1 AND $Campo == 1 AND $Atendido == 1) {
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
			if (($Costo != '' OR $Costo > 0) AND $Es == 'Reporte') {
				# Insertar pago e imprimir ticket SOLO REPORTE
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
				$sql = "INSERT INTO pagos (id_cliente, descripcion, cantidad, fecha, hora, tipo, id_user, corte, corteP, tipo_cambio) VALUES ($IdCliente, '$Descripcion', '$Costo', '$FechaAtendido', '$Hora', 'Reporte', $Tecnico, 0, 0, '$Tipo_Campio')";
				if ($Tipo_Campio == "Credito") {
					$mysql = "INSERT INTO deudas(id_cliente, cantidad, fecha_deuda, tipo, descripcion, usuario) VALUES ($IdCliente, '$Costo', '$FechaAtendido', 'Reporte', '$Descripcion', $Tecnico)";
					mysqli_query($conn,$mysql);
					$ultimo =  mysqli_fetch_array(mysqli_query($conn, "SELECT MAX(id_deuda) AS id FROM deudas WHERE id_cliente = $IdCliente"));            
					$id_deuda = $ultimo['id'];
					$sql = "INSERT INTO pagos (id_cliente, descripcion, cantidad, fecha, hora, tipo, id_user, corte, corteP, tipo_cambio, id_deuda) VALUES ($IdCliente, '$Descripcion', '$Costo', '$FechaAtendido', '$Hora', 'Reporte', $Tecnico, 0, 0, '$Tipo_Campio', $id_deuda)";
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
			}elseif ($Es == 'Reporte') {
				#Imprimir ticket SIN COSTO SI ES REPORTE
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
				

				include('is_logged.php');
				$IdTecnico = $_SESSION['user_id'];
				$Sql_Mat = "INSERT INTO materiales(id_cliente, antena, router, cable, tubos, extras, fecha, usuarios, tipo, es) VALUES('$IdCliente','$Antena', '$Router', '$Cable', '$Tubos', '$Extras', '$FechaAtendido', '$Tecnicos', '$Tipo', '$Es')";
				if(mysqli_query($conn, $Sql_Mat)){
					echo '<script>M.toast({html:"Material insertado.", classes: "rounded"})</script>';	
					if ($Antena != '') {
						$sqlA = "UPDATE stock_tecnicos SET uso = 1, disponible = 1, fecha_salida = '$FechaAtendido' WHERE serie = '$Antena' AND disponible = 0 AND tipo = 'Antena' AND tecnico = $IdTecnico";
						if(mysqli_query($conn, $sqlA)){
							echo '<script>M.toast({html:"Se dio de baja la antena: "'.$Antena.', classes: "rounded"})</script>';
						}
					}
					if ($Router != '') {
						$sqlR = "UPDATE stock_tecnicos SET uso = 1, disponible = 1, fecha_salida = '$FechaAtendido' WHERE serie = '$Router' AND disponible = 0 AND tipo = 'Router' AND tecnico = $IdTecnico";
						if(mysqli_query($conn, $sqlR)){
							echo '<script>M.toast({html:"Se dio de baja el router: "'.$Router.', classes: "rounded"})</script>';
						}
					}
					if ($Cable > 0) {
						$bobina = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM stock_tecnicos WHERE uso < 300 AND tecnico = $IdTecnico AND disponible = 0 AND tipo = 'Bobina'"));
						$Total =$bobina['uso']+$Cable;
						if ($Total >= 300) {
							if (mysqli_query($conn, "UPDATE stock_tecnicos SET uso = 300, fecha_salida = '$FechaAtendido', disponible = 1  WHERE uso < 300 AND tecnico = $IdTecnico AND disponible = 0 AND tipo = 'Bobina'")){
							    echo '<script>M.toast({html:"Se dio de baja la bobina.", classes: "rounded"})</script>';
							}
						}else{
							if (mysqli_query($conn, "UPDATE stock_tecnicos SET uso = $Total WHERE uso < 300 AND tecnico = $IdTecnico AND disponible = 0 AND tipo = 'Bobina'")){
							    echo '<script>M.toast({html:"Se actualizo la Bobina...", classes: "rounded"})</script>';
							}
						}	
					}
					if ($Tubos > 0) {
						$Total = mysqli_fetch_array(mysqli_query($conn, "SELECT SUM(cantidad) AS suma FROM stock_tecnicos WHERE tecnico = $IdTecnico AND disponible = 0 AND tipo = 'Tubo(s)'"));
      					$Uso = mysqli_fetch_array(mysqli_query($conn, "SELECT SUM(uso) AS suma FROM stock_tecnicos WHERE tecnico = $IdTecnico AND disponible = 0 AND tipo = 'Tubo(s)'"));
      					$Disponibles = $Total['suma']-$Uso['suma'];
      					if ($Tubos <= $Disponibles) {
      						$Entra = True;
      						while ($Entra) {
						      $Prox_tubo = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM stock_tecnicos WHERE tecnico = $IdTecnico AND disponible = 0 AND tipo = 'Tubo(s)' LIMIT 1"));
						      $id = $Prox_tubo['id'];
						      $Cantidad = $Prox_tubo['cantidad'];
						      $Uso = $Prox_tubo['uso']+$Tubos;
						      $Resta = $Cantidad-$Uso;

						      if ($Resta < 0) {
						      	if (mysqli_query($conn, "UPDATE stock_tecnicos SET uso = $Cantidad, fecha_salida = '$FechaAtendido', disponible = 1 WHERE id = $id")) {
							        echo '<script>M.toast({html:"Tubos actualizados...", classes: "rounded"})</script>';
							    }
						        $Entra = True;  
						        $Tubos = $Uso-$Cantidad;
						      }else if ($Resta == 0) {
						      	if (mysqli_query($conn, "UPDATE stock_tecnicos SET uso = $Cantidad, fecha_salida = '$FechaAtendido', disponible = 1 WHERE id = $id")) {
							        echo '<script>M.toast({html:"Tubos actualizados...", classes: "rounded"})</script>';
							    }
						        $Entra = False;  
						        $Tubos = $Uso-$Cantidad;
						      }else{
						      	if (mysqli_query($conn, "UPDATE stock_tecnicos SET uso = $Uso WHERE id = $id")) {
							        echo '<script>M.toast({html:"Tubos actualizados...", classes: "rounded"})</script>';
							    }
						     	$Entra = False;
						      }
						  }
      					}
					}
				}else{
					echo '<script>M.toast({html:"Ocurrio un error Material.", classes: "rounded"})</script>';
				}
			}
		}
		echo '<script>
				location.href="../views/'.$ir.'";
			 </script>';
}else{
		echo '<script>M.toast({html:"Ha ocurrido un error UPDATE.", classes: "rounded"})</script>';	
}

echo mysqli_error($conn);
mysqli_close($conn);
?>