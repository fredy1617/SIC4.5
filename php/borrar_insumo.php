<?php
include('../php/conexion.php');
include('../php/superAdmin.php');

$id = $conn->real_escape_string($_POST['valorId']);
$Unidad = $conn->real_escape_string($_POST['valorUnidad']);

if(mysqli_query($conn, "DELETE FROM insumos WHERE id = '$id'")){
    echo '<script >M.toast({html:"Insumo Borrado...", classes: "rounded"})</script>';	
}else{
    echo '<script >M.toast({html:"Ocurrio un error...", classes: "rounded"})</script>';
}
?>
    <div class="row" id="resultado_update_insumo">
        <h3>Historial</h3>
        <div class="col s2"></div>
        <div class="row col s8">
          <!--CREAMOS UNA TABLA QUE MUESTRA EL HISTORIAL DE LOS MANTENIMIENTOS O INSUMOS -->
          <table class="bordered highlight responsive-table">
            <thead>
              <th>#</th>
              <th>Descripcion</th>
              <th>Fecha</th>
              <th>Registro</th>
              <th>Borrar</th>
            </thead>
            <tbody>
            <?php
            #SELECCIONAMOS TODOS LOS ROUTERS Y ANTENAS DISPONIBLES EN STOCK
            $tab = mysqli_query($conn, "SELECT * FROM insumos WHERE vehiculo = '$Unidad'");
            #SI TIENE DISPONIBLES LA RECORREMOS UNA POR UNA Y MOSTRAMOS LA INFORMACION
            while($insumo = mysqli_fetch_array($tab)){
              ?>
              <tr>
                <td><?php echo $insumo['id']; ?></td>
                <td><?php echo $insumo['descripcion']?></td>
                <td><?php echo $insumo['fecha']; ?></td>
                <td><?php echo $insumo['registro']; ?></td>
                <td><a onclick="borrar(<?php echo $insumo['id'];?>);" class="btn btn-floating red darken-1 waves-effect waves-light"><i class="material-icons">delete</i></td>
              </tr>
            <?php
            }
            ?> 
            </tbody>
          </table>
        </div>
    </div>  