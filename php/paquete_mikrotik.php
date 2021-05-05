<?php 
include('../php/conexion.php');
include_once('../API/api_mt_include2.php');
include('is_logged.php');
date_default_timezone_set('America/Mexico_City');
$FechaAtendido = date('Y-m-d');
$Hora = date('H:i:s');

$IdCliente = $conn->real_escape_string($_POST['valorCliente']);
$Paquete = $conn->real_escape_string($_POST ['valorPaquete']);

if (mysqli_query($conn, "UPDATE clientes SET paquete = '$Paquete' WHERE id_cliente=$IdCliente ")) {
  	echo  '<script>M.toast({html:"Información actualizada.", classes: "rounded"})</script>';
 	echo '<h3>Editar Paquete </h3>';
  	$paquete_cliente = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM paquetes WHERE id_paquete='$Paquete'"));
  	?>
  	<div class="col s1"><br></div>
  	<div class="col s12 m6 l6">
      <label><i class="material-icons">import_export</i>Paquete:</label>
      <div class="input-field ">
        <select id="paquete" class="browser-default" required>
            <option value="<?php echo $paquete_cliente['id_paquete'];?>" selected>$<?php echo $paquete_cliente['mensualidad'];?> Velocidad: <?php echo $paquete_cliente['bajada'].'/'.$paquete_cliente['subida'];?></option>
            <?php
            $sql = mysqli_query($conn,"SELECT * FROM paquetes");
            while($paquete = mysqli_fetch_array($sql)){
            ?>
                <option value="<?php echo $paquete['id_paquete'];?>">$<?php echo $paquete['mensualidad'];?> Velocidad: <?php echo $paquete['bajada'].'/'.$paquete['subida'];?></option>
            <?php
            } 
            ?>
        </select>
   	 </div>
 	</div><br><br>
  	<a onclick="update_paquete();" class="waves-effect waves-light btn pink"><i class="material-icons right">send</i>EDITAR</a>
<?php
	//Buscamos el servidor
	$cliente = mysqli_fetch_array(mysqli_query($conn, "SELECT nombre, lugar, ip FROM clientes WHERE id_cliente=$IdCliente"));
	$lugar = $cliente['lugar'];
	$comunidad = mysqli_fetch_array(mysqli_query($conn, "SELECT servidor, nombre FROM comunidades WHERE id_comunidad=$lugar"));
	$id_servidor = $comunidad['servidor'];
	$servidor = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM servidores WHERE id_servidor=$id_servidor"));

	//////// configura tus servidor
	$ServerList = $servidor['ip']; //ip_de_tu_API
	$Username = $servidor['user']; //usuario_API
	$Pass = $servidor['pass']; //contraseña_API
	$Port = $servidor['port']; //puerto_API
		/// VARIABLES DE FORMULARIO
	$target= $cliente['ip'];  // IP Cliente		
	$maxlimit= $paquete_cliente['subida']."/".$paquete_cliente['bajada']; // PAQUETE VELOCIDAD 
	$name= '#'.$IdCliente.'_'.strtoupper($comunidad['nombre']).'_'.strtoupper($cliente['nombre']);//NOMBRE EN MIKROTIK

	$API = new routeros_api();
	$API->debug = false;
	if ($API->connect($ServerList, $Username, $Pass, $Port)) {
		$API->write("/queue/simple/getall",false);
      	$API->write('?name='.$name,true);
        $READ = $API->read(false);
        $ARRAY = $API->parse_response($READ);
		if(count($ARRAY)>0){ // si el nombre de usuario "ya existe" lo edito
		    //CODIGO PARA EDITAR EL QUEUE
			$API->write("/queue/simple/set",false);  
			$API->write("=.id=".$ARRAY[0]['.id'],false);
		    $API->write('=max-limit='.$maxlimit,true);   //   2M/2M   [TX/RX]			
			$READ = $API->read(false);
			$ARRAY = $API->parse_response($READ);
		    echo '<script >M.toast({html:"MIKROTIK MODIFICADO...", classes: "rounded"})</script>';
		}elseif (mysqli_num_rows(mysqli_query($conn, "SELECT * FROM tmp_mikrotik WHERE ip = '$target' AND servidor = '$id_servidor'"))>0) {
			$queue = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM tmp_mikrotik WHERE ip = '$target' AND servidor = '$id_servidor'"));
			$id = $queue['id'];
			//CODIGO PARA EDITAR EL QUEUE
			$API->write("/queue/simple/set",false);  
			$API->write("=.id=".$id,false);
		    $API->write('=max-limit='.$maxlimit,true);   //   2M/2M   [TX/RX]			
			$READ = $API->read(false);
			$ARRAY = $API->parse_response($READ);
		    echo '<script >M.toast({html:"MIKROTIK MODIFICADO...", classes: "rounded"})</script>';
		}else{
		    echo '<script>M.toast({html:">>> ERROR DE MIKROTIK NO MODIFICADO <<<", classes: "rounded"})</script>';
		    echo '<script>M.toast({html:">>> QUEUE NO ENCONTRADO <<<", classes: "rounded"})</script>';
		    echo '<script>M.toast({html:">>> MODIFICAR DIRECTO EN MIKROTIK <<<", classes: "rounded"})</script>';
		}
	}
}
?>