<?php
include('../php/conexion.php');
$ValorDe = $conn->real_escape_string($_POST['valorDe']);
$ValorA = $conn->real_escape_string($_POST['valorA']);
$User = $conn->real_escape_string($_POST['valorUsuario']);

if ($User ==  0) {
  $usuarios = mysqli_query($conn, "SELECT * FROM users WHERE area='Redes' OR user_id = 49 OR user_id = 28");
}else if ($User == 27) {
  $usuarios = mysqli_query($conn, "SELECT * FROM users WHERE user_id = '$User' OR user_id = 28 ");
}else if ($User == 50) {
  $usuarios = mysqli_query($conn, "SELECT * FROM users WHERE user_id = '$User' OR user_id = 49");
}else{
  $usuarios = mysqli_query($conn, "SELECT * FROM users WHERE user_id = '$User' ");
}

while($usuario = mysqli_fetch_array($usuarios)){
  $user=$usuario['user_name'];
  $id_user = $usuario['user_id']
?>
<br><br>
<h3 class="center">TECNICO: <?php echo $usuario['firstname']; ?></h3>
<h4>Instalaciones</h4>
<table class="bordered highlight responsive-table">
    <thead>
      <tr>
        <th>#</th>
        <th>Nombre</th>
        <th>Comunidad</th>
        <th>Fecha</th>
        <th>Hora</th>
        <th>Técnicos</th>
        <th>Se Pago</th>
      </tr>
    </thead>
    <tbody>
      <?php
      $resultado_instalaciones = mysqli_query($conn,"SELECT * FROM clientes WHERE fecha_instalacion>='$ValorDe' AND fecha_instalacion<='$ValorA' AND  tecnico LIKE '%$user%' ORDER BY fecha_instalacion");
      $aux = mysqli_num_rows($resultado_instalaciones);
      if($aux>0){
      while($instalaciones = mysqli_fetch_array($resultado_instalaciones)){
        $id_comunidad = $instalaciones['lugar'];
        $comunidad = mysqli_fetch_array(mysqli_query($conn,"SELECT * FROM comunidades WHERE id_comunidad = '$id_comunidad'"));
        $id_cliente = $instalaciones['id_cliente'];
        $Total = $instalaciones['total'];
        $Anticipo = mysqli_query($conn,"SELECT * FROM pagos WHERE id_cliente = '$id_cliente' AND tipo = 'Anticipo'");
        $Entra = "No";
        $Estatus = "Revisar";
        if (mysqli_num_rows($Anticipo)>0) {
          $Pago = mysqli_fetch_array(mysqli_query($conn,"SELECT * FROM pagos WHERE id_cliente = '$id_cliente' AND tipo = 'Anticipo'"));
          $Anti = $Pago['cantidad'];
          if ($Anti == $Total) {
            $Estatus = "Oficina";
          }else{
            $Entra = "Si";
          }
        }else{
          $Entra = "Si";
        }
        if ($Entra == "Si") {
          $Liquido = mysqli_fetch_array(mysqli_query($conn,"SELECT * FROM pagos WHERE id_cliente = '$id_cliente' AND tipo = 'Liquidacion'"));
          $Tipo_Cambio = $Liquido['tipo_cambio'];
          if ($Tipo_Cambio == "Efectivo") {
            $Estatus = "Domicilio";
          }else if ($Tipo_Cambio == "Credito") {
            $Estatus =$Tipo_Cambio;
          }
        }
        ?>
        <tr>
          <td><b><?php echo $id_cliente;?></b></td>
          <td><?php echo $instalaciones['nombre'];?></td>
          <td><?php echo $comunidad['nombre'];?></td>
          <td><?php echo $instalaciones['fecha_instalacion'];?></td>
          <td><?php echo $instalaciones['hora_alta']; ?></td>
          <td><?php echo $instalaciones['tecnico'];?></td>
          <td><?php echo $Estatus; ?></td>
        </tr>
      <?php
        $aux--;
      }
      }else{
        echo "<center><b><h5>No se encontraron instalaciones</h5></b></center>";
      }
      ?>       
    </tbody>
</table><br><br>

<?php
$sql = mysqli_query($conn, "SELECT * FROM reportes WHERE  fecha_solucion>='$ValorDe' AND fecha_solucion<='$ValorA'  AND atendido = 1 AND tecnico = '$id_user'");
?>
<h4>Reportes</h4>
<table class="bordered highlight responsive-table" width="100%">
        <thead>
          <tr>
            <th>Id. Reporte</th>
            <th>Id. Cliente</th>
            <th>Nombre Cliente</th>
            <th>Fecha Solución</th>
            <th>Hora</th>
            <th width="15%">Descripción</th>
            <th>Técnico</th>
          </tr>
        </thead>
        <tbody>
        <?php
        $aux = mysqli_num_rows($sql);
        if($aux>0){ 
        while ($info = mysqli_fetch_array($sql)) {
          $id_cliente = $info['id_cliente'];
          $cliente = mysqli_fetch_array(mysqli_query($conn, "SELECT nombre FROM clientes WHERE id_cliente=$id_cliente"));
        ?>
          <tr>
            <td><?php echo $info['id_reporte']; ?></td>
            <td><?php echo $info['id_cliente']; ?></td>
            <td><?php echo $cliente['nombre']; ?></td>            
            <td><?php echo $info['fecha_solucion']; ?></td>
            <td><?php echo $info['hora_atendido']; ?></td>
            <td><?php echo $info['descripcion']; ?></td>
            <td><?php echo $user; ?></td>
          </tr>
        <?php
        $aux--;
        }
        }else{
          echo "<center><b><h5>No se encontraron reportes</h5></b></center>";
        }
        ?>
        </tbody>        
</table><br><br><br>
<?php
}
mysqli_close($conn);
?>