<?php
include 'conexion.php';

$consultaBusqueda = $conn->real_escape_string($_POST['texto']);
error_reporting(0);

//Variable vacía (para evitar los E_NOTICE)
$mensaje = "";

//Comprueba si $consultaBusqueda está seteado
if (isset($consultaBusqueda)) {
	
	//Selecciona todo de la tabla mmv001 
	//donde el nombre sea igual a $consultaBusqueda, 
	//o el apellido sea igual a $consultaBusqueda, 
	//o $consultaBusqueda sea igual a nombre + (espacio) + apellido
	$sql = "SELECT * FROM clientes WHERE instalacion is NOT NULL AND nombre LIKE '%".$consultaBusqueda."%' OR id_cliente = '".$consultaBusqueda."' LIMIT 20";
	$consulta = mysqli_query($conn, $sql);
	//Obtiene la cantidad de filas que hay en la consulta
	$filas = mysqli_num_rows($consulta);

	//Si no existe ninguna fila que sea igual a $consultaBusqueda, entonces mostramos el siguiente mensaje
	if ($filas == 0) {
		$mensaje = '<script>M.toast({html:"No se encontraron clientes.", classes: "rounded"})</script>';
	} else {

		//La variable $resultado contiene el array que se genera en la consulta, así que obtenemos los datos y los mostramos en un bucle	
		while($resultados = mysqli_fetch_array($consulta)) {
			$id_comunidad = $resultados['lugar'];
            $sql_comunidad = mysqli_fetch_array(mysqli_query($conn,"SELECT nombre FROM comunidades WHERE id_comunidad=$id_comunidad"));

			$no_cliente = $resultados['id_cliente'];
			$nombre = $resultados['nombre'];
			$servicio = $resultados['servicio'];
			$lugar = $sql_comunidad['nombre'];
			$telefono = $resultados['telefono'];
			$ip = $resultados['ip'];

			//Output
			$mensaje .= '			
		          <tr>
		            <td>'.$no_cliente.'</td>
		            <td>'.$nombre.'</td>
		            <td>'.$servicio.'</td>
		            <td>'.$lugar.'</td>
		            <td>'.$telefono.'</td>
		            <td>'.$ip.'</td>
		            <td><form method="post" action="../views/editar_cliente.php"><input id="no_cliente" name="no_cliente" type="hidden" value="'.$no_cliente.'"><button class="btn-floating btn-tiny waves-effect waves-light pink"><i class="material-icons">edit</i></button></form></td>
		          </tr>';     

		}//Fin while $resultados
	} //Fin else $filas

}//Fin isset $consultaBusqueda

//Devolvemos el mensaje que tomará jQuery
echo $mensaje;

mysqli_close($conn);
?>