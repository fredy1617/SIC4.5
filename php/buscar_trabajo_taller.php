<?php
include('../php/conexion.php');
$ValorDe = $conn->real_escape_string($_POST['valorDe']);
$ValorA = $conn->real_escape_string($_POST['valorA']);
$valorUsuario = $conn->real_escape_string($_POST['valorUsuario']);
$usuario = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM users WHERE user_id = '$valorUsuario'"));
?>
<br><br>
<h4>Trabajo realizadó por: <?php echo $usuario['firstname']; ?></h4>
<table class="bordered highlight responsive-table">
    <thead>
      <tr>
        <th>Folio</th>
        <th>Dispositivo</th>
        <th>Cliente</th>
        <th>Trabajo</th>
        <th>Accion</th>
        <th>Fecha</th>
        <th>Hora</th>
      </tr>
    </thead>
    <tbody>
      <?php
      $sql = mysqli_query($conn, "SELECT * FROM actividades_taller WHERE fecha>='$ValorDe' AND fecha<='$ValorA' AND tecnico = '$valorUsuario' ORDER BY fecha, hora");
      if(mysqli_num_rows($sql)>0){
        while($actividad = mysqli_fetch_array($sql)){
          $id_dispositivio =  $actividad['dispositivo'];
          $dispositivo = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM dispositivos WHERE id_dispositivo = '$id_dispositivio'"));
          $disp = $dispositivo['tipo'].' '.$dispositivo['marca'];
          ?>
          <tr> 
            <td><?php echo $id_dispositivio;?></td>
            <td><?php echo $disp;?></td>
            <td><?php echo $dispositivo['nombre'];?></td>
            <td><?php echo $dispositivo['observaciones'];?></td>
            <td><?php echo $actividad['accion'];?></td>
            <td><?php echo $actividad['fecha'];?></td>
            <td><?php echo $actividad['hora'];?></td>
          </tr>
          <?php          
        }
      }else{
        echo "<center><b><h5>No se encontro trabajo realizado</h5></b></center>";
      }
?>
<?php 
?>        
    </tbody>
</table><br><br>
