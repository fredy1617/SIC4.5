<?php
include('../php/conexion.php');
$IdRefaccion = $conn->real_escape_string($_POST['valorIdRefaccion']);
$mensaje = '';

if(mysqli_query($conn, "DELETE FROM `refacciones` WHERE `id_refaccion` = $IdRefaccion")){
    echo '<script >M.toast({html:"Refacciones Borrada Correctamente.", classes: "rounded"})</script>';
    }else{
    echo "<script >M.toast({html: 'Ha ocurrido un error.', classes: 'rounded'});/script>";
    }
?>
<table class="bordered highlight responsive-table">
  <thead>
    <th>#</th>
    <th>Refacción</th>
    <th>Precio</th>
    <th>Borrar</th>
  </thead>
  <tbody>                 
  <?php
  $sql = mysqli_query($conn, "SELECT * FROM refacciones WHERE id_dispositivo = '$IdRefaccion' ");
  if (mysqli_num_rows($sql)>0) {
  $aux= 0;
  while ($refas = mysqli_fetch_array($sql)) {
    $aux++;
  ?>
    <tr>
      <td><?php echo $aux; ?></td>
      <td><?php echo $refas['descripcion']; ?></td>
      <td>$<?php echo $refas['cantidad']; ?></td>
      <td><a onclick="borrar_refa(<?php echo $refas['id_refaccion']; ?>);" class="btn btn-floating red darken-1 waves-effect waves-light"><i class="material-icons">delete</i></a></td>

    </tr>
    <?php
    }
    }
   mysqli_close($conn);
    ?>
  </tbody>
</table>

        
  