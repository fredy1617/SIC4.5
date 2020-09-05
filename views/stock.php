<!DOCTYPE html>
<html lang="en">
<head>
<?php
  include('fredyNav.php');
  include('../php/conexion.php');
?>
<title>SIC | Stock</title>
</head>
<main>
<body>
	<div class="container">
    <div><br><br>
      <h3 class="row"><b>Stock de Tecnicos:</b></h3><br>
      <div class="row">
        <?php
        $usuarios = mysqli_query($conn, "SELECT * FROM users WHERE area = 'Redes' OR user_id IN (25, 28, 49)");
        $filas = mysqli_num_rows($usuarios);
        if ($filas > 0) {
        ?>        
        <div class="row">
        <table class="bordered highlight responsive-table">
          <thead>
            <th>#</th>
            <th>Nombre</th>
            <th>Usuario</th>
            <th>Stock</th>
          </thead>
          <tbody>
          <?php
          $aux = 0;
          while($user = mysqli_fetch_array($usuarios)){
            ?>
            <tr>
              <td><?php echo $user['user_id']; ?></td>
              <td><b><?php echo $user['firstname'].' '.$user['lastname']; ?></b></td>
              <td><?php echo $user['user_name']; ?></td>
              <td><br><form action="stock_tecnico.php" method="post"><input type="hidden" name="id_tecnico" value="<?php echo $user['user_id']; ?>"><button type="submit" class="btn-floating btn-tiny waves-effect waves-light pink"><i class="material-icons">assignment_ind</i></button></form></td>
            </tr>
          <?php
          }
          ?> 
          </tbody>
        </table>
        </div>
        <?php
        }
        ?>
    </div>
  </div><br><br>
<?php mysqli_close($conn);?>
</body>
</main>
</html>