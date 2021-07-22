<?php
include ("../php/conexion.php");

$Texto = $conn->real_escape_string($_POST['texto']);

$mensaje = '';
$sql = "SELECT * FROM inventario";
if ($Texto !="") {
	$sql = "SELECT * FROM inventario WHERE nombre LIKE '%$Texto%' OR codigo = '$Texto'";
}

$consulta =mysqli_query($conn, $sql);
$filas = mysqli_num_rows($consulta);

if ($filas == 0) {
	$mensaje = '<script>M.toast({html:"No se encontraron productos.", classes: "rounded"})</script>';
}else{
	//La variable $resultados contiene el array que se genera en la consulta, asi que obtenemos los datos y los mostramos en un bucle.
	while($resultados = mysqli_fetch_array($consulta)){
		//Output / Salida
		$mensaje .= '
			<tr>
				<td>'.$resultados['codigo'].'</td>
		        <td>'.$resultados['nombre'].'</td>
		        <td>'.$resultados['marca'].'</td>
		        <td>'.$resultados['cantidad'].'</td>
		        <td>'.$resultados['unidad'].'</td>
		        <td>'.$resultados['estatus'].'</td>
		        <td>'.$resultados['responsable'].'</td>
		        <td><a onclick="borrar('.$resultados['codigo'].');" class="btn btn-floating red darken-1 waves-effect waves-light"><i class="material-icons">delete</i></a>
		    </tr>';
	}//Fin while $resultados
} //Fin else $filas

echo $mensaje;
mysqli_close($conn);
?>