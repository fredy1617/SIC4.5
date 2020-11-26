<?php 
include('../php/conexion.php');
include('is_logged.php');
date_default_timezone_set('America/Mexico_City');
$Hora = date('H:i:s');
$id_user = $_SESSION['user_id'];
$id_cliente = $conn->real_escape_string($_POST['valorIdCliente']);
$mensaje = '';

$sql_chequeo = mysqli_query($conn, "SELECT * FROM tmp_pendientes WHERE id_cliente = $id_cliente");
$numero_columnas = mysqli_num_rows($sql_chequeo);
if($numero_columnas==0){
	$sql_select = mysqli_query($conn, "SELECT * FROM clientes WHERE id_cliente = $id_cliente");
	$cliente = mysqli_fetch_array($sql_select);
	$cliente_id = $cliente['id_cliente'];
	$cliente_nombre = $cliente['nombre'];
	$cliente_telefono = $cliente['telefono'];
	$cliente_lugar = $cliente['lugar'];
	$cliente_direccion = $cliente['direccion'];
	$cliente_referencia = $cliente['referencia'];
	$cliente_total = $cliente['total'];
	$cliente_dejo = $cliente['dejo'];
	$cliente_pagar = $cliente_total - $cliente_dejo;
	$cliente_paquete = $cliente['paquete'];
	$cliente_fecha =  $cliente['fecha_registro'];	

	$sql_insert = "INSERT INTO tmp_pendientes (id_cliente, nombre, telefono, lugar, direccion, referencia, total, dejo, pagar, paquete, fecha_registro, usuario, hora) VALUES ($cliente_id, '$cliente_nombre', '$cliente_telefono', '$cliente_lugar', '$cliente_direccion', '$cliente_referencia', $cliente_total, $cliente_dejo, $cliente_pagar, $cliente_paquete, '$cliente_fecha', '$id_user', '$Hora')";

	if(mysqli_query($conn, $sql_insert)){
		echo '<script>M.toast({html:"Se agrego a la ruta.", classes: "rounded"})</script>';	
	}else{
		$mensaje = '<script>M.toast({html:"Hubo un error al insertar en ruta.", classes: "rounded"})</script>';
	}
}else{
	$mensaje = '<script>M.toast({html:"Ya se encuentra esta instalación en ruta.", classes: "rounded"})</script>';
}

$sql_instalacion = mysqli_query($conn,"SELECT * FROM tmp_pendientes WHERE ruta_inst = 0 AND usuario = $id_user");
		while($instalacion = mysqli_fetch_array($sql_instalacion)){
			$id_comunidad = $instalacion['lugar'];
            $sql_comunidad = mysqli_fetch_array(mysqli_query($conn,"SELECT nombre FROM comunidades WHERE id_comunidad=$id_comunidad"));
            $id_cliente = $instalacion['id_cliente'];
            $serv = mysqli_fetch_array(mysqli_query($conn,"SELECT * FROM clientes WHERE id_cliente=$id_cliente"));

			$mensaje .= '
	        <tr>
	          <td>'.$instalacion['id_cliente'].'</td>
	          <td>'.$instalacion['nombre'].'</td>
	          <td>'.$serv['servicio'].'</td>
	          <td>'.$instalacion['telefono'].'</td>
	          <td>'.$sql_comunidad['nombre'].'</td>
	          <td>'.$instalacion['direccion'].'</td>
	          <td><a onclick="borrar_inst('.$instalacion['id_cliente'].');" class="btn btn-floating red darken-1 waves-effect waves-light"><i class="material-icons">delete</i></a></td>
	        </tr>';
		}

echo '<table class="bordered highlight responsive-table">
      <thead>
        <tr>
        	<th>No. Cliente</th>
            <th>Nombre</th>
            <th>Telefono</th>
            <th>Lugar</th>
            <th>Dirección</th>
            <th>Borrar</th>
        </tr>
      </thead>
      <tbody>';
      echo $mensaje;
echo  '</tbody>
    </table>';
mysqli_close($conn);
?>