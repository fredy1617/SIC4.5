<?php
date_default_timezone_set('America/Mexico_City');
include('../php/conexion.php');
include_once('../API/api_mt_include2.php');
$IP = $_POST['valorIP'];
$filtrarMaterial = $_POST['valorMaterial'];
$filtrarIdCliente = $_POST['valorIdCliente'];
$filtrarTecnico = $_POST['valorTecnicos'];

//Filtro anti-XSS
$caracteres_malos = array("<", ">", "\"", "'", "/", "<", ">", "'", "/");
$caracteres_buenos = array("& lt;", "& gt;", "& quot;", "& #x27;", "& #x2F;", "& #060;", "& #062;", "& #039;", "& #047;");

$id_user = $_SESSION['user_id'];

$Material = str_replace($caracteres_malos, $caracteres_buenos, $filtrarMaterial);
$IdCliente = str_replace($caracteres_malos, $caracteres_buenos, $filtrarIdCliente);
$Tecnico = str_replace($caracteres_malos, $caracteres_buenos, $filtrarTecnico);
$Liquidar = $conn->real_escape_string($_POST['valorLiquidar']);
$Tipo_Campio = $conn->real_escape_string($_POST['valorTipo_Cambio']);
$Direccion = $conn->real_escape_string($_POST['valorDireccion']);
$Referencia = $conn->real_escape_string($_POST['valorReferencia']);
$Coordenada = $conn->real_escape_string($_POST['valorCoordenada']);

$FechaInstalacion = date('Y-m-d');
$Hora = date('h:i:s');


