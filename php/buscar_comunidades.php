<?php
include ("../php/conexion.php");

$Texto = $conn->real_escape_string($_POST['texto']);

$mensaje = '';
$sql = "SELECT * FROM comunidades";
if ($Texto !="") {
	$sql = "SELECT * FROM comunidades WHERE nombre LIKE '%$Texto%' OR id_comunidad = '$Texto'";
}

$consulta =mysqli_query($conn, $sql);
$filas = mysqli_num_rows($consulta);

if ($filas == 0) {
	$mensaje = '<script>M.toast({html:"No se encontraron comunidades.", classes: "rounded"})</script>';
}else{
	//La variable $resultados contiene el array que se genera en la consulta, asi que obtenemos los datos y los mostramos en un bucle.
	while($resultados = mysqli_fetch_array($consulta)){

		$id_servidor = $resultados['servidor'];
		$servidor = mysqli_fetch_array(mysqli_query($conn, "SELECT nombre FROM servidores WHERE id_servidor = '$id_servidor'"))	;

		//Output / Salida

		$mensaje .= '
			<tr>
				<td>'.$resultados['id_comunidad'].'</td>
		        <td>'.$resultados['nombre'].'</td>
		        <td>'.$resultados['municipio'].'</td>
		        <td>'.$servidor['nombre'].'</td>
		        <td>'.$resultados['instalacion'].'</td>
		        <td><form method="post" action="../views/editar_comunidad.php"><input name="no_comunidad" type="hidden" value="'.$resultados['id_comunidad'].'"><button type="submit" class="btn-floating btn-tiny waves-effect waves-light pink"><i class="material-icons">edit</i></button></form></td>
		    </tr>';
	}//Fin while $resultados
} //Fin else $filas

echo $mensaje;
mysqli_close($conn);
?>