<!DOCTYPE html>
<html lang="en">
<head>
<?php
#INCLUIMOS EL ARCHIVO DONDE ESTA LA BARRA DE NAVEGACION DEL SISTEMA
include('fredyNav.php');
#INCLUIMOS EL ARCHIVO EL CUAL HACE LA CONEXION DE LA BASE DE DATOS PARA ACCEDER A LA INFORMACION DEL SISTEMA
include('../php/conexion.php');
#INCLUIMOS UN ARCHIVO QUE PROHIBE EL ACCESO A ESTA VISTA A LOS USUARIOS CON EL ROL DE COBRADOR 
include('../php/cobrador.php');
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
        #SELECCIONAMOS TODOS LOS USUARIOS CON EL ROL DE REDES Y LOS QUE TENGAN ID 25, 28, 49 QUE SON LOS USARIOS QUE PUEDEN TENER MATERIAL A SU CARGO (STOCK)
        $usuarios = mysqli_query($conn, "SELECT * FROM users WHERE area = 'Redes' OR user_id IN (25, 28, 49)");
        #VERIFICAMOS SI ENCONTRAMOS MAS DE UN USUARIO
        if (mysqli_num_rows($usuarios) > 0) {
          #SI ENCONTRAMOS USARIOS CREAMOS UNA TABLA CON ESTOS MISMOS
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
          #RECORREMOS UNO POR UNO LOS USUARIOS PARA MOSTRAR SU INFORMACION Y ACCEDER A CADA UNO DE ELLOS
          while($user = mysqli_fetch_array($usuarios)){
            ?>
            <tr>
              <td><?php echo $user['user_id']; ?></td>
              <td><b><?php echo $user['firstname'].' '.$user['lastname']; ?></b></td>
              <td><?php echo $user['user_name']; ?></td>
              <td><br><form action="stock_tecnico.php" method="post"><input type="hidden" name="id_tecnico" value="<?php echo $user['user_id']; ?>"><button type="submit" class="btn-floating btn-tiny waves-effect waves-light pink"><i class="material-icons">assignment_ind</i></button></form></td>
            </tr>
          <?php
          }//FIN WHILE
          ?> 
          </tbody>
        </table>
        </div>
        <?php
        }//FIN DEL IF
        ?>
    </div>
  </div><br><br>
<?php mysqli_close($conn);?>
</body>
</main>
</html>