if (filter_var($IP, FILTER_VALIDATE_IP)) {
	$sql_ip = "SELECT * FROM clientes WHERE ip='$IP'";
	if(mysqli_num_rows(mysqli_query($conn, $sql_ip))>0){
		echo '<script>M.toast({html :"Esta IP ya se encuentra asignada a un cliente.", classes: "rounded"})</script>';
	}else{
		//Buscamos el paquete
		$id_paquete1 = mysqli_fetch_array(mysqli_query($conn, "SELECT nombre, paquete FROM clientes WHERE id_cliente=$IdCliente"));
		$id_paquete = $id_paquete1['paquete'];
		$paquete = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM paquetes WHERE id_paquete=$id_paquete"));
		$limite = $paquete['subida']."/".$paquete['bajada'];
		//Buscamos el servidor
		$sql_lugar = mysqli_fetch_array(mysqli_query($conn, "SELECT lugar FROM clientes WHERE id_cliente=$IdCliente"));
		$lugar = $sql_lugar['lugar'];
		$sql_servidor = mysqli_fetch_array(mysqli_query($conn, "SELECT servidor, nombre FROM comunidades WHERE id_comunidad=$lugar"));
		$servidor = $sql_servidor['servidor'];
		$comunidad = $sql_servidor['nombre'];
		$datos = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM servidores WHERE id_servidor=$servidor"));

		$nombre_completo = $id_paquete1['nombre'];
		//////// configura tus datos
		$ServerList = $datos['ip']; //ip_de_tu_API
		$Username = $datos['user']; //usuario_API
		$Pass = $datos['pass']; //contraseña_API
		$Port = $datos['port']; //puerto_API
		/// VARIABLES DE FORMULARIO
		$target= $IP;  // IP Cliente
		$name= '#'.$IdCliente.'_'.strtoupper($comunidad).'_'.strtoupper($nombre_completo);
		$maxlimit= $limite;
		$comment= "";
		if( $target !="" ){
		    $API = new routeros_api();
		    $API->debug = false;
		    if ($API->connect($ServerList, $Username, $Pass, $Port)) {
		       $API->write("/queue/simple/getall",false);
		       $API->write('?name='.$name,true);
		       $READ = $API->read(false);
		       $ARRAY = $API->parse_response($READ); 
		        if(count($ARRAY)>0){ // si el nombre de usuario "ya existe" lo edito
					$API->write("/queue/simple/set",false);  
					$API->write("=.id=".$ARRAY[0]['.id'],false);
		            $API->write('=max-limit='.$maxlimit,true);   //   2M/2M   [TX/RX]			
					$READ = $API->read(false);
					$ARRAY = $API->parse_response($READ);
		            echo '<script >M.toast({html:"No se puede registrar dos IPs al mismo cliente.", classes: "rounded"})</script>';
		        }else{
		            $API->write("/queue/simple/add",false);
		            $API->write('=target='.$target,false);   // IP
		            $API->write('=name='.$name,false);       // nombre
		            $API->write('=max-limit='.$maxlimit,false);   //   2M/2M   [TX/RX]
		            $API->write('=comment='.$comment,true);         // comentario
		            $READ = $API->read(false);
		            $ARRAY = $API->parse_response($READ);  
		            $sql="UPDATE clientes SET ip='$IP', material='$Material', tecnico='$Tecnico', instalacion=1, fecha_instalacion='$FechaInstalacion', fecha_corte='$FechaInstalacion', hora_alta = '$Hora', coordenadas = '$Coordenada', referencia = '$Referencia', direccion = '$Direccion' WHERE id_cliente=$IdCliente";

		        	if(mysqli_query($conn, $sql)){    

			            $Descripcion = "Liquidación de Instalación";
						$Tipo_t = "Liquidacion";
		        		if(mysqli_num_rows(mysqli_query($conn, "SELECT * FROM pagos WHERE id_cliente = $IdCliente AND descripcion = '$Descripcion' AND cantidad='$Liquidar'"))>0){
							echo '<script>M.toast({html:"Ya se encuentra un pago registrado con los mismos valores.", classes: "rounded"})</script>';
						}else{
						if ($Liquidar != 0) {	
							$sql = "INSERT INTO pagos (id_cliente, descripcion, cantidad, fecha, tipo, id_user, corte, tipo_cambio) VALUES ($IdCliente, '$Descripcion', '$Liquidar', '$FechaInstalacion', '$Tipo_t', $id_user, 0, '$Tipo_Campio')";
							if ($Tipo_Campio == "Credito") {
							   $mysql = "INSERT INTO deudas(id_cliente, cantidad, fecha_deuda, tipo, descripcion, usuario) VALUES ($IdCliente, '$Liquidar', '$FechaInstalacion', '$Tipo', '$Descripcion', $id_user)";
							  mysqli_query($conn,$mysql);
							  $ultimo =  mysqli_fetch_array(mysqli_query($conn, "SELECT MAX(id_deuda) AS id FROM deudas WHERE id_cliente = $IdCliente"));            
							  $id_deuda = $ultimo['id'];
							  $sql = "INSERT INTO pagos (id_cliente, descripcion, cantidad, fecha, tipo, id_user, corte, tipo_cambio, id_deuda) VALUES ($IdCliente, '$Descripcion', '$Liquidar', '$FechaInstalacion', '$Tipo_t', $id_user, 0, '$Tipo_Campio', $id_deuda)";
							}
							if(mysqli_query($conn, $sql)){
								echo '<script>M.toast({html:"El pago se dió de alta satisfcatoriamente.", classes: "rounded"})</script>';
							}
						}
						}                     
			            echo '<script>M.toast({html:"Cliente registrado.", classes: "rounded"})</script>';
			            echo '<script>M.toast({html:"Favor de dar de alta en el servidor al cliente.", classes: "rounded"})</script>';
			            //echo '<script>function recargar() {
							 //   setTimeout("location.href="../views/instalaciones.php"", 1000);
							 // }</script>';
						echo '<script>recargar()</script>';
					}else{
						echo '<script>M.toast({html:"Ocurrio un error.", classes: "rounded"})</script>';
					}
		        }        
		        $API->disconnect();
		    }else{
		    	echo '<script >M.toast({html:"No se pudo hacer conexión al Mikrotik.", classes: "rounded"})</script>';
		    }//Conexion al mikro
		}	
	}
}else{
	echo '<script >M.toast({html:"Formato de IP incorrecto, por favor escriba una IP válida.", classes: "rounded"})</script>';
}

mysqli_close($conn);
?>