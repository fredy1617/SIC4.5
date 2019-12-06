<?php
include('../php/conexion.php');
$ValorDe = $conn->real_escape_string($_POST['valorDe']);
$ValorA = $conn->real_escape_string($_POST['valorA']);
$sql = "SELECT * FROM pagos WHERE fecha>='$ValorDe' AND fecha<='$ValorA' AND tipo = 'Dispositivo' ORDER BY fecha";
$resultado = mysqli_query($conn, $sql);
?>
<br><br>
<table class="bordered highlight responsive-table">
    <thead>
      <tr>
        <th>No. Dispositivo</th>
        <th>Nombre</th>
        <th>Dispositivo</th>
        <th>Descripcion</th>
        <th>Fecha</th>
        <th>Cambio</th>
        <th>Cantidad</th>        
      </tr>
    </thead>
    <tbody>
      <?php
      $aux = mysqli_num_rows($resultado);
      if($aux>0){
      $Total = 0;
      while($pagos = mysqli_fetch_array($resultado)){
        $id_dispositivo = $pagos['id_cliente'];
        $Dispositivo = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM dispositivos WHERE id_dispositivo = '$id_dispositivo'"));
        ?>
        <tr> 
          <td><b><?php echo $id_dispositivo;?></b></td>
          <td><?php echo $Dispositivo['nombre'];?></td>
          <td><?php echo $Dispositivo['tipo'].' '.$Dispositivo['marca'];?></td>
          <td><?php echo $pagos['descripcion']; ?></td>
          <td><?php echo $pagos['fecha']; ?></td>
          <td><?php echo $pagos['tipo_cambio']; ?></td>
          <td>$ <?php echo $pagos['cantidad']; ?></td>
        </tr>
        <?php
        $Total += $pagos['cantidad'];
      }
      ?>
        <tr> 
          <td></td><td></td><td></td><td></td><td></td>
          <td><b>TOTAL:</b></td><td><b>$ <?php echo $Total; ?></b></td>
        </tr>
        <?php
      }else{
        echo "<center><b><h5>No se encontraron instalaciones</h5></b></center>";
      }
      ?>   
    </tbody>
</table><br><br>
<?php 
mysqli_close($conn);
?> 