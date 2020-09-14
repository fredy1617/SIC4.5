<?php
include('../php/conexion.php');

$Id_Comunidad = $conn->real_escape_string($_POST['comunidad']);

$sql = mysqli_query($conn, "SELECT * FROM clientes WHERE  lugar = '$Id_Comunidad'");
if ($Id_Comunidad == 'Todos') {
  $sql = mysqli_query($conn, "SELECT * FROM clientes");
} 
$filas = mysqli_num_rows($sql);
if ($filas == 0) {
  echo '<script>M.toast({html:"No se encontraron clientes.", classes: "rounded"})</script>';
} else {
  $corte = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM int_cortes ORDER BY id DESC LIMIT 1"));
  $all_corte = $corte['fecha'];
?>
<div class="row">
  <div class="col s12 m6 l6">   
    <h4 class="hide-on-med-and-down indigo-text"><b>ACTIVOS</b></h4>  
    <h5 class="hide-on-large-only indigo-text"><b>ACTIVOS</b></h5>
    <table class="border highlight">
            <thead>
                <tr>
                  <th>#</th>
                  <th>Estatus</th>
                    <th>ID</th>
                    <th>Cliente</th>
                    <th>IP</th>
                    <th>Fecha Corte</th>
                </tr>
            </thead>
            <tbody>
            <?php   
            $sql1 = mysqli_query($conn, "SELECT * FROM clientes WHERE  lugar = '$Id_Comunidad' AND fecha_corte > '$all_corte'");
            if ($Id_Comunidad == 'Todos') {
              $sql1 = mysqli_query($conn, "SELECT * FROM clientes WHERE  fecha_corte > '$all_corte'");
            }           $filas1 = mysqli_num_rows($sql1);
            if ($filas1 == 0) {
              echo "<h5><b>No se encontraron clientes activos</b></h5>";
            } else {
              $aux = 0;
              while ($cliente = mysqli_fetch_array($sql1)) {
              $aux ++;   
              ?>
                <tr>
                    <td><?php echo $aux; ?></span></td>
                    <td><span class="new badge green" data-badge-caption=""></span></td>
                    <td><b><?php echo $cliente['id_cliente']; ?></b></td>
                    <td><?php echo $cliente['nombre']; ?></td>
                    <td><?php echo $cliente['ip']; ?></td>
                    <td><?php echo $cliente['fecha_corte']; ?></td>
                </tr>
              <?php
              }
            }
              ?>
            </tbody>
        </table>  
  </div>
  <div class="col s12 m6 l6">
    <h4 class="hide-on-med-and-down indigo-text"><b>INACTIVOS</b></h4>  
    <h5 class="hide-on-large-only indigo-text"><b>INACTIVOS</b></h5>
    <table class="border highlight">
            <thead>
                <tr>
                  <th>#</th>
                  <th>Estatus</th>
                    <th>ID</th>
                    <th>Cliente</th>
                    <th>IP</th>
                    <th>Fecha Corte</th>
                </tr>
            </thead>
            <tbody>
            <?php   
            $sql1 = mysqli_query($conn, "SELECT * FROM clientes WHERE  lugar = '$Id_Comunidad' AND fecha_corte < '$all_corte'");
            if ($Id_Comunidad == 'Todos') {
              $sql1 = mysqli_query($conn, "SELECT * FROM clientes WHERE  fecha_corte < '$all_corte'");
            }   
            $filas1 = mysqli_num_rows($sql1);
            if ($filas1 == 0) {
              echo "<h5><b>No se encontraron clientes inactivos</b></h5>";
            } else {
              $aux = 0;
              while ($cliente = mysqli_fetch_array($sql1)) {
              $aux ++;   
              ?>
                <tr>
                    <td><?php echo $aux; ?></span></td>
                    <td><span class="new badge red" data-badge-caption=""></span></td>
                    <td><b><?php echo $cliente['id_cliente']; ?></b></td>
                    <td><?php echo $cliente['nombre']; ?></td>
                    <td><?php echo $cliente['ip']; ?></td>
                    <td><?php echo $cliente['fecha_corte']; ?></td>
                </tr>
              <?php
              }
            }
              ?>
            </tbody>
        </table>
  </div>  
</div>
<?php } ?>