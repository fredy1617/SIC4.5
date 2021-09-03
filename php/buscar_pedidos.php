<?php
include ("../php/conexion.php");

$Texto = $conn->real_escape_string($_POST['texto']);

$mensaje = '';
$sql = "SELECT * FROM pedidos WHERE estatus = 'Autorizado'  ORDER BY folio DESC";
if ($Texto != "") {
	$sql = "SELECT * FROM pedidos WHERE estatus = 'Autorizado' AND (nombre LIKE '%$Texto%' OR folio = '$Texto' OR id_orden = '$Texto')  ORDER BY folio DESC";
}

$consulta =mysqli_query($conn, $sql);

if (mysqli_num_rows($consulta) <= 0) {
    echo '<h5 class = "center">No se encontraron pedidos (Autorizados)</h5>';
}else{
	//La variable $resultados contiene el array que se genera en la consulta, asi que obtenemos los datos y los mostramos en un bucle.
	while($resultados = mysqli_fetch_array($consulta)){
		$usuario = $resultados['usuario'];
        $datos = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM users WHERE user_id = $usuario"));
        $folio = $resultados['folio'];
	    $LISTOS = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM detalles_pedidos WHERE folio = $folio AND listo = 1"));
	    $TOTAL = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM detalles_pedidos WHERE folio = $folio"));
	    $color = ($LISTOS == $TOTAL)? 'green':'red';
    	$Fecha_req = ($resultados['fecha_requerido']=='0000-00-00' OR $resultados['fecha_requerido']== NULL OR $resultados['fecha_requerido']== '2000-01-01') ? 'N/A':'<b>'.$resultados['fecha_requerido'].'</b>';

		//Output / Salida
		$mensaje .= '
			<tr>
				<td><b class="'.$color.'-text">'.$LISTOS.' / '.$TOTAL.'</b></td>
				<td>'.$folio.'</td>
		        <td>'.$resultados['nombre'].'</td>
		        <td>'.$resultados['id_orden'].'</td>
		        <td>'.$resultados['fecha'].' '.$resultados['hora'].'</td>
		        <td>'.$resultados['fecha_cerrado'].'</td>
		        <td>'.$resultados['fecha_autorizado'].'</td>
		        <td>'.$Fecha_req.'</td>
		        <td>'.$datos['firstname'].'</td>
		        <td><a href = "../views/detalles_pedido.php?folio='.$folio.'" class="btn-floating btn-tiny waves-effect waves-light pink"><i class="material-icons">visibility</i></a></td>
		        <td><a onclick="borrar('.$folio.');" class="btn btn-floating red darken-1 waves-effect waves-light"><i class="material-icons">delete</i></a></td>
		    </tr>';
	}//Fin while $resultados
} //Fin else $filas
echo $mensaje;
mysqli_close($conn);
?>