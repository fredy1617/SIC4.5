<?php
session_start();
include('../php/conexion.php');
date_default_timezone_set('America/Mexico_City');
$Hora = date('H:i:s');
$Tipo_Campio = $conn->real_escape_string($_POST['valorTipo_Campio']);
$Tipo = $conn->real_escape_string($_POST['valorTipo']);
$Cantidad = $conn->real_escape_string($_POST['valorCantidad']);
$Descripcion = $conn->real_escape_string($_POST['valorDescripcion']);
$IdCliente = $conn->real_escape_string($_POST['valorIdCliente']);
$id_user = $_SESSION['user_id'];
$ReferenciaB = $conn->real_escape_string($_POST['valorRef']);
$Respuesta = $conn->real_escape_string($_POST['valorRespuesta']);
$entra = 'No';
if ($Respuesta == 'Ver') {
    $sql_DEUDAS = mysqli_query($conn, "SELECT * FROM deudas WHERE liquidada = 0 AND id_cliente = '$IdCliente'");
    if (mysqli_num_rows($sql_DEUDAS)>0) {
      ?>
      <script>
        $(document).ready(function(){
          $('#mostrarmodal').modal();
          $('#mostrarmodal').modal('open'); 
        });
      </script>
      <!-- Modal Structure -->
      <div id="mostrarmodal" class="modal">
        <div class="modal-content">
          <h4 class="red-text center">! Advertencia !</h4>
          <p>
          <h6 class="blue-text"><b>FAVOR DE PAGAR TIENE DEUDA(s):</b></h6><br>
          <table>
            <thead>
              <th>Descripción</th>
              <th>Fecha</th>
              <th>Cantidad</th>
              <th>Registró</th>
            </thead>
            <tbody>
          <?php
          $total=0;
          while ($deuda = mysqli_fetch_array($sql_DEUDAS)) {
            $id_userd = $deuda['usuario'];
            $user = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM users WHERE user_id = $id_userd"));
             ?>
             <tr>
               <td><?php echo $deuda['descripcion']; ?></td>
               <td><?php echo $deuda['fecha_deuda']; ?></td>
               <td>$<?php echo $deuda['cantidad']; ?></td>
               <td><?php echo $user['firstname']; ?></td>
             </tr>
             <?php 
             $total += $deuda['cantidad'];          
          }
          ?>
              <tr>
                <td></td><td><b>TOTAL:</b></td>
                <td><b>$<?php echo $total; ?></b></td><td></td>
              </tr>
            </tbody>
          </table><br><br>
          <h6 class="red-text"><b>Para resolver cualquier duda favor de marcar a oficinal al 433 935 6286 y 433 935 6288.</b></h6>
          </p>
        </div>
        <div class="modal-footer row">
          <?php 
          $rol = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM users WHERE user_id = $id_user"));
          if ($rol['area'] == 'Administrador') {
            
          ?>
          <form method="post" action="../views/otros_pagos.php">
            <input id="resp" name="resp" type="hidden" value="Si">
            <input id="no_cliente" name="no_cliente" type="hidden" value="<?php echo $IdCliente;?>">
            <button class="btn waves-effect red accent-4 waves-light" type="submit" name="action"><b>Registrar</b></button>
          </form>
         <?php  } ?>
          <form method="post" action="../views/credito.php">
            <input id="no_cliente" name="no_cliente" type="hidden" value="<?php echo $IdCliente;?>">
            <button class="btn waves-effect green accent-4 waves-light" type="submit" name="action"><b>Liquidar</b></button>
          </form>
          <form action="../views/clientes.php">
            <button class="btn waves-effect waves-light" type="submit" name="action"><b>Cancelar</b></button>
          </form><br>
        </div>
      </div>
      <?php
        echo '<script>M.toast({html:"Este cliente tiene deudas.", classes: "rounded"})</script>';
    }else {
      $entra = "Si";
    }
}else{
  $entra = $Respuesta;
}

if ($entra == "Si") {
  $Cotejamiento = 0;
  $Fecha_hoy = date('Y-m-d');

  if(mysqli_num_rows(mysqli_query($conn, "SELECT * FROM pagos WHERE id_cliente = $IdCliente AND descripcion = '$Descripcion' AND cantidad='$Cantidad' AND fecha='$Fecha_hoy'"))>0){
    echo '<script>M.toast({html:"Ya se encuentra un pago registrado con los mismos valores el día de hoy.", classes: "rounded"})</script>';
  }else{
  $sql = "INSERT INTO pagos (id_cliente, descripcion, cantidad, fecha, hora, tipo, id_user, corte, corteP, tipo_cambio, Cotejado) VALUES ($IdCliente, '$Descripcion', '$Cantidad', '$Fecha_hoy', '$Hora', '$Tipo', $id_user, 0, 0, '$Tipo_Campio', '$Cotejamiento')";
  if ($Tipo_Campio == "Credito") {
    $mysql= "INSERT INTO deudas(id_cliente, cantidad, fecha_deuda, tipo, descripcion, usuario) VALUES ($IdCliente, '$Cantidad', '$Fecha_hoy', '$Tipo', '$Descripcion', $id_user)";
    mysqli_query($conn,$mysql);
    $ultimo =  mysqli_fetch_array(mysqli_query($conn, "SELECT MAX(id_deuda) AS id FROM deudas WHERE id_cliente = $IdCliente"));            
    $id_deuda = $ultimo['id'];
    $sql = "INSERT INTO pagos (id_cliente, descripcion, cantidad, fecha, hora, tipo, id_user, corte, corteP, tipo_cambio, id_deuda, Cotejado) VALUES ($IdCliente, '$Descripcion', '$Cantidad', '$Fecha_hoy', '$Hora', '$Tipo', $id_user, 0, 0, '$Tipo_Campio', $id_deuda, '$Cotejamiento')";
  }
  //o $consultaBusqueda sea igual a nombre + (espacio) + apellido

  if(mysqli_query($conn, $sql)){
    echo '<script>M.toast({html:"El pago se dió de alta satisfcatoriamente.", classes: "rounded"})</script>';
    $ultimo =  mysqli_fetch_array(mysqli_query($conn, "SELECT MAX(id_pago) AS id FROM pagos WHERE id_cliente = $IdCliente"));            
    $id_pago = $ultimo['id'];
    // Si el pago es de banco guardar la referencia....
    if (($Tipo_Campio == 'Banco' OR $Tipo_Campio == 'SAN') AND $ReferenciaB != '') {
      mysqli_query($conn,  "INSERT INTO referencias (id_pago, descripcion) VALUES ('$id_pago', '$ReferenciaB')");
    }
    if (strpos($Descripcion, 'AUMENTAR PAQUETE') !== false) {
      if (mysqli_query($conn,"INSERT INTO reportes(id_cliente, descripcion, fecha, registro) VALUES ($IdCliente, '$Descripcion', '$Fecha_hoy', $id_user)")) {
          echo '<script>M.toast({html:"Se registro el reporte (AUMENTAR)", classes: "rounded"})</script>';
      }
    }
    ?>
    <script>
    id_pago = <?php echo $id_pago; ?>;
    var a = document.createElement("a");
        a.target = "_blank";
        a.href = "../php/imprimir.php?IdPago="+id_pago;
        a.click();
    </script>
    <?php  
  }else{
    echo  '<script>M.toast({html:"Ha ocurrido un error.", classes: "rounded"})</script>';  
    }
  }
  ?>
    <div id="mostrar_pagos">
      <table class="bordered highlight responsive-table">
      <thead>
        <tr>
          <th>#</th>
          <th>Cantidad</th>
          <th>Tipo</th>
          <th>Descripción</th>
          <th>Usuario</th>
          <th>Fecha</th>
          <th>Imprimir</th>
          <th>Borrar</th>
        </tr>
      </thead>
      <tbody>
      <?php
      $sql_pagos = "SELECT * FROM pagos WHERE id_cliente = '$IdCliente' && tipo = 'Otros Pagos' ORDER BY id_pago DESC";
      $resultado_pagos = mysqli_query($conn, $sql_pagos);
      $aux = mysqli_num_rows($resultado_pagos);
      if($aux>0){
      while($pagos = mysqli_fetch_array($resultado_pagos)){
        $id_user = $pagos['id_user'];
        $user = mysqli_fetch_array(mysqli_query($conn, "SELECT user_name FROM users WHERE user_id = '$id_user'"));
      ?>
        <tr>
          <td><b><?php echo $aux;?></b></td>
          <td>$<?php echo $pagos['cantidad'];?></td>
          <td><?php echo $pagos['tipo'];?></td>
          <td><?php echo $pagos['descripcion'];?></td>
          <td><?php echo $user['user_name'];?></td>
          <td><?php echo $pagos['fecha'].' '.$pagos['hora'];?></td>
          <td><a onclick="imprimir(<?php echo $pagos['id_pago'];?>);" class="btn btn-floating pink waves-effect waves-light"><i class="material-icons">print</i></a>
          </td>
          <td><a onclick="borrar(<?php echo $pagos['id_pago'];?>);" class="btn btn-floating red darken-1 waves-effect waves-light"><i class="material-icons">delete</i></a>
          </td>
        </tr>
      <?php
      $aux--;
      }
      }else{
        echo "<center><b><h3>Este cliente aún no ha registrado pagos</h3></b></center>";
      }
      ?>          
      </tbody>
    </table>
    </div>
<?php 
}
mysqli_close($conn);
?>