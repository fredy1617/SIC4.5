<?php 
include('../php/conexion.php');
date_default_timezone_set('America/Mexico_City');
$id_cliente = $conn->real_escape_string($_POST['valorIdCliente']);
$id_ruta = $conn->real_escape_string($_POST['valorRuta']);
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

	$sql_insert = "INSERT INTO tmp_pendientes (id_cliente, nombre, telefono, lugar, direccion, referencia, total, dejo, pagar, paquete, fecha_registro, ruta_inst) VALUES ($cliente_id, '$cliente_nombre', '$cliente_telefono', '$cliente_lugar', '$cliente_direccion', '$cliente_referencia', $cliente_total, $cliente_dejo, $cliente_pagar, $cliente_paquete, '$cliente_fecha',$id_ruta)";

	if(mysqli_query($conn, $sql_insert)){
		echo '<script>M.toast({html:"Se agrego a la ruta.", classes: "rounded"})</script>';	
	}
}else{
	$mensaje = '<script>M.toast({html:"Ya se encuentra esta instalación en ruta.", classes: "rounded"})</script>';
}
?>
<table class="bordered highlight responsive-table">
      <thead>
        <tr>
        	<th>No. Cliente</th>
            <th>Nombre</th>
            <th>Telefono</th>
            <th>Lugar</th>
            <th>Dirección</th>            
            <th>Fecha</th>
            <th>Hora</th>
			      <th>Estatus</th>
        </tr>
      </thead>
      <tbody>
    <?php
    $sql_tmp = mysqli_query($conn,"SELECT * FROM tmp_pendientes WHERE ruta_inst =$id_ruta");
        $columnas = mysqli_num_rows($sql_tmp);
        if($columnas == 0){
            ?>
            <h5 class="center">No hay instalaciones en ruta</h5>
            <?php
        }else{
            while($tmp = mysqli_fetch_array($sql_tmp)){
                $id_cliente = $tmp['id_cliente'];
                $cliente = mysqli_fetch_array(mysqli_query($conn,"SELECT * FROM clientes WHERE id_cliente =$id_cliente"));
            $instalacion =$cliente['instalacion'];
            $estatus = '<span class="new badge red" data-badge-caption="Pendiente"></span>';
            if ($instalacion == 1) {
                $estatus = '<span class="new badge green" data-badge-caption="Terminado"></span>';
            }
            $id_comunidad = $tmp['lugar'];
            $sql_comunidad1 = mysqli_fetch_array(mysqli_query($conn,"SELECT nombre FROM comunidades WHERE id_comunidad=$id_comunidad"));
            $id_cliente = $tmp['id_cliente'];
            $serv = mysqli_fetch_array(mysqli_query($conn,"SELECT * FROM clientes WHERE id_cliente=$id_cliente"));

            ?>
            <tr>
              <td><?php echo $tmp['id_cliente']; ?></td>
              <td><?php echo $tmp['nombre']; ?></td>
              <td><?php echo $serv['servicio']; ?></td>
              <td><?php echo  $tmp['telefono']; ?></td>
              <td><?php echo $sql_comunidad1['nombre']; ?></td>
              <td><?php echo $tmp['direccion']; ?></td>
              <td><?php echo $cliente['fecha_instalacion']; ?></td> 
              <td><?php echo $cliente['hora_alta']; ?></td> 
              <td><?php echo $estatus; ?></td>
            </tr>
        <?php
            }
            }
            mysqli_close($conn);
        ?>
      </tbody>
</table>
