<?php
include 'conexion.php';
 $Texto = $conn->real_escape_string($_POST['texto']);
 $mensaje = '';

 $sql = "SELECT * FROM dispositivos WHERE estatus = 'Almacen' AND fecha > '2019-01-01' ORDER BY fecha_salida LIMIT 50";
 if ($Texto != "") {
 	$sql = "SELECT * FROM dispositivos WHERE (id_dispositivo  = '$Texto' OR nombre LIKE '%$Texto%') AND estatus = 'Almacen' AND fecha > '2019-01-01' ORDER BY fecha_salida LIMIT 25";
 }

$consulta = mysqli_query($conn, $sql);

$filas = mysqli_num_rows($consulta);

if ($filas == 0) {
	$mensaje = '<script>M.toast({html: "No se encontraron dispositivos.", classes: "rounded"})</script>';
}else{
	while ($resultados = mysqli_fetch_array($consulta)) {
		$id_dispositivo = $resultados['id_dispositivo'];
		$nombre = $resultados['nombre'];
		$dispositivo = $resultados['tipo'].' '.$resultados['marca'];
		$falla = $resultados['falla'];
		$fecha = $resultados['fecha'];
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
		            <td>'.$nombre.'</td>
		            <td>'.$dispositivo.'</td>
		            <td>'.$falla.'</td>
		            <td>'.$observacion.'</td>
		            <td>'.$total.'</td>
		            <td>'.$resultados['fecha_salida'].'</td>
		            <td><form method="post" action="../views/salidas.php" target="blank"><input id="id_dispositivo" name="id_dispositivo" type="hidden" value="'. $id_dispositivo.'"><button class="btn-floating btn-tiny waves-effect waves-light pink"><i class="material-icons">exit_to_app</i></button></form></td>
		          </tr>';
	}
}

echo $mensaje;
mysqli_close($conn);
?>