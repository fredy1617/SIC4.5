<?php
include('../php/conexion.php');
$ValorDe = $conn->real_escape_string($_POST['valorDe']);
$ValorA = $conn->real_escape_string($_POST['valorA']);
$valorUsuario = $conn->real_escape_string($_POST['valorUsuario']);
?>
<br><br>
<h4>Matreial De: <?php echo $valorUsuario; ?></h4>
<table class="bordered highlight responsive-table">
    <thead>
      <tr>
        <th>No.</th>
        <th>Id_Cliente</th>
        <th>Antena</th>
        <th>Router</th>
        <th>Cable</th>
        <th>Tubos</th>
        <th>Extras</th>
        <th>Fecha</th>
        <th>Tipo</th>
        <th>Es</th>
      </tr>
    </thead>
    <tbody>
      <?php
      $sql_material = mysqli_query($conn, "SELECT * FROM materiales WHERE fecha>='$ValorDe' AND fecha<='$ValorA' AND usuarios LIKE '%$valorUsuario%' ORDER BY fecha");
      $aux = mysqli_num_rows($sql_material);
      if($aux>0){
      while($material = mysqli_fetch_array($sql_material)){
        ?>
        <tr> 
          <td><?php echo $aux;?></td>
          <td><?php echo $material['id_cliente'];?></td>
          <td><?php echo $material['antena'];?></td>
          <td><?php echo $material['router'];?></td>
          <td><?php echo $material['cable'];?></td>
          <td><?php echo $material['tubos'];?></td>
          <td><?php echo $material['extras'];?></td>
          <td><?php echo $material['fecha'];?></td>
          <td><?php echo $material['tipo'];?></td>
          <td><?php echo $material['es'];?></td>
        </tr>
        <?php
        $aux--;
      }
      }else{
        echo "<center><b><h5>No se encontro material utilizado</h5></b></center>";
      }
?>
<?php 
?>        
    </tbody>
</table><br><br>
<h4>Matreial Ordenes</h4>
<table class="bordered highlight responsive-table">
    <thead>
      <tr>
        <th>No.</th>
        <th>Id_Cliente</th>
        <th>Material</th>
        <th>Fecha</th>
        <th>Tipo</th>
        <th>Es</th>
      </tr>
    </thead>
    <tbody>
      <?php
      $sql_material = mysqli_query($conn, "SELECT * FROM orden_servicios WHERE fecha_s >= '$ValorDe' AND fecha_s <= '$ValorA' AND tecnicos_s LIKE '%$valorUsuario%' ORDER BY fecha_s");
      $aux = mysqli_num_rows($sql_material);
      if($aux>0){
      while($material = mysqli_fetch_array($sql_material)){
        ?>
        <tr> 
          <td><?php echo $aux;?></td>
          <td><?php echo $material['id_cliente'];?></td>
          <td><?php echo $material['material'];?></td>
          <td><?php echo $material['fecha_s'];?></td>
          <td>Nuevo</td>
          <td>Orden Servicio</td>
        </tr>
        <?php
        $aux--;
      }
      }else{
        echo "<center><b><h5>No se encontro material utilizado</h5></b></center>";
      }
?>
<?php 
mysqli_close($conn);
?>        
    </tbody>
</table><br>