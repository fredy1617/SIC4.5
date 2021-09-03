<?php
  include('../php/conexion.php');
  $User = $conn->real_escape_string($_POST['valorUsuario']);

  $usuario = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM users WHERE user_id = '$User'"));

  $ValorDe = $conn->real_escape_string($_POST['valorDe']);
  $ValorA = $conn->real_escape_string($_POST['valorA']);
  $user= $usuario['user_name'];
  $id_user = $usuario['user_id'];

  $instalaciones = mysqli_fetch_array(mysqli_query($conn,"SELECT count(*) FROM clientes WHERE  fecha_instalacion >= '$ValorDe' AND fecha_instalacion <= '$ValorA' AND  tecnico LIKE '%$user%'")); 
  $Ordenes = mysqli_fetch_array(mysqli_query($conn,"SELECT count(*) FROM orden_servicios WHERE (fecha_s >= '$ValorDe' AND fecha_s <= '$ValorA' AND  tecnicos_s LIKE '%$user%') OR (fecha_r >= '$ValorDe' AND fecha_r <= '$ValorA' AND  tecnicos_r LIKE '%$user%')")); 
  $Reportes_Oficina = mysqli_fetch_array(mysqli_query($conn,"SELECT count(*) FROM reportes WHERE (fecha_solucion >= '$ValorDe' AND fecha_solucion <= '$ValorA'  AND campo = 0 AND atendido = 1 AND (tecnico = '$id_user' OR apoyo = '$id_user')) OR (fecha_d >= '$ValorDe' AND fecha_d <= '$ValorA' AND tecnico_d = '$id_user')"));
  $Reportes_Campo = mysqli_fetch_array(mysqli_query($conn,"SELECT count(*) FROM reportes WHERE fecha_solucion >= '$ValorDe' AND fecha_solucion <= '$ValorA'  AND campo = 1 AND atendido = 1 AND (tecnico = '$id_user' OR apoyo = '$id_user')"));
?>
<br><br>
<h3>Estadistica de: <?php echo $usuario['firstname']; ?></h3><br>

<div class="row">
  <div class="col s2 hide-on-small-only"><br></div>
  <table class="col s12 l8 m8">
    <thead>
      <tr>
        <th><h4>Reliza</h4></th>
        <th><h4>Total</h4></th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td><h5 class="indigo-text">Instalaciones </h5></td>
        <td><h5 class="indigo-text"><?php echo $instalaciones['count(*)']; ?></h5></td>
      </tr>
      <tr>
        <td><h5 class="indigo-text">Reportes Ofician </h5></td>
        <td><h5 class="indigo-text"><?php echo $Reportes_Oficina['count(*)']; ?></h5></td>
      </tr>
      <tr>
        <td><h5 class="indigo-text">Reportes Campo </h5></td>
        <td><h5 class="indigo-text"><?php echo $Reportes_Campo['count(*)']; ?></h5></td>
      </tr>
      <tr>
        <td><h5 class="indigo-text">Ordenes </h5></td>
        <td><h5 class="indigo-text"><?php echo $Ordenes['count(*)']; ?></h5></td>
      </tr>
    </tbody>
  </table>
</div>
<br><br>