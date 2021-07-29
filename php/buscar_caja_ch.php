<?php 
  include ('conexion.php');
  $De = $conn->real_escape_string($_POST['valorDe']);
  $A = $conn->real_escape_string($_POST['valorA']);

  if ($De != "" AND $A != "") {   
    //AQUI BUSCARA POR EL RANGO DE FECHAS DEFINIDO...
    $ingresos = mysqli_query($conn, "SELECT * FROM historila_caja_ch WHERE tipo = 'Ingreso' AND fecha >= '$De' AND fecha <= '$A'");
    $egresos = mysqli_query($conn, "SELECT * FROM historila_caja_ch WHERE tipo = 'Egreso' AND fecha >= '$De' AND fecha <= '$A'");
  }else{
    //ESTAS CONSULTAS SE HARAN SIEMPRE QUE NO ALLA NADA EN EL RANGO DE FECHAS...
    $ingresos = mysqli_query($conn, "SELECT * FROM historila_caja_ch WHERE tipo = 'Ingreso'");
    $egresos = mysqli_query($conn, "SELECT * FROM historila_caja_ch WHERE tipo = 'Egreso'");
  }
?>
    <div class="row" >
        <div class="col s12 m6 l6">
          <h4>Ingresos: </h4>
          <table>
            <thead>
                <tr>
                  <th>Id</th>
                  <th>Cantidad</th>
                  <th>Fecha y Hora</th>
                  <th>Descripcion</th>
                  <th>Usuario</th>
                  <th>Imprimir</th>
                  <th>Borrar</th>
                </tr>
            </thead>
            <tbody>
              <?php
              $aux = mysqli_num_rows($ingresos);
              if ($aux > 0) {
                $Total_I = 0;
                while ($ingreso = mysqli_fetch_array($ingresos)) {
                  $id_user = $ingreso['usuario'];
                  $user = mysqli_fetch_array(mysqli_query($conn, "SELECT user_name FROM users WHERE user_id = '$id_user'"));
                  $Total_I += $ingreso['cantidad'];
              ?>
                  <tr>
                    <td><b><?php echo $ingreso['id'];?></b></td>         
                    <td>$<?php echo $ingreso['cantidad'];?></td>
                    <td><?php echo $ingreso['fecha'].' '.$ingreso['hora'];?></td>
                    <td><?php echo $ingreso['descripcion'];?></td>
                    <td><?php echo $user['user_name'];?></td>
                    <td><a onclick="imprimir(<?php echo $ingreso['id']; ?>);" class="btn btn-floating pink waves-effect waves-light"><i class="material-icons">print</i></a></td>
                    <td><a onclick="borrar_caja(<?php echo $ingreso['id']; ?>);" class="btn btn-floating red darken-1 waves-effect waves-light"><i class="material-icons">delete</i></a></td>
                  </tr>
                <?php 
                }//fin while
                ?>
                  <tr>
                    <td></td>
                    <td></td>
                    <td><b>TOTAL = </b></td>
                    <td><b>$<?php echo $Total_I;?></b></td>
                    <td></td>
                    <td></td>
                    <td></td>
                  </tr>
                <?php
              }else{
                echo "<center><b><h3>No se han registrado ingresos</h3></b></center>";
              }
              ?>
            </tbody>
          </table>
        </div>
        <div class="col s12 m6 l6">
          <h4>Egresos: </h4>
          <table >
            <thead>
                <tr>
                  <th>Id</th>
                  <th>Cantidad</th>
                  <th>Fecha y Hora</th>
                  <th>Descripcion</th>
                  <th>Usuario</th>
                  <th>Imprimir</th>
                  <th>Borrar</th>
                </tr>
            </thead>
            <tbody>
              <?php
              $aux = mysqli_num_rows($egresos);
              if ($aux > 0) {
                $Total_E = 0;
                while ($egreso = mysqli_fetch_array($egresos)) {
                  $id_user = $egreso['usuario'];
                  $user = mysqli_fetch_array(mysqli_query($conn, "SELECT user_name FROM users WHERE user_id = '$id_user'"));
                  $Total_E += $egreso['cantidad'];
              ?>
                  <tr>
                    <td><b><?php echo $egreso['id'];?></b></td>         
                    <td>$<?php echo $egreso['cantidad'];?></td>
                    <td><?php echo $egreso['fecha'].' '.$egreso['hora'];?></td>
                    <td><?php echo $egreso['descripcion'];?></td>
                    <td><?php echo $user['user_name'];?></td>
                    <td><a onclick="imprimir(<?php echo $egreso['id']; ?>);" class="btn btn-floating pink waves-effect waves-light"><i class="material-icons">print</i></a></td>
                    <td><a onclick="borrar_caja(<?php echo $egreso['id']; ?>);" class="btn btn-floating red darken-1 waves-effect waves-light"><i class="material-icons">delete</i></a></td>
                  </tr>
                <?php 
                }//fin while
                ?>
                  <tr>
                    <td></td>
                    <td></td>
                    <td><b>TOTAL = </b></td>
                    <td><b>$<?php echo $Total_E;?></b></td>
                    <td></td>
                    <td></td>
                    <td></td>
                  </tr>
                <?php
              }else{
                echo "<center><b><h3>No se han registrado egresos</h3></b></center>";
              }
              ?>
            </tbody>
          </table>
        </div>
      </div>