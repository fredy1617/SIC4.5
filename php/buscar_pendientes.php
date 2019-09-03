<?php 
	include 'conexion.php';

	$Texto = $conn->real_escape_string($_POST['texto']);
	//Filtro anti-XSS
	$caracteres_malos = array("<", ">", "\"", "'", "/", "<", ">", "'", "/");
	$caracteres_buenos = array("& lt;", "& gt;", "& quot;", "& #x27;", "& #x2F;", "& #060;", "& #062;", "& #039;", "& #047;");

	$Texto = str_replace($caracteres_malos, $caracteres_buenos, $Texto);

	$mensaje = '';
	$sql = "SELECT * FROM dispositivos WHERE estatus IN ('Cotizado','En Proceso','Pendiente') AND fecha > '2019-01-01'";

	if ($Texto != ""){
	$sql = "SELECT * FROM dispositivos WHERE (id_dispositivo  = '$Texto' OR nombre LIKE '%$Texto%') AND estatus IN ('Cotizado','En Proceso','Pendiente') AND fecha > '2019-01-01' LIMIT 20";

	}

	$consulta = mysqli_query($conn, $sql);
	//Obtiene la cantidad de filas que hay en la consulta
	$filas = mysqli_num_rows($consulta);

	//Si no existe ninguna fila que sea igual a $consultaBusqueda, entonces mostramos el siguiente mensaje
	if ($filas == 0) {
		$mensaje = '<script type="text/javascript">M.toast({html:"No se encontraron dispositivos.", classes: "rounded"})</script>';
	} else {
		//La variable $resultado contiene el array que se genera en la consulta, así que obtenemos los datos y los mostramos en un bucle
		while($resultados = mysqli_fetch_array($consulta)) {
		  $id_dispositivo = $resultados['id_dispositivo'];
	      $nombre = $resultados['nombre'];
	      $telefono = $resultados['telefono'];
	      $disp = $resultados['tipo'].' '.$resultados['marca'];

	      if ($resultados['extras'] == NULL) {
	      	$extra = 'Color '. $color = $resultados['color'].', con '.$cables = $resultados['cables'];
	      }else{
	      	$extra = $resultados['extras'];
	      }
	      $falla = $resultados['falla'];	      
	      $fecha = $resultados['fecha'];
	      $id_tecnico = $resultados['tecnico'];

	      if($id_tecnico==''){
	          $tecnico[0] = 'Sin tecnico';
	        }else{
	          $tecnico = mysqli_fetch_array(mysqli_query($conn, "SELECT user_name, user_id FROM users WHERE user_id=$id_tecnico")); 
	        }

			//Output
			$mensaje .= '
			
		          <tr>
		            <td>'.$id_dispositivo.'</td>
		            <td><b>'.$nombre.'</b></td>
		            <td>'.$telefono.'</td>
		            <td>'.$disp.'</td>
		            <td>'.$extra.'</td>
		            <td>'.$falla.'</td>
		            <td>'.$fecha.'</td>
		            <td>'.$tecnico[0].'</td>
		            <td><form method="post" action="../views/salidas.php"><input id="id_dispositivo" name="id_dispositivo" type="hidden" value="'. $id_dispositivo.'"><button class="btn-floating btn-tiny waves-effect waves-light pink"><i class="material-icons">exit_to_app</i></button></form></td>
		          </tr>';
		        
		}//Fin while $resultados
	} //Fin else $filas

echo $mensaje;
mysqli_close($conn);
?>