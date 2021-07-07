<?php
date_default_timezone_set('America/Mexico_City');
include('../php/conexion.php');
include_once('../API/api_mt_include2.php');
include('is_logged.php');

$IP = $_POST['valorIP'];
$filtrarIdCliente = $_POST['valorIdCliente'];
$filtrarTecnico = $_POST['valorTecnicos'];

//Filtro anti-XSS
$caracteres_malos = array("<", ">", "\"", "'", "/", "<", ">", "'", "/");
$caracteres_buenos = array("& lt;", "& gt;", "& quot;", "& #x27;", "& #x2F;", "& #060;", "& #062;", "& #039;", "& #047;");

$id_user = $_SESSION['user_id'];

$IdCliente = str_replace($caracteres_malos, $caracteres_buenos, $filtrarIdCliente);
$Tecnico = str_replace($caracteres_malos, $caracteres_buenos, $filtrarTecnico);
$Liquidar = $conn->real_escape_string($_POST['valorLiquidar']);
$Tipo_Campio = $conn->real_escape_string($_POST['valorTipo_Cambio']);
$Direccion = $conn->real_escape_string($_POST['valorDireccion']);
$Referencia = $conn->real_escape_string($_POST['valorReferencia']);
$Coordenada = $conn->real_escape_string($_POST['valorCoordenada']);
$Extencion = $conn->real_escape_string($_POST['valorExtencion']);
$FechaInstalacion = date('Y-m-d');
$Hora = date('H:i:s');


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
		        	//COFIGO PARA EDITAR EL QUEUE
					#$API->write("/queue/simple/set",false);  
					#$API->write("=.id=".$ARRAY[0]['.id'],false);
		            #$API->write('=max-limit='.$maxlimit,true);   //   2M/2M   [TX/RX]			
					#$READ = $API->read(false);
					#$ARRAY = $API->parse_response($READ);
		            echo '<script >M.toast({html:"No se puede registrar dos Queues al mismo cliente.", classes: "rounded"})</script>';
		        }else{
		            $API->write("/queue/simple/add",false);
		            $API->write('=target='.$target,false);   // IP
		            $API->write('=name='.$name,false);       // nombre
		            $API->write('=max-limit='.$maxlimit,false);   //   2M/2M   [TX/RX]
		            $API->write('=comment='.$comment,true);         // comentario
		            $READ = $API->read(false);
		            $ARRAY = $API->parse_response($READ);  
		            $sql="UPDATE clientes SET ip='$IP', material='$Material', tecnico='$Tecnico', instalacion=1, fecha_instalacion='$FechaInstalacion', fecha_corte='$FechaInstalacion', hora_alta = '$Hora', coordenadas = '$Coordenada', referencia = '$Referencia', direccion = '$Direccion', tel_servicio = '$Extencion' WHERE id_cliente=$IdCliente";
		            
		        	if(mysqli_query($conn,$sql)){
						#--- MATERIALES ---
						$Antena = $conn->real_escape_string($_POST['valorAntena']);
						$Router = $conn->real_escape_string($_POST['valorRouter']);
						$Cable = $conn->real_escape_string($_POST['valorCable']);
						$Tubos = $conn->real_escape_string($_POST['valorTubos']);
						$Bobina = '';
						$Extras = $conn->real_escape_string($_POST['valorExtras']);
		        		$Descripcion = "Liquidación de Instalación";
						$Tipo_t = "Liquidacion";
		        		if(mysqli_num_rows(mysqli_query($conn, "SELECT * FROM pagos WHERE id_cliente = '$IdCliente' AND descripcion = '$Descripcion' AND cantidad='$Liquidar'"))>0){
							echo '<script>M.toast({html:"Ya se encuentra un pago registrado con los mismos valores.", classes: "rounded"})</script>';
						}else{
						if ($Liquidar != 0) {	
							$sql = "INSERT INTO pagos (id_cliente, descripcion, cantidad, fecha, hora, tipo, id_user, corte, corteP, tipo_cambio) VALUES ('$IdCliente', '$Descripcion', '$Liquidar', '$FechaInstalacion', '$Hora', '$Tipo_t', '$id_user', 0, 0, '$Tipo_Campio')";
							if ($Tipo_Campio == "Credito") {
							   $mysql = "INSERT INTO deudas(id_cliente, cantidad, fecha_deuda, tipo, descripcion, usuario) VALUES ('$IdCliente', '$Liquidar', '$FechaInstalacion', '$Tipo', '$Descripcion', '$id_user')";
							  mysqli_query($conn,$mysql);
							  $ultimo =  mysqli_fetch_array(mysqli_query($conn, "SELECT MAX(id_deuda) AS id FROM deudas WHERE id_cliente = '$IdCliente'"));            
							  $id_deuda = $ultimo['id'];
							  $sql = "INSERT INTO pagos (id_cliente, descripcion, cantidad, fecha, hora, tipo, id_user, corte, corteP, tipo_cambio, id_deuda) VALUES ('$IdCliente', '$Descripcion', '$Liquidar', '$FechaInstalacion', '$Hora', '$Tipo_t', '$id_user', 0, 0, '$Tipo_Campio', $id_deuda)";
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
							}
						}
						}     
						$Sql_Mat = "INSERT INTO materiales(id_cliente, antena, router, cable, tubos, extras, bobina, fecha, usuarios, tipo, es) VALUES('$IdCliente','$Antena', '$Router', '$Cable', '$Tubos', '$Extras', '$Bobina', '$FechaInstalacion', '$Tecnico', 'Nuevo', 'Instalacion')";
						if(mysqli_query($conn, $Sql_Mat)){
							$Mas = 'y Material';	
						}else{
							echo '<script>M.toast({html:"Ocurrio un error Material.", classes: "rounded"})</script>';
						}
						$IdTecnico = $_SESSION['user_id'];

						if ($Antena != '') {
							$sqlA = "UPDATE stock_tecnicos SET uso = 1, disponible = 1, fecha_salida = '$FechaInstalacion' WHERE serie = '$Antena' AND disponible = 0 AND tipo = 'Antena' AND tecnico = $IdTecnico";
							if(mysqli_query($conn, $sqlA)){
								echo '<script>M.toast({html:"Se dio de baja la antena: "'.$Antena.', classes: "rounded"})</script>';
							}
						}
						if ($Router != '') {
							$sqlR = "UPDATE stock_tecnicos SET uso = 1, disponible = 1, fecha_salida = '$FechaInstalacion' WHERE serie = '$Router' AND disponible = 0 AND tipo = 'Router' AND tecnico = $IdTecnico";
							if(mysqli_query($conn, $sqlR)){
								echo '<script>M.toast({html:"Se dio de baja el router: "'.$Router.', classes: "rounded"})</script>';
							}
						}
						if ($Cable > 0) {
							$bobina = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM stock_tecnicos WHERE uso < 300 AND tecnico = $IdTecnico AND disponible = 0 AND tipo = 'Bobina'"));
							$Total =$bobina['uso']+$Cable;
							if ($Total >= 300) {
								if (mysqli_query($conn, "UPDATE stock_tecnicos SET uso = 300, fecha_salida = '$FechaInstalacion', disponible = 1  WHERE uso < 300 AND tecnico = $IdTecnico AND disponible = 0 AND tipo = 'Bobina'")){
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
							      	if (mysqli_query($conn, "UPDATE stock_tecnicos SET uso = $Cantidad, fecha_salida = '$FechaInstalacion', disponible = 1 WHERE id = $id")) {
								        echo '<script>M.toast({html:"Tubos actualizados...", classes: "rounded"})</script>';
								    }
							        $Entra = True;  
							        $Tubos = $Uso-$Cantidad;
							      }else if ($Resta == 0) {
							      	if (mysqli_query($conn, "UPDATE stock_tecnicos SET uso = $Cantidad, fecha_salida = '$FechaInstalacion', disponible = 1 WHERE id = $id")) {
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

			            echo '<script>M.toast({html:"Cliente '.$Mas.' registrado.", classes: "rounded"})</script>';
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