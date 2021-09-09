<?php
include('../php/conexion.php');
$ValorDe = $conn->real_escape_string($_POST['valorDe']);
$ValorA = $conn->real_escape_string($_POST['valorA']);
?>
<br><br>
<table class="bordered highlight responsive-table">
    <thead>
      <tr>
        <th>No.</th>
        <th>Id_Cliente</th>
        <th>Nombre</th>
        <th>Comunidad</th>
        <th>Municipio</th>
        <th>Costo</th>
        <th width="12%">Fecha</th>
        <th>Hora</th>
        <th>Registro</th>
        <th>Técnicos</th>
        <th>Se Pago</th>
      </tr>
    </thead>
    <tbody>
      <?php
      $sql_instalaciones = "SELECT * FROM clientes WHERE fecha_instalacion>='$ValorDe' AND fecha_instalacion<='$ValorA' ORDER BY fecha_instalacion";
      $resultado_instalaciones = mysqli_query($conn, $sql_instalaciones);
      $aux = mysqli_num_rows($resultado_instalaciones);
      if($aux>0){
      $TotalI = 0;
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
          <td><?php echo $aux;?></td>
          <td><b><?php echo $id_cliente;?></b></td>
          <td><?php echo $instalaciones['nombre'];?></td>
          <td><?php echo $comunidad['nombre'];?></td>
          <td><?php echo $comunidad['municipio'];?></td>
          <td>$<?php echo $instalaciones['total'];?></td>
          <td><?php echo $instalaciones['fecha_instalacion'];?></td>
          <td><?php echo $instalaciones['hora_alta']; ?></td>
          <td><?php echo $instalaciones['registro'];?></td>
          <td><?php echo $instalaciones['tecnico'];?></td>
          <td><?php echo $Estatus; ?></td>
        </tr>
        <?php
        $TotalI += $instalaciones['total'];
        $aux--;
      }
      ?>
        <tr> 
          <td></td><td></td><td></td>
          <td><b>TOTAL =</b></td>
          <td><b>$<?php echo $TotalI ?></b></td>
          <td></td><td></td><td></td><td></td>
        </tr>
        <?php
      }else{
        echo "<center><b><h5>No se encontraron instalaciones</h5></b></center>";
      }
?>
<?php 
mysqli_close($conn);
?>        
    </tbody>
</table><br><br><br>