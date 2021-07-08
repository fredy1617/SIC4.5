<?php
include('../php/conexion.php');
include('../php/superAdmin.php');

$IdPago = $conn->real_escape_string($_POST['valorIdPago']);
$dispositivo = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM pagos WHERE id_pago = '$IdPago'"));
$id_dispositivo = $dispositivo['id_cliente'];
if(mysqli_query($conn, "DELETE FROM pagos WHERE id_pago = '$IdPago'")){
    echo '<script >M.toast({html:"Pago Borrado.", classes: "rounded"})</script>';
}else{
    echo '<script >M.toast({html:"Ocurrio un error...", classes: "rounded"})</script>';
}
?>
			<table>
                <thead>
                  <th>#</th>
                  <th>Descripcion</th>
                  <th>Fecha</th>                  
                  <th>Cantidad</th>
                  <th>Borrar</th>
                </thead>
                <tbody>                 
                  <?php
                  $sql = mysqli_query($conn, "SELECT * FROM pagos WHERE id_cliente = '$id_dispositivo' AND descripcion = 'Anticipo' AND tipo = 'Dispositivo'");
                  $Total = 0;
                  if (mysqli_num_rows($sql)>0) {
                    $aux= 0;
                    
                    while ($anticipo = mysqli_fetch_array($sql)) {
                      $aux++;
                      $Total += $anticipo['cantidad'];
                      ?>
                    <tr>
                      <td><?php echo $aux; ?></td>
                      <td><?php echo $anticipo['descripcion']; ?></td>
                      <td><?php echo $anticipo['fecha']; ?></td>
                      <td>$<?php echo $anticipo['cantidad']; ?></td>
                      <td><a onclick="borrar(<?php echo $anticipo['id_pago']; ?>);" class="btn btn-floating red darken-1 waves-effect waves-light"><i class="material-icons">delete</i></a></td>
                    </tr>
                      <?php
                    }
                  }
                  ?>
                  <tr>
                  	<td></td>
                  	<td></td>
                  	<td><b>TOTAL:</b></td>
                  	<td>$<?php echo $Total; ?></td>
                  </tr>
                </tbody>
              </table>
<?php
mysqli_close($conn);
?>