<script>
$(document).ready(function(){
    $('.tooltipped').tooltip({delay: 50, html: true});
  });
</script>
<?php
include('../php/conexion.php');
error_reporting(0);
$consultaBusqueda = $_POST['valorBusqueda'];

//Filtro anti-XSS
$caracteres_malos = array("<", ">", "\"", "'", "/", "<", ">", "'", "/");
$caracteres_buenos = array("& lt;", "& gt;", "& quot;", "& #x27;", "& #x2F;", "& #060;", "& #062;", "& #039;", "& #047;");
$consultaBusqueda = str_replace($caracteres_malos, $caracteres_buenos, $consultaBusqueda);

//Variable vacía (para evitar los E_NOTICE)
$mensaje = "";

//Comprueba si $consultaBusqueda está seteado
if (isset($consultaBusqueda)) {
	
	//Selecciona todo de la tabla mmv001 
	//donde el nombre sea igual a $consultaBusqueda, 
	//o el apellido sea igual a $consultaBusqueda, 
	//o $consultaBusqueda sea igual a nombre + (espacio) + apellido
	$sql = "SELECT * FROM clientes WHERE nombre LIKE '%$consultaBusqueda%' OR id_cliente = '$consultaBusqueda' AND instalacion IS NOT NULL";
	$consulta = mysqli_query($conn, $sql);
	//Obtiene la cantidad de filas que hay en la consulta
	$filas = mysqli_num_rows($consulta);

	//Si no existe ninguna fila que sea igual a $consultaBusqueda, entonces mostramos el siguiente mensaje
	if ($filas == 0) {
		$mensaje = '<script type="text/javascript">Materialize.toast("No se encontraron clientes.", 4000, "rounded")</script>';
	} else {
		//La variable $resultado contiene el array que se genera en la consulta, así que obtenemos los datos y los mostramos en un bucle
		while($resultados = mysqli_fetch_array($consulta)) {
			$id_comunidad = $resultados['lugar'];
            $sql_comunidad = mysqli_fetch_array(mysqli_query($conn,"SELECT nombre FROM comunidades WHERE id_comunidad=$id_comunidad"));

			$no_cliente = $resultados['id_cliente'];
			$nombre = $resultados['nombre'];
			$lugar = $sql_comunidad['nombre'];
			$telefono = $resultados['telefono'];
			$ip = $resultados['ip'];

			//Output
			$mensaje .= '			
		          <tr>
		            <td>'.$no_cliente.'</td>
		            <td> <a class="tooltipped" href="#" data-position="top" data-delay="50" data-tooltip="Lugar: '.$lugar.' <br> Telefono: '.$telefono.' <br> IP: '.$ip.'">'.$nombre.'</a></td>
		            <td><form method="post" action="../views/crear_pago.php"><input id="no_cliente" name="no_cliente" type="hidden" value="'.$no_cliente.'"><button class="btn-floating btn-tiny waves-effect waves-light pink"><i class="material-icons">payment</i></button></form></td>
		            <td><form method="post" action="../views/form_reportes.php"><input id="no_cliente" name="no_cliente" type="hidden" value="'.$no_cliente.'"><button class="btn-floating btn-tiny waves-effect waves-light pink"><i class="material-icons">report_problem</i></button></form></td>
		          </tr>';
		        
		            
		}//Fin while $resultados
	} //Fin else $filas

}//Fin isset $consultaBusqueda

//Devolvemos el mensaje que tomará jQuery
echo '<table class="bordered highlight centered">
	    <thead>
	      <tr>
	      	<th># Cliente</th>
	        <th>Nombre</th>
	        <th>Pago</th>
	        <th>Reporte</th>
	      </tr>
	    </thead>
	    <tbody>';
			echo $mensaje;
echo    '</tbody>
	  </table>';

	  mysqli_close($conn);
?>