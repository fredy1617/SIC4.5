<?php
include('../php/conexion.php');
$ValorDe = $conn->real_escape_string($_POST['valorDe']);
$ValorA = $conn->real_escape_string($_POST['valorA']);
$Usuario = $conn->real_escape_string($_POST['valorUsuario']);
$Tipo = $conn->real_escape_string($_POST['valorTipo']);
if ($Usuario != "" AND $Tipo == "") {
  $usuario = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM users WHERE user_id = '$Usuario'"));
  $total = mysqli_fetch_array(mysqli_query($conn, "SELECT SUM(cantidad) AS precio FROM pagos WHERE fecha>='$ValorDe' AND fecha<='$ValorA' AND id_user='$Usuario'"));
  $sql_pagos = mysqli_query($conn, "SELECT * FROM pagos WHERE fecha>='$ValorDe' AND fecha<='$ValorA' AND id_user='$Usuario' ORDER BY id_pago DESC");
  $head = $usuario['firstname'].' '.$usuario['lastname'].':  .  TOTAL = $'.$total['precio'];
}elseif ($Tipo != "" AND $Usuario == "") {
  $total = mysqli_fetch_array(mysqli_query($conn, "SELECT SUM(cantidad) AS precio FROM pagos WHERE fecha>='$ValorDe' AND fecha<='$ValorA' AND tipo_cambio='$Tipo'"));
  $head = $Tipo.':  .  TOTAL = $'.$total['precio'];
  $sql_pagos = mysqli_query($conn, "SELECT * FROM pagos WHERE fecha>='$ValorDe' AND fecha<='$ValorA' AND tipo_cambio = '$Tipo' ORDER BY id_pago DESC");
}else{
  $total = mysqli_fetch_array(mysqli_query($conn, "SELECT SUM(cantidad) AS precio FROM pagos WHERE fecha>='$ValorDe' AND fecha<='$ValorA' AND id_user='$Usuario' AND tipo_cambio='$Tipo'"));
  $head = $Tipo.':  .  TOTAL = $'.$total['precio'];
  $sql_pagos = mysqli_query($conn, "SELECT * FROM pagos WHERE fecha>='$ValorDe' AND fecha<='$ValorA' AND id_user='$Usuario' AND tipo_cambio = '$Tipo' ORDER BY id_pago DESC");
}
?>

<div>

<h4 class="blue-text"><?php echo $head;?></h4><br>
  <table class="bordered highlight responsive-table">
    <thead>
      <tr>
        <th>#Cliente</th>
        <th>Cliente</th>
        <th>Cantidad</th>
        <th>Tipo</th>
        <th>Descripción</th>
        <th>Fecha</th>
        <?php
        if ($Usuario != "" AND $Tipo == "") {
        ?>
        <th>Cambio</th>
        <?php
        }elseif ($Tipo == 'Banco'  OR $Tipo == 'SAN') {
        ?>
        <th>Referencia</th>        
        <th>Registró</th>
        <?php
        }else{
        ?>
        <th>Registró</th>
        <?php
        }
        ?>
      </tr>
    </thead>
    <tbody>
<?php
$aux = mysqli_num_rows($sql_pagos);
if($aux>0){
while($pagos = mysqli_fetch_array($sql_pagos)){
  $id_cliente = $pagos['id_cliente'];
  $sql = mysqli_query($conn, "SELECT nombre FROM clientes WHERE id_cliente = $id_cliente");
  
  $filas = mysqli_num_rows($sql);
  if ($filas == 0) {
    $sql = mysqli_query($conn, "SELECT nombre FROM dispositivos WHERE id_dispositivo = $id_cliente"); 
  }
  $cliente= mysqli_fetch_array($sql);
  $id_user = $pagos['id_user'];
  $usuario = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM users WHERE user_id = '$id_user'"));
  if ($pagos['tipo_cambio'] == 'Banco' OR $pagos['tipo_cambio'] == 'SAN') {
    $id = $pagos['id_pago'];
    $sqlR = mysqli_query($conn, "SELECT * FROM referencias WHERE id_pago = $id");  
    $filas2 = mysqli_num_rows($sqlR);
    if ($filas2 == 0) {
      $refe = "Sin";
    }else{
      $referecia = mysqli_fetch_array($sqlR);
      $refe = $referecia['descripcion'];
    }
  }
  ?>
  <tr>
    <td><b><?php echo $id_cliente;?></b></td>
    <td><?php echo $cliente['nombre'];?></td>
    <td>$<?php echo $pagos['cantidad'];?></td>
    <td><?php echo $pagos['tipo'];?></td>
    <td><?php echo $pagos['descripcion'];?></td>
    <td><?php echo $pagos['fecha'].' '.$pagos['hora'];?></td>
    <?php
    if ($Usuario != "" AND $Tipo == "") {
    ?>
    <td><?php echo $pagos['tipo_cambio'];?><br><?php if ($pagos['tipo_cambio'] == 'Banco' OR $pagos['tipo_cambio'] == 'SAN') { echo $refe; } ?></td>
    <?php
    }elseif ($Tipo == 'Banco' OR $Tipo == 'SAN') {
     ?>
    <td><?php echo $refe;?></td>
    <td><?php echo $usuario['firstname'];?></td>
    <?php
    }else{
    ?>
    <td><?php echo $usuario['firstname'];?></td>
    <?php
    }
    ?>
  </tr>
  <?php
  $aux--;
}
}else{
  echo "<center><b><h5>No hay pagos registrados en esta fecha</h5></b></center>";
}
?>
<?php 
mysqli_close($conn);
?>        
        </tbody>
      </table>
  </div>
<br>