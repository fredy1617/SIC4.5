<?php
include 'conexion.php';
 $Texto = $conn->real_escape_string($_POST['texto']);
 $mensaje = '';

 $sql = "SELECT * FROM dispositivos WHERE estatus = 'Almacen' LIMIT 50";
 if ($Texto != "") {
 	$sql = "SELECT * FROM dispositivos WHERE (id_dispositivo  = '$Texto' OR nombre LIKE '%$Texto%') AND estatus = 'Almacen' LIMIT 25";
 }

$consulta = mysqli_query($conn, $sql);

$filas = mysqli_num_rows($consulta);

if ($filas == 0) {
	$mensaje = '<script>M.toast({html: "No se encontraron dispositivos.", classes: "rounded"})</script>';
}else{
	while ($resultados = mysqli_fetch_array($consulta)) {
		$id_dispositivo = $resultados['id_dispositivo'];
		$nombre = $resultados['nombre'];
		$telefono = $resultados['telefono'];
		$marca = $resultados['marca'];
		$color = $resultados['color'];
		$falla = $resultados['falla'];
		$cables = $resultados['cables'];
		$fecha = $resultados['fecha'];
		$observacion = $resultados['observaciones'];
		$id_tecnico = $resultados['tecnico'];
		if ($resultados['precio'] == 0) {
			$total = $resultados['mano_obra']+$resultados['t_refacciones'];
		}else{
			$total = $resultados['precio'];
		}
		if ($resultados['extras'] == NULL) {
			$extra = 'color '.$color.', con cable(s) de '.$cables;
		}else{
			$extra = $resultados['extras'];
		}

		if ($id_tecnico == '') {
			$tecnico[0] = 'Sin tecnico';
		}else{
			$tecnico = mysqli_fetch_array(mysqli_query($conn, "SELECT user_name, user_id FROM users WHERE user_id=$id_tecnico"));
		}
		//Output
			$mensaje .= '			
		          <tr>
		            <td>'.$id_dispositivo.'</td>
		            <td>'.$nombre.'</td>
		            <td>'.$telefono.'</td>
		            <td>'.$resultados['tipo'].' '.$marca.'</td>
		            <td>'.$extra.'</td>
		            <td>'.$falla.'</td>
		            <td>'.$observacion.'</td>
		            <td>'.$total.'</td>
		            <td>'.$fecha.'</td>
		            <td>'.$tecnico[0].'</td>
		            <td><form method="post" action="../php/Salida_SerTec.php" target="blank"><input id="id_dispositivo" name="id_dispositivo" type="hidden" value="'. $id_dispositivo.'"><button class="btn-floating btn-tiny waves-effect waves-light pink"><i class="material-icons">exit_to_app</i></button></form></td>
		          </tr>';
	}
}

echo $mensaje;
mysqli_close($conn);
?>