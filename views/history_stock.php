<!DOCTYPE html>
<html>
<head>
  <title>SIC | Historial Stock</title>
</head>
<?php
include ('fredyNav.php');
include('../php/conexion.php');
include('../php/cobrador.php');
if (isset($_GET['id']) == false) {
  ?>
  <script>    
    function atras() {
      M.toast({html: "Regresando a listado tecnicos...", classes: "rounded"})
      setTimeout("location.href='stock.php'", 1000);
    }
    atras();
  </script>
  <?php
}else{
  $id_tecnico = $_GET['id'];
  $datos = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM users WHERE user_id = $id_tecnico"));
?>
<body>
  <div class="container" id="resultado_update_stock">
    <div class="row"><br><br>
        <h3 class="hide-on-med-and-down"><b>Historial: </b><?php echo $datos['firstname'].' '.$datos['lastname'];?><a href="stock.php" class="btn-floating btn-large waves-effect waves-light pink right"><i class="material-icons">reply</i></a></h3>
        <h5 class="hide-on-large-only"><b>Historial: </b><?php echo $datos['firstname'].' '.$datos['lastname'];?><a href="stock.php" class="btn-floating btn-large waves-effect waves-light pink right"><i class="material-icons">reply</i></a></h5>
      </div><br>
      <div class="row">
        <div class="row col s12">
        <table class="bordered highlight responsive-table">
          <thead>
            <th>#</th>
            <th>Tipo</th>
            <th>Nombre</th>
            <th>Serie</th>
            <th>Cantidad</th>
            <th>Uso</th>
            <th>Disponible</th>
            <th>Fecha Alta</th>
            <th>Fecha Salida</th>
            <th>Registro</th>
          </thead>
          <tbody>
          <?php
          $tab = mysqli_query($conn, "SELECT * FROM stock_tecnicos WHERE tecnico = $id_tecnico ORDER BY fecha_alta, id DESC");
          while($unidad = mysqli_fetch_array($tab)){
              $user_id = $unidad['registro'];
              if ((mysqli_num_rows(mysqli_query($conn, "SELECT * FROM users WHERE user_id = $user_id"))) > 0) {
                $user = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM users WHERE user_id = $user_id"));
              }
            ?>
            <tr>
              <td><b><?php echo $unidad['id']; ?></b></td>
              <td><?php echo $unidad['tipo']?></td>
              <td><?php echo $unidad['nombre']; ?></td>
              <td><?php echo $unidad['serie']; ?></td>
              <td><?php echo $unidad['cantidad']; ?></td>
              <td><?php echo $unidad['uso']; ?></td>
              <td><b><?php echo ($unidad['disponible'] == 1)? "NO":"SI"; ?></b></td>
              <td><?php echo $unidad['fecha_alta']; ?></td>
              <td><?php echo $unidad['fecha_salida']; ?></td>
              <td><?php echo $user['firstname']; ?></td>
            </tr>
          <?php
          }
          ?> 
          </tbody>
        </table>
        </div>
    </div>     
    </div>
</body>
<?php
}
mysqli_close($conn);
?>
</script>
</html>