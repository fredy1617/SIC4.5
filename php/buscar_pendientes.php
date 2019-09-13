<?php 
	include 'conexion.php';
	date_default_timezone_set('America/Mexico_City');
  	$Hoy = date('Y-m-d');

	$Texto = $conn->real_escape_string($_POST['texto']);

	$mensaje = '';

	if ($Texto != ""){
		$sql = "SELECT * FROM dispositivos WHERE (id_dispositivo  = '$Texto' OR nombre LIKE '%$Texto%') AND estatus IN ('Cotizado','En Proceso','Pendiente') AND fecha > '2019-02-01' ORDER BY fecha LIMIT 35";

	}else{		
		$sql = "SELECT * FROM dispositivos WHERE estatus IN ('Cotizado','En Proceso','Pendiente') AND fecha > '2019-02-01' ORDER BY fecha";
	}

	$consulta = mysqli_query($conn, $sql);
	//Obtiene la cantidad de filas que hay en la consulta
	$filas = mysqli_num_rows($consulta);

	//Si no existe ninguna fila que sea igual a $consultaBusqueda, entonces mostramos el siguiente mensaje
	if ($filas == 0) {
		echo '<script>M.toast({html:"No se encontraron dispositivos.", classes: "rounded"})</script>';
	} else {
		//La variable $resultado contiene el array que se genera en la consulta, así que obtenemos los datos y los mostramos en un bucle
		while($resultados = mysqli_fetch_array($consulta)) {
		  $id_dispositivo = $resultados['id_dispositivo'];
	      $nombre = $resultados['nombre'];
	      $disp = $resultados['tipo'].' '.$resultados['marca'];
	      if ($resultados['extras'] == NULL) {
	      	$extra = 'Color '. $color = $resultados['color'].', con '.$cables = $resultados['cables'];
	      }else{
	      	$extra = $resultados['extras'];
	      }
	      $falla = $resultados['falla'];	      
	      $estatus = $resultados['estatus'];
	      if($resultados['tecnico']==0){
	        $tecnico1[0] = 'Sin tecnico';
	      }else{
	        $id_tecnico = $resultados['tecnico'];
	        $tecnico1 = mysqli_fetch_array(mysqli_query($conn, "SELECT user_name FROM users WHERE user_id=$id_tecnico"));  
	      }
	        if ($estatus == 'En Proceso') {
	        	$color = 'green lighten-1';
	        }elseif ($estatus == 'Cotizado') {
	        	$color = 'orange lighten-1';
	        }else{
	        	$color = 'red lighten-1';
	        }
	        $estatus =str_replace(" ", "", $estatus);
	        $Diferencia = 0;
	        if ($resultados['fecha']< $Hoy) {
	        	$date1 = new DateTime($Hoy);
	        	$date2 = new DateTime($resultados['fecha']);
	        	$diff = $date1->diff($date2);
	        	$Diferencia = $diff->days;
	        }
	        if ($Diferencia >1 AND $Diferencia<4) {
	        	$color2 = "yellow darken-2";
	        }elseif ($Diferencia > 3 AND $Diferencia < 6) {
	        	$color2 = "orange darken-2";
	        }elseif ($Diferencia >= 6) {
	        	$color2 = "red accent-4";
	        }else{
	        	$color2 = "green";
	        }

			//Output
			$mensaje .= '
			
		          <tr>


		            <td><span class="new badge '.$color2.'" data-badge-caption="">'.$Diferencia.'</span></td>
		            <td>'.$id_dispositivo.'</td>
		            <td><b>'.$nombre.'</b></td>
		            <td>'.$disp.'</td>
		            <td>'.$extra.'</td>
		            <td>'.$falla.'</td>
		            <td><span class="new badge '.$color.'" data-badge-caption="'.$estatus.'"></span></td>
		            <td>'.$tecnico1[0].'</td>
		            <td><form method="post" action="../views/atender_dispositivo.php"><input id="id_dispositivo" name="id_dispositivo" type="hidden" value="'. $id_dispositivo.'"><button class="btn-floating btn-tiny waves-effect waves-light pink"><i class="material-icons">send</i></button></form></td>
		          </tr>';
		        
		}//Fin while $resultados
	} //Fin else $filas

echo $mensaje;
mysqli_close($conn);
?>