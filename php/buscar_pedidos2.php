<?php
include ("../php/conexion.php");

$Texto = $conn->real_escape_string($_POST['texto']);

$mensaje = '';
$sql = "SELECT * FROM pedidos WHERE estatus = 'No Autorizado'  ORDER BY folio DESC";
if ($Texto != "") {
  $sql = "SELECT * FROM pedidos WHERE estatus = 'No Autorizado' AND (nombre LIKE '%$Texto%' OR folio = '$Texto' OR id_orden = '$Texto')  ORDER BY folio DESC";
}

$consulta =mysqli_query($conn, $sql);

if (mysqli_num_rows($consulta) <= 0) {
    echo '<h5 class = "center">No se encontraron pedidos (No Autorizados)</h5>';
}else{
  //La variable $resultados contiene el array que se genera en la consulta, asi que obtenemos los datos y los mostramos en un bucle.
  while($pedido = mysqli_fetch_array($consulta)){
    $usuario = $pedido['usuario'];
    $datos = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM users WHERE user_id = $usuario"));
    $folio = $pedido['folio'];
    $color = ($pedido['cerrado'] == 0)? 'red': 'green';
    $es = ($pedido['cerrado'] == 0)? 'PENDIENTE': 'CERRADO';
    $Fecha_req = ($pedido['fecha_requerido']=='0000-00-00' OR $pedido['fecha_requerido']== NULL OR $pedido['fecha_requerido']== '2000-01-01') ? 'N/A':'<b>'.$pedido['fecha_requerido'].'</b>';
    //Output / Salida
    $mensaje .= '
      <tr>
        <td><span class="new badge '.$color.'" data-badge-caption="">'.$es.'</span></td>
        <td>'.$folio.'</td>
        <td>'.$pedido['nombre'].'</td>
        <td>'.$pedido['id_orden'].'</td>
        <td>'.$pedido['fecha'].' '.$pedido['hora'].'</td>
        <td>'.$pedido['fecha_cerrado'].'</td>
        <td>'.$Fecha_req .'</td>
        <td>'.$datos['firstname'].'</td>
        <td><a href = "../views/detalles_pedido.php?folio='.$folio.'" class="btn-floating btn-tiny waves-effect waves-light pink"><i class="material-icons">visibility</i></a></td>
        <td><a onclick="borrar('.$folio.');" class="btn btn-floating red darken-1 waves-effect waves-light"><i class="material-icons">delete</i></a></td>
      </tr>';
  }//Fin while $resultados
} //Fin else $filas
echo $mensaje;
mysqli_close($conn);
?>

          
