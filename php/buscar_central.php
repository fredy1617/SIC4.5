<?php
include ("../php/conexion.php");

$Texto = $conn->real_escape_string($_POST['texto']);

//Filtro anti-XSS
$mensaje = '';
$sql = "SELECT * FROM centrales_pings";
if ($Texto !="") {
	$sql = "SELECT * FROM centrales_pings WHERE ip LIKE '%$Texto%' OR id = '$Texto'";
}

$consulta =mysqli_query($conn, $sql);
$filas = mysqli_num_rows($consulta);

if ($filas == 0) {
	$mensaje = '<script>M.toast({html:"No se encontraron centrales.", classes: "rounded"})</script>';
}else{
	//La variable $resultados contiene el array que se genera en la consulta, asi que obtenemos los datos y los mostramos en un bucle.
	while($resultados = mysqli_fetch_array($consulta)){
		$id_comunidad = $resultados['comunidad'];
		$comunidad = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM comunidades WHERE id_comunidad = '$id_comunidad'"));
		$id_servidor = $comunidad['servidor'];
		$servidor = mysqli_fetch_array(mysqli_query($conn, "SELECT nombre FROM servidores WHERE id_servidor = '$id_servidor'"));

		//Output / Salida
		$mensaje .= '
			<tr>
				<td>'.$resultados['id'].'</td>
				<td>'.$comunidad['nombre'].'</td>
		        <td>'.$resultados['descripcion'].'</td>
		        <td>'.$resultados['ip'].'</td>
		        <td>'.$servidor['nombre'].'</td>
		        <td><form method="post" action="../views/editar_central_pins.php"><input name="id" type="hidden" value="'.$resultados['id'].'"><button type="submit" class="btn-floating btn-tiny waves-effect waves-light pink"><i class="material-icons">edit</i></button></form></td>
		    </tr>';
	}//Fin while $resultados
} //Fin else $filas

echo $mensaje;
mysqli_close($conn);
?>