<?php 
	include ('conexion.php');
	$Tipo = $conn->real_escape_string($_POST['valorTipo']);//RECIBIMOS EL VALOR  0 o 1 SEGUN EL TIPO DE BUSQUEDA DE reporte_pedidos_entregados.php
	$mensaje = '';//CREAMOS UN STR VACIO ES DONDE IREMOS GUARDANDO LA INFORMACION QUE SE RETORNARA A LA VISTA
	if ($Tipo == 1) {
		#BUSQUEDA POR TEXTO ESCRITO
		$Texto =  $conn->real_escape_string($_POST['texto']);// RECIBIMOS LA VARIABLE CON LO ESCRITO EN El INPUT DE reportes_pedidos.....
		if ($Texto != "") {
			$sql = "SELECT * FROM pedidos WHERE ( nombre LIKE '%$Texto%'  OR folio = '$Texto' OR id_orden = '$Texto') AND estatus = 'Entregado'LIMIT 10";
		}else{
			$sql = "SELECT * FROM pedidos WHERE estatus = 'Entregado' LIMIT 10";
		}
	}else{
		$ValorDe =  $conn->real_escape_string($_POST['valorDe']);// RECIBIMOS LA VARIABLE CON LA FECHA 'DE' DE reportes_pedidos.....
		$ValorA =  $conn->real_escape_string($_POST['valorA']);// RECIBIMOS LA VARIABLE CON LA FECHA 'A' DE reportes_pedidos.....
		#BUSQUEDA POR RANGO DE FECHA DE ENTREGADOS (COMPLETO)
		$sql = "SELECT * FROM pedidos WHERE (fecha_completo >= '$ValorDe' AND fecha_completo <= '$ValorA') AND estatus = 'Entregado' LIMIT 10";
	}

	$consulta = mysqli_query($conn, $sql);
	//Obtiene la cantidad de filas que hay en la consulta
	$filas = mysqli_num_rows($consulta);
	if ($filas == 0) {
		echo '<script>M.toast({html:"No se encontraron Pedidos.", classes: "rounded"})</script>';		
	}else{
		$mensaje.= '
		<table class="bordered highlight responsive-table">
		  	<thead>
				<tr>
					<th>Folio</th>
					<th>Nombre</th>
					<th>Orden</th>
					<th>Fecha Y Hora Creación</th>
					<th>Fecha Cerrado</th>
					<th>Fecha Autorizado</th>
					<th>Fecha Surtido</th>
					<th>Registro</th>
					<th>Detalles</th>
				</tr>
		  	</thead>
		  	<tbody>';	
		//La variable $resultado contiene el array que se genera en la consulta, así que obtenemos los datos y los mostramos en un bucle		
		while($resultados = mysqli_fetch_array($consulta)) {
            $usuario = $resultados['usuario'];
            $datos = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM users WHERE user_id = $usuario"));
			//Output
			$mensaje .= '			
			    <tr>
			        <td><b>'.$resultados['folio'].'</b></td>
			        <td><b>'.$resultados['nombre'].'</b></td>
			        <td><b>'.$resultados['id_orden'].'</b></td>
			        <td><b>'.$resultados['fecha'].' '.$resultados['hora'].'</b></td>
			        <td><b>'.$resultados['fecha_cerrado'].'</b></td>
			        <td><b>'.$resultados['fecha_autorizado'].'</b></td>
			        <td><b>'.$resultados['fecha_completo'].'</b></td>
			        <td><b>'.$datos['firstname'].'</b></td>
			        <td><a href = "../views/detalles_pedido.php?folio='.$resultados['folio'].'" class="btn-floating btn-tiny waves-effect waves-light pink"><i class="material-icons">visibility</i></a></td>
			    </tr>';     
		}//Fin while $resultados
	} //Fin else $filas

echo $mensaje;
mysqli_close($conn);
?>


