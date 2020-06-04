<?php 
	include ('conexion.php');
	$Texto = $conn->real_escape_string($_POST['texto']);
	$mensaje = '';
	if ($Texto != "") {
		//AQUI BUSCARA SI ES POR NOMBRE O POR ID DE CLIENTE
		$sql = "SELECT * FROM especiales WHERE ( nombre LIKE '%$Texto%' OR id_cliente = '$Texto') LIMIT 1";
	}else{
		//ESTA CONSULTA SE HARA SIEMPRE QUE NO ALLA NADA EN EL BUSCADOR...
		$sql = "SELECT * FROM especiales WHERE id_cliente < 10000  LIMIT 1";
	}

	$consulta = mysqli_query($conn, $sql);
	//Obtiene la cantidad de filas que hay en la consulta
	$filas = mysqli_num_rows($consulta);

	if ($filas == 0) {
			$mensaje = '<br><br>';
	} else {
		$mensaje .='
				<table class="bordered highlight centered responsive-table">
				    <thead>
				      <tr>
				      	<th>No. Cliente</th>
				        <th>Nombre</th>
				        <th>Telefono</th>
				        <th>Comunidad</th>
				        <th>Referencia</th>
				        <th>Elegir</th>
				      </tr>
				    </thead>';
		//La variable $resultado contiene el array que se genera en la consulta, así que obtenemos los datos y los mostramos en un bucle		
		while($resultados = mysqli_fetch_array($consulta)) {
			$id_comunidad = $resultados['lugar'];
			$sql_comunidad = mysqli_fetch_array(mysqli_query($conn,"SELECT nombre FROM comunidades WHERE id_comunidad = $id_comunidad"));
			$no_cliente = $resultados['id_cliente'];
			$nombre = $resultados['nombre'];
			$lugar = $sql_comunidad['nombre'];
			$telefono = $resultados['telefono'];
			$referencia = $resultados['referencia'];

			//Output
			$mensaje .= '
					<tbody>				
			          <tr>
			            <td>'.$no_cliente.'</td>
			            <td><b>'.$nombre.'</b></td>
			            <td><b>'.$telefono.'</b></td>
			            <td>'.$lugar.'</td>
			            <td>'.$referencia.'</td>
			            <td><form method="post" action="../views/form_orden_R.php"><input id="no_cliente" name="no_cliente" type="hidden" value="'.$no_cliente.'"><button class="btn-floating btn-tiny waves-effect waves-light pink"><i class="material-icons">check</i></button></form></td>
		          	  </tr>
		          	</tbody>';     

		}//Fin while $resultados
	} //Fin else $filas
			$mensaje.= '
				</table>';

echo $mensaje;
mysqli_close($conn);
?>