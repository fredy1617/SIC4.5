<?php
	include('conexion.php');

	$Texto = $conn->real_escape_string($_POST['texto']);

	//Variable vacía (para evitar los E_NOTICE)
	$mensaje = "";

	$sql = "SELECT * FROM dispositivos LIMIT 50";

	if ($Texto != "") {
		$sql="SELECT * FROM dispositivos WHERE (nombre LIKE '%$Texto%' OR  id_dispositivo = '$Texto') LIMIT 25";
	}
	$consulta = mysqli_query($conn, $sql);
	//Obtiene la cantidad de filas que hay en la consulta
	$filas = mysqli_num_rows($consulta);

	//Si no existe ninguna fila que sea igual a $consultaBusqueda, entonces mostramos el siguiente mensaje
	if ($filas == 0) {
		$mensaje = "<script>M.toast({html: 'No se encontraron dispositivos.', classes: 'rounded'})</script>";
	} else {
		//La variable $resultado contiene el array que se genera en la consulta, así que obtenemos los datos y los mostramos en un bucle
		while($resultados = mysqli_fetch_array($consulta)) {

			$id_dispositivo = $resultados['id_dispositivo'];
			if ($resultados['precio'] == 0) {
				$total = $resultados['mano_obra']+$resultados['t_refacciones'];
			}else{
				$total = $resultados['precio'];
			}
			//Output
			$mensaje .= '			
		          <tr>
		            <td>'.$id_dispositivo.'</td>
		            <td>'.$resultados['nombre'].'</td>
		            <td>'.$resultados['telefono'].'</td>
		            <td>'.$resultados['tipo'].' '.$resultados['marca'].'</td>
		            <td><b class = "blue-text">'.$resultados['estatus'].'</b></td> 
		            <td>'.$resultados['falla'].'</td>
		            <td>'.$resultados['observaciones'].'</td>
		            <td>'.$total.'</td>
		            <td>'.$resultados['fecha'].'</td>
		            <td>'.$resultados['fecha_salida'].'</td>
		            <td><a onclick="regresa('.$id_dispositivo.');" class="btn btn-floating red darken-1 waves-effect waves-light"><i class="material-icons">reply</i></a></td>        
		          </tr>';

		}//Fin while $resultados
	} //Fin else $filas

//Devolvemos el mensaje que tomará jQuery
echo $mensaje;
mysqli_close($conn);
?>