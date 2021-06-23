<?php
#INCLUIMOS EL ARCHIVO CON LA CONEXION A LA BASE DE DATOS era (Buscar)
include('../php/conexion.php');

$codigo='1365';// RECIBIMOS EL ID DE LA RUTA POR GET

#CONSULTAMOS TODAS LAS INSTALACIONES QUE ALLA DE ESTA RUTA
$resultado = $conn->query("SELECT * FROM tmp_reportes WHERE ruta =$codigo");

#RECORREMOS CADA INTSLACION CON UN CICLO Y LO VACIAMOS EN UN ARRAY 
$arr = array();//CREAMOS UN ARRAY VACI PARA COLOCAR LA INFORAMCION NESESARIA (id_reporte, id_cliente, clientes.nombre, telefono, comunidades.nombre, referencia, coordenadas, reporte.descripcion, diagnostico, reporte.fecha)
while($listado=$resultado -> fetch_array()){
	$id_reporte = $listado['id_reporte'];                   
    if ((mysqli_num_rows(mysqli_query($conn, "SELECT * FROM reportes WHERE id_reporte = $id_reporte"))) == 0){
        $fila = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM orden_servicios WHERE id = $id_reporte")); 
        $id = $fila['id'];
        $Descripcion = $fila['solicitud'];  
        $Diagnostico = $fila['trabajo'];  
        $Es = 'Orden de Servicio';
    }else{
        $fila = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM reportes WHERE id_reporte = $id_reporte")); 
        $id = $fila['id_reporte'];
        $Descripcion = $fila['descripcion'];
        $Diagnostico = $fila['falla'];
        $Es = 'Reporte/Mantenimineto';
    }
	$id_cliente = $fila['id_cliente'];
    $sql = mysqli_query($conn, "SELECT * FROM clientes WHERE id_cliente=$id_cliente");
    $filas = mysqli_num_rows($sql);
    if ($filas == 0) {
        $sql = mysqli_query($conn, "SELECT * FROM especiales WHERE id_cliente=$id_cliente");
    }
    $cliente = mysqli_fetch_array($sql);
	//Buscar Comunidad
    $id_comunidad = $cliente['lugar'];
    $sql_comunidad = mysqli_fetch_array(mysqli_query($conn,"SELECT nombre FROM comunidades WHERE id_comunidad='$id_comunidad'"));

	#LLEMANMOS NUESRTRO ARRAY POR CADA REPORTE ENCONTRADO
	$arr['id_reporte'] =$id_reporte;
	$arr['es'] =$Es;
	$arr['id_cliente'] =$id_cliente;
	$arr['nombre'] =$cliente['nombre'];
	$arr['telefono'] =$cliente['telefono'];
	$arr['comunidad'] =$sql_comunidad['nombre'];
	$arr['referencia'] =$cliente['referencia'];
	$arr['coordenadas'] =$cliente['coordenadas'];
	$arr['descripcion'] =$Descripcion;
	$arr['diagnostico'] =$Diagnostico;
	$arr['fecha'] =$fila['fecha'];
    $producto[] = $arr;
}

echo json_encode($producto, JSON_UNESCAPED_UNICODE);
?>
