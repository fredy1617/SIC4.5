<?php 
	include 'conexion.php';

	$Texto = $conn->real_escape_string($_POST['texto']);

	$mensaje = '';

	$sql = "SELECT * FROM dispositivos WHERE estatus IN ('Listo (En Taller)','Listo (No Reparado)', 'Listo') AND fecha > '2019-01-01' ORDER BY fecha_salida Limit 50";

	if ($Texto != ""){
		$sql = "SELECT * FROM dispositivos WHERE (id_dispositivo  = '$Texto' OR nombre LIKE '%$Texto%')  AND estatus IN ('Listo (En Taller)','Listo (No Reparado)', 'Listo') AND fecha > '2019-01-01' ORDER BY fecha_salida Limit 25";
	}

	$consulta = mysqli_query($conn, $sql);
	//Obtiene la cantidad de filas que hay en la consulta
	$filas = mysqli_num_rows($consulta);

	//Si no existe ninguna fila que sea igual a $consultaBusqueda, entonces mostramos el siguiente mensaje
	if ($filas == 0) {
		$mensaje = '<script>M.toast({html:"No se encontraron dispositivos.", classes: "rounded"})</script>';
	} else {
		//La variable $resultado contiene el array que se genera en la consulta, así que obtenemos los datos y los mostramos en un bucle
		while($resultados = mysqli_fetch_array($consulta)) {
		  $id_dispositivo = $resultados['id_dispositivo'];
          $listo = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM actividades_taller WHERE dispositivo = '$id_dispositivo' AND (accion = 'Listo' OR accion = 'Listo (No Reparado)')"));
          if ($listo == '') {
          	$listo['fecha'] = 'NULL';
          }
	      $dispositivo = $resultados['tipo'].' '.$resultados['marca'];
	      $observacion = $resultados['observaciones'];
	      if ($resultados['precio'] == 0) {
	      	$total = $resultados['mano_obra']+$resultados['t_refacciones'];
	      }else{
		  	$total = $resultados['precio'];
		  }
			//Output
			$mensaje .= '
			
		          <tr>
		            <td>'.$id_dispositivo.'</td>
		            <td><b>'.$resultados['nombre'].'</b></td>
		            <td>'.$dispositivo.'</td>	            
		            <td>'.$resultados['falla'].'</td>
		            <td>'.$observacion.'</td>
		            <td>$'.$total.'</td>
		            <td>'.$listo['fecha'].'</td>
		            <td><form method="post" action="../views/salidas.php"><input id="id_dispositivo" name="id_dispositivo" type="hidden" value="'. $id_dispositivo.'"><button class="btn-floating btn-tiny waves-effect waves-light pink"><i class="material-icons">exit_to_app</i></button></form></td>
		             <td><a onclick="almacen('.$id_dispositivo.');" class="btn btn-floating pink  waves-effect waves-light"><i class="material-icons">dashboard</i></a></td>
		          </tr>';
		        
		}//Fin while $resultados
	} //Fin else $filas

echo $mensaje;
mysqli_close($conn);
?>