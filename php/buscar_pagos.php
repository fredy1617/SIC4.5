<?php
include('../php/conexion.php');
$ValorDe = $conn->real_escape_string($_POST['valorDe']);
$ValorA = $conn->real_escape_string($_POST['valorA']);
$Usuario = $conn->real_escape_string($_POST['valorUsuario']);

$usuario = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM users WHERE user_id = '$Usuario'"));

$total = mysqli_fetch_array(mysqli_query($conn, "SELECT SUM(cantidad) AS precio FROM pagos WHERE fecha>='$ValorDe' AND fecha<='$ValorA' AND id_user='$Usuario'"));
?>

<div>
<h4 class="right"><?php echo $usuario['firstname'].' '.$usuario['lastname'].': $'.$total['precio'];?></h4><br><br>
<br><br><br>
  <table class="bordered highlight responsive-table">
    <thead>
      <tr>
        <th>#Cliente</th>
        <th>Cliente</th>
        <th>Cantidad</th>
        <th>Tipo</th>
        <th>Descripción</th>
        <th>Fecha</th>
        <th>Tipo</th>
      </tr>
    </thead>
    <tbody>
<?php
$sql_pagos = "SELECT * FROM pagos WHERE fecha>='$ValorDe' AND fecha<='$ValorA' AND id_user='$Usuario' ORDER BY id_pago DESC";
$resultado_pagos = mysqli_query($conn, $sql_pagos);
$aux = mysqli_num_rows($resultado_pagos);
if($aux>0){
while($pagos = mysqli_fetch_array($resultado_pagos)){
  $id_cliente = $pagos['id_cliente'];
  $cliente = mysqli_fetch_array(mysqli_query($conn,"SELECT nombre FROM clientes WHERE id_cliente = $id_cliente"));
  ?>
  <tr>
    <td><b><?php echo $id_cliente;?></b></td>
    <td><?php echo $cliente['nombre'];?></td>
    <td>$<?php echo $pagos['cantidad'];?></td>
    <td><?php echo $pagos['tipo'];?></td>
    <td><?php echo $pagos['descripcion'];?></td>
    <td><?php echo $pagos['fecha'];?></td>
    <td><?php echo $pagos['tipo_cambio'];?></td>
  </tr>
  <?php
  $aux--;
}
}else{
  echo "<center><b><h5>Este usuario aún no ha registrado pagos</h5></b></center>";
}
?>
<?php 
mysqli_close($conn);
?>        
        </tbody>
      </table>
  </div>
<br>