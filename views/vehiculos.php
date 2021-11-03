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
<title>SIC | Vehiculos</title>
</head>
<main>
<body>
	<div class="container">
    <div><br><br><br>
      <h3 class="row"><b>Vehiculos:</b></h3><br>
      <a class="waves-effect waves-light btn pink right" href="form_vehiculo.php">Agregar<i class="material-icons right">add</i></a>
      <div class="row">
        <?php
        #SELECCIONAMOS TODAS LAS UNIDADES
        $Unidades = mysqli_query($conn, "SELECT * FROM unidades");
        #VERIFICAMOS SI ENCONTRAMOS MAS DE UNA UNIDAD
        if (mysqli_num_rows($Unidades) > 0) {
          #SI ENCONTRAMOS UNIDADES CREAMOS UNA TABLA CON ESTOS MISMOS
        ?>        
        <div class="row">
        <table class="bordered highlight responsive-table">
          <thead>
            <th>#</th>
            <th>Nombre</th>
            <th>Descripcion</th>
            <th>Responsable</th>
            <th>Accion</th>
          </thead>
          <tbody>
          <?php
          #RECORREMOS UNO POR UNO LAS UNIDADES PARA MOSTRAR SU INFORMACION Y ACCEDER A CADA UNO DE ELLOS
          while($unidad = mysqli_fetch_array($Unidades)){
            ?>
            <tr>
              <td><?php echo $unidad['id']; ?></td>
              <td><?php echo $unidad['nombre']; ?></td>
              <td><?php echo $unidad['descripcion']; ?></td>
              <td><?php echo $unidad['responsable']; ?></td>
              <td><br><form action="mantenimiento_vehiculo.php" method="post"><input type="hidden" name="id" value="<?php echo $unidad['id']; ?>"><button type="submit" class="btn-floating btn-tiny waves-effect waves-light pink"><i class="material-icons">build</i></button></form></td>
            </tr>
          <?php
          }//FIN WHILE
          ?> 
          </tbody>
        </table>
        </div>
        <?php
        }//FIN DEL IF
        else{
          echo '<h4>No se encontraron Unidades</h4>';
        }
        ?>
    </div>
  </div><br><br>
<?php mysqli_close($conn);?>
</body>
</main>
</html>