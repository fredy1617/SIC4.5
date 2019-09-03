<?php
session_start();
date_default_timezone_set('America/Mexico_City');

include('../php/conexion.php');
$IdPago = $conn->real_escape_string($_POST['valorIdPago']);
$IdCliente = $conn->real_escape_string($_POST['valorIdCliente']);
$fecha_corte = mysqli_fetch_array(mysqli_query($conn, 'SELECT * FROM clientes WHERE id_cliente='.$IdCliente));
$Fecha = $fecha_corte['fecha_corte'];
$nuevafecha = strtotime('-1 month', strtotime($Fecha));
$FechaCorte = date('Y-m-05', $nuevafecha);

$id = $_SESSION['user_id'];
$area = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM users WHERE user_id=$id"));

if($area['area']!="Administrador"){
  echo "<script >M.toast({html: 'Sólo un administrador puede borrar pagos.', classes: 'rounded'});</script>";
}else{
  $Pago = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM pagos WHERE id_pago=$IdPago"));
  if ($Pago['tipo_cambio'] == 'Credito') {
    $id_deuda = $Pago['id_deuda'];
    if ( mysqli_query($conn, "DELETE FROM deudas WHERE id_deuda = '$id_deuda'")) {
      echo '<script >M.toast({html:"Deuda Borrada.", classes: "rounded"})</script>';   
    }
  }
  if(mysqli_query($conn, "DELETE FROM pagos WHERE id_pago = '$IdPago'")){
    echo '<script >M.toast({html:"Pago Borrado.", classes: "rounded"})</script>'; 
    mysqli_query($conn, "UPDATE clientes SET fecha_corte='$FechaCorte' WHERE id_cliente='$IdCliente'");
  }else{
    echo "<script >M.toast({html: 'Ha ocurrido un error.', classes: 'rounded'});</script>";
  }
}
?>
<div id="mostrar_pagos">
    <table class="bordered highlight">
    <thead>
      <tr>
        <th>#</th>
        <th>Cantidad</th>
        <th>Tipo</th>
        <th>Descripción</th>
        <th>Fecha</th>
        <th>Imprimir</th>
        <th>Borrar</th>
      </tr>
    </thead>
    <tbody>
<?php
$sql_pagos = "SELECT * FROM pagos WHERE id_cliente = ".$IdCliente." ORDER BY id_pago DESC";
$resultado_pagos = mysqli_query($conn, $sql_pagos);
$aux = mysqli_num_rows($resultado_pagos);
if($aux>0){
while($pagos = mysqli_fetch_array($resultado_pagos)){
  ?>
  <tr>
    <td><b><?php echo $aux;?></b></td>
    <td>$<?php echo $pagos['cantidad'];?></td>
    <td><?php echo $pagos['tipo'];?></td>
    <td><?php echo $pagos['descripcion'];?></td>
    <td><?php echo $pagos['fecha'];?></td>
    <td><a onclick="imprimir(<?php echo $pagos['id_pago'];?>);" class="btn btn-floating pink waves-effect waves-light"><i class="material-icons">print</i></a>
        </td>
    <td><a onclick="borrar(<?php echo $pagos['id_pago'];?>);" class="btn btn-floating red darken-4 waves-effect waves-light"><i class="material-icons">delete</i></a>
    </td>
  </tr>
  <?php
  $aux--;
}
}else{
  echo "<center><b><h3>Este cliente aún no ha registrado pagos</h3></b></center>";
}
?>
<?php 
mysqli_close($conn);
?>        
        </tbody>
      </table>
  </div>
<br>