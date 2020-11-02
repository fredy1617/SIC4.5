<?php 
#INCLUIMOS EL ARCHIVO EL CUAL HACE LA CONEXION DE LA BASE DE DATOS PARA ACCEDER A LA INFORMACION DEL SISTEMA
include ('conexion.php');
#RECIBIMOS EL LA VARIABLE texto CON EL METODO POST QUE ES EL NOMBRE DEL CLIENTE A BUSCAR
$Texto = $conn->real_escape_string($_POST['texto']);
$mensaje = '';//CREAMOS LA VARIABLE QUE CONTRENDRA TODA LA INFORMACION DEL CLIENTE EN FORMA DE TABLA
#VERIFIAMOS QUE EL INPUT DEL NOMBRE ESTE ESCRITO ALGO PARA PODER HACER LA SELECCION $Texto
if ($Texto != "") {
	//AQUI BUSCARA SI ES POR NOMBRE O POR ID DE CLIENTE
	$consulta = mysqli_query($conn, "SELECT * FROM canceladas WHERE ( nombre LIKE '%$Texto%') LIMIT 1");
	//Obtiene la cantidad de filas que hay en la consulta
	$filas = mysqli_num_rows($consulta);
	if ($filas == 0) {
		$mensaje = '<br><br>';
	} else {
		$mensaje .='
				<table class="bordered highlight centered responsive-table">
					<thead>
					  <tr>
					    <th>Nombre</th>
					    <th>Telefono</th>
					    <th>Comunidad</th>
					    <th>Direccion</th>
					    <th>Referencia</th>
					    <th>Elegir</th>
					  </tr>
					</thead>';
		//La variable $resultado contiene el array que se genera en la consulta, así que obtenemos los datos y los mostramos en un bucle		
		while($resultados = mysqli_fetch_array($consulta)) {
			$id_comunidad = $resultados['lugar'];
			$comunidad = mysqli_fetch_array(mysqli_query($conn,"SELECT nombre FROM comunidades WHERE id_comunidad = $id_comunidad"));
			//Output
			$mensaje .= '
					<tbody>				
				      <tr>
				        <td><b>'.$resultados['nombre'].'</b></td>
				        <td><b>'.$resultados['telefono'].'</b></td>
				        <td>'.$comunidad['nombre'].'</td>
				        <td>'.$resultados['direccion'].'</td>
				        <td>'.$resultados['referencia'].'</td>
				       <td><form method="post" action="../views/form_instalacion_R.php"><input id="no_cliente" name="no_cliente" type="hidden" value="'.$resultados['id_cliente'].'"><button class="btn-floating btn-tiny waves-effect waves-light pink"><i class="material-icons">check</i></button></form></td>
			          </tr>
			        </tbody>';    
			}//Fin while $resultados
		} //Fin else $filas
		$mensaje.= '
				</table>';
	}
echo $mensaje;
mysqli_close($conn);
?>