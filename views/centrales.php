<!DOCTYPE html>
<html lang="en">
<head>
<?php
  include('fredyNav.php');
  include('../php/cobrador.php');
  require('../php/conexion.php');
?>
<script>
function borrar(IdCentral){
  $.post("../php/borrar_central.php", { 
          valorIdCentral: IdCentral
  }, function(mensaje) {
  $("#borrar").html(mensaje);
  }); 
};
</script>
<title>SIC | Centrales</title>
</head>
<body>
  <div class="container">
    <div class="row" >
      <h3 class="hide-on-med-and-down">Centrales</h3>
      <h5 class="hide-on-large-only">Centrales</h5>
      <a href="form_central.php" class="waves-effect waves-light btn pink right">AGREGAR CENTRAL<i class="material-icons right">add</i></a>
    </div>
    <div id="borrar"></div>
    <table class="bordered highlight">
      <thead>
        <tr>
          <th>#</th>
          <th>Comunidad</th>
          <th>Encargado</th>
          <th>Telefono</th>
          <th>Ver</th>
          <th>Editar</th>
          <th>Borrar</th>
        </tr>
      </thead>
      <tbody>
      <?php
      $sql_tmp = mysqli_query($conn,"SELECT * FROM centrales");
      $columnas = mysqli_num_rows($sql_tmp);
      if($columnas == 0){
      ?>
        <h5 class="center">No hay centrales</h5>
      <?php
      }else{
        while($tmp = mysqli_fetch_array($sql_tmp)){
          $id_comundad = $tmp['comunidad'];
          $cominidad = mysqli_fetch_array(mysqli_query($conn,"SELECT * FROM comunidades WHERE id_comunidad = $id_comundad"));
      ?>
        <tr>
          <td><b><?php echo $tmp['id']; ?></b></td>
          <td><?php echo $cominidad['nombre']; ?></td>
          <td><?php echo $tmp['nombre']; ?></td>
          <td><?php echo $tmp['telefono']; ?></td>
          <td><form method="post" action="../views/central.php"><input name="id_central" type="hidden" value="<?php echo $tmp['id']; ?>"><button type="submit" class="btn-floating btn-tiny waves-effect waves-light pink"><i class="material-icons">visibility</i></button></form></td>
          <td><form method="post" action="../views/editar_central.php"><input name="id_central" type="hidden" value="<?php echo $tmp['id']; ?>"><button type="submit" class="btn-floating btn-tiny waves-effect waves-light pink"><i class="material-icons">edit</i></button></form></td>
          <td><a onclick="borrar(<?php echo $tmp['id'];?>);" class="btn btn-floating red darken-1 waves-effect waves-light"><i class="material-icons">delete</i></a></td>
        </tr>
      <?php
        }
      }
      mysqli_close($conn);
      ?>
      </tbody>
    </table><br>
  </div>
</body>
</html>