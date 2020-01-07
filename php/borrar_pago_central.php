<?php
include('../php/is_logged.php');
include('../php/conexion.php');
include('../php/superAdmin.php');

$IdCentral = $conn->real_escape_string($_POST['valorIdCentral']);
$IdPago = $conn->real_escape_string($_POST['valorIdPago']);
$pago =mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM pagos_centrales WHERE id='$IdPago'"));
$Tipo = $pago['tipo'];
if(mysqli_query($conn, "DELETE FROM pagos_centrales WHERE id = '$IdPago'")){
    echo '<script >M.toast({html:"Pago Borrado..", classes: "rounded"})</script>';
    $central =mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM centrales WHERE id='$IdCentral'"));
    $fecha_vencimiento = $central['vencimiento_renta'];
    if ($Tipo == 'Anual') {
      $AÑO = strtotime('-1 year', strtotime($fecha_vencimiento));
      $Vencimiento = date('Y-m-01', $AÑO);
    }else{
      $AÑO = strtotime('-1 month', strtotime($fecha_vencimiento));
      $Vencimiento = date('Y-m-01', $AÑO);
    }
    mysqli_query($conn, "UPDATE centrales SET vencimiento_renta='$Vencimiento'WHERE id='$IdCentral'");
}else{
    echo '<script >M.toast({html:"Ocurrio un error...", classes: "rounded"})</script>';
}
?>
     <table class="bordered highlight responsive-table">
          <thead>
            <tr>
              <th>#</th>
              <th>Cantidad</th>
              <th>Tipo</th>
              <th>Descripción</th>
              <th>Usuario</th>
              <th>Fecha</th>
              <!--<th>Imprimir</th>-->
              <th>Borrar</th>
            </tr>
          </thead>
          <tbody>
          <?php
          $sql_pagos = "SELECT * FROM pagos_centrales WHERE id_central = '$IdCentral' ORDER BY id DESC";
          $resultado_pagos = mysqli_query($conn, $sql_pagos);
          $aux = mysqli_num_rows($resultado_pagos);
          if($aux>0){
          while($pagos = mysqli_fetch_array($resultado_pagos)){
            $id_user = $pagos['usuario'];
            $user = mysqli_fetch_array(mysqli_query($conn, "SELECT user_name FROM users WHERE user_id = '$id_user'"));
          ?>
            <tr>
              <td><b><?php echo $aux;?></b></td>
              <td>$<?php echo $pagos['cantidad'];?></td>
              <td><?php echo $pagos['tipo'];?></td>
              <td><?php echo $pagos['descripcion'];?></td>
              <td><?php echo $user['user_name'];?></td>
              <td><?php echo $pagos['fecha'];?></td>
              <!--<td><a onclick="imprimir(<?php echo $pagos['id'];?>);" class="btn btn-floating pink waves-effect waves-light"><i class="material-icons">print</i></a></td>-->
              <td><a onclick="borrar(<?php echo $pagos['id'];?>);" class="btn btn-floating red darken-1 waves-effect waves-light"><i class="material-icons">delete</i></a></td>
            </tr>
            <?php
            $aux--;
            }//Fin while
            }else{
            echo "<center><b><h5 class = 'red-text'>Esta central aún no ha registrado pagos</h5 ></b></center>";
          }
          ?> 
          </tbody>
      </table>