<?php
#INCLUIMOS EL ARCHIVO CON LA CONEXION A LA BASE DE DATOS era (Buscar2)
include('../php/conexion.php');

$codigo=$_GET['codigo'];;// RECIBIMOS EL ID DE LA RUTA POR GET

#CONSULTAMOS TODAS LAS INSTALACIONES QUE ALLA DE ESTA RUTA
$resultado = $conn->query("SELECT * FROM tmp_pendientes INNER JOIN clientes ON tmp_pendientes.id_cliente = clientes.id_cliente WHERE ruta_inst =$codigo");

$arr = array();//CREAMOS UN ARRAY VACIO PARA COLOCAR LA INFORAMCION NECESARIA
#RECORREMOS CADA INTSLACION CON UN CICLO Y LO VACIAMOS EN UN ARRAY 
while($cliente=$resultado -> fetch_array()){	
    $id_comunidad = $cliente['lugar'];
    $sql_comunidad = mysqli_fetch_array(mysqli_query($conn,"SELECT nombre FROM comunidades WHERE id_comunidad='$id_comunidad'"));
    $id_paquete = $cliente['paquete'];
    $paquete = mysqli_fetch_array(mysqli_query($conn, "SELECT subida, bajada, mensualidad FROM paquetes WHERE id_paquete=$id_paquete"));
    $Apagar = $cliente['total']-$cliente['dejo'];
	#LLEMANMOS NUESRTRO ARRAY POR CADA REPORTE ENCONTRADO
	$arr['id_cliente'] =$cliente['id_cliente'];
	$arr['nombre'] =$cliente['nombre'];
	$arr['servicio'] =$cliente['servicio'];
	$arr['telefono'] =$cliente['telefono'];
	$arr['comunidad'] =$sql_comunidad['nombre'];
	$arr['referencia'] =$cliente['referencia'];
	$arr['total'] =$cliente['total'];
	$arr['dejo'] =$cliente['dejo'];
	$arr['Apagar'] =$Apagar;
	$arr['paquete'] ='(Subida/Bajada)'.$paquete['subida']."/".$paquete['bajada'];
	$arr['fecha'] =$cliente['fecha_registro'];

    $producto[] = $arr;
}

echo json_encode($producto, JSON_UNESCAPED_UNICODE);
?>
