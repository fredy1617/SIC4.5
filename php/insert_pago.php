<?php
session_start();
include('../php/conexion.php');
date_default_timezone_set('America/Mexico_City');
$id_user = $_SESSION['user_id'];

$Promo = $conn->real_escape_string($_POST['valorPromo']);
$Tipo_Campio = $conn->real_escape_string($_POST['valorTipo_Campio']);
$Tipo = $conn->real_escape_string($_POST['valorTipo']);
$Cantidad = $conn->real_escape_string($_POST['valorCantidad']);
$Descripcion = $conn->real_escape_string($_POST['valorDescripcion']);
$IdCliente = $conn->real_escape_string($_POST['valorIdCliente']);
$Descuento = $conn->real_escape_string($_POST['valorDescuento']);
$Hasta = $conn->real_escape_string($_POST['valorHasta']);
$Respuesta = $conn->real_escape_string($_POST['valorRespuesta']);
$tabla = '';
$tabla1 = '';
$tabla2 = '';
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
          <form method="post" action="../views/crear_pago.php">
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
  if ($Descuento == "") {
    $Descuento = 0;
  }
  $RegistrarCan = $Cantidad-$Descuento;

  if ($Tipo == 'Mensualidad'){
    $TelTipo = '';
    $Cotejamiento = 0;
  }elseif($Tipo == 'Otros Pagos'){
    $TelTipo = '';
    $Cotejamiento = 0;
  }else {
    $TelTipo = $conn->real_escape_string($_POST['valorTipoTel']);
    $Tipo = $TelTipo;
    $Cotejamiento = 1;
  }
  $fecha_corte = mysqli_fetch_array(mysqli_query($conn, 'SELECT * FROM clientes WHERE id_cliente='.$IdCliente));
  $Fecha_db = $fecha_corte['fecha_corte'];
  $Fecha_hoy = date('Y-m-d');

  if($Fecha_hoy<=$Fecha_db){
    $Fecha = $fecha_corte['fecha_corte'];
  }else{
    $Fecha = date('Y-m-d');  
  }

  if($Promo == 'si'){
    $nuevafecha = strtotime('+12 month', strtotime($Fecha));
    $FechaCorte = date('Y-m-05', $nuevafecha);
  }else{
    $nuevafecha = strtotime('+1 month', strtotime($Fecha));
    $dia =date('d');
    if ($dia > 23) {
      $nuevafecha = strtotime('+2 month', strtotime($Fecha));
    }
    $FechaCorte = date('Y-m-05', $nuevafecha);
  }

  $cambia_fecha = $FechaCorte;

  //Variable vacía (para evitar los E_NOTICE)

  $NDesc = explode(" ", $Descripcion);
  $ver = $NDesc[0].' '.$NDesc[1];
  $sql_ver = mysqli_query($conn, "SELECT * FROM pagos WHERE id_cliente = $IdCliente AND descripcion like '%$ver%' AND tipo = 'Mensualidad'");
  if(mysqli_num_rows($sql_ver)>0){
    echo '<script>M.toast({html:"Ya se encuentra un pago del mismo mes y mismo año.", classes: "rounded"})</script>';
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
          <h6 class="red-text center"><b>NO SE REISTRO EL PAGO YA QUE YA SE REGISTRO UN PAGO DEL MISMO MES:</b></h6>
          <table class="bordered highlight responsive-table  " id="mostrar_pagos">
            <thead>
              <tr>
                <th>Id_Cliente</th>
                <th>Fecha</th>
                <th>Descripción</th>
                <th>Regitró</th>
              </tr>
            </thead>
            <tbody>
            <?php
            $aux = mysqli_num_rows($sql_ver);
            if($aux>0){
              $pago = mysqli_fetch_array ($sql_ver);
              $id = $pago['id_user'];
              $user = mysqli_fetch_array(mysqli_query($conn, "SELECT * from users WHERE user_id = '$id'"));
              ?>
              <tr>
                <td><b><?php echo $IdCliente;?></b></td>
                <td><?php echo $pago['fecha'];?></td>
                <td><?php echo $pago['descripcion'];?></td>
                <td><?php echo $user['firstname'];?></td>
              </tr>
              <?php
            }else{
              echo "<center><b><h5>Este cliente aún no ha registrado reportes</h5></b></center>";
            }
            ?>        
          </tbody>
        </table><br>
        <h6 class="blue-text"><b>Pago No Registrado:</b></h6>
        <table class="bordered highlight responsive-table ">
            <thead>
              <tr>
                <th>Id_Cliente</th>
                <th>Fecha</th>
                <th>Descripción</th>
                <th>Regitró</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td><?php echo $IdCliente; ?></td>
                <td><?php echo $Fecha_hoy; ?></td>
                <td><?php echo $Descripcion; ?></td>
                <td><?php  $user = mysqli_fetch_array(mysqli_query($conn, "SELECT * from users WHERE user_id = '$id_user'")); echo $user['firstname']; ?></td>
              </tr>
            </tbody>
        </table>
        </p>
      </div>
      <div class="modal-footer">
        <form method="post" action="../views/crear_pago.php"><input id="no_cliente" name="no_cliente" type="hidden" value="<?php echo $IdCliente ?>"><button class="btn waves-effect red accent-2 waves-light" type="submit" name="action">
        <b>Aceptar</b>
        </button></form>
      </div>
    </div>
    <?php
  }else{
  $sql = "INSERT INTO pagos (id_cliente, descripcion, cantidad, fecha, tipo, id_user, corte, tipo_cambio, Cotejado) VALUES ($IdCliente, '$Descripcion', '$RegistrarCan', '$Fecha_hoy', '$Tipo', $id_user, 0, '$Tipo_Campio', '$Cotejamiento')";
  if ($Tipo_Campio == "Credito") {
    $mysql= "INSERT INTO deudas(id_cliente, cantidad, fecha_deuda, hasta, tipo, descripcion, usuario) VALUES ($IdCliente, '$RegistrarCan', '$Fecha_hoy', '$Hasta', '$Tipo', '$Descripcion', $id_user)";
    if ($Hasta == "") {
     $mysql = "INSERT INTO deudas(id_cliente, cantidad, fecha_deuda, tipo, descripcion, usuario) VALUES ($IdCliente, '$RegistrarCan', '$Fecha_hoy', '$Tipo', '$Descripcion', $id_user)";
    }
    mysqli_query($conn,$mysql);
    $ultimo =  mysqli_fetch_array(mysqli_query($conn, "SELECT MAX(id_deuda) AS id FROM deudas WHERE id_cliente = $IdCliente"));            
    $id_deuda = $ultimo['id'];
    $sql = "INSERT INTO pagos (id_cliente, descripcion, cantidad, fecha, tipo, id_user, corte, tipo_cambio, id_deuda, Cotejado) VALUES ($IdCliente, '$Descripcion', '$RegistrarCan', '$Fecha_hoy', '$Tipo', $id_user, 0, '$Tipo_Campio', $id_deuda, '$Cotejamiento')";
  }
   
  //o $consultaBusqueda sea igual a nombre + (espacio) + apellido

  if(mysqli_query($conn, $sql)){
    echo '<script>M.toast({html:"El pago se dió de alta satisfcatoriamente.", classes: "rounded"})</script>';
    $cliente = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM clientes WHERE id_cliente = $IdCliente"));
    if($cliente['fecha_corte']<$FechaCorte){
        mysqli_query($conn, "UPDATE clientes SET fecha_corte='$FechaCorte' WHERE id_cliente='$IdCliente'");
      }
      $id_mensualidad=$cliente['paquete'];
      $mensualidad = mysqli_fetch_array(mysqli_query($conn, "SELECT mensualidad FROM paquetes WHERE id_paquete='$id_mensualidad'"));
      $dif = $mensualidad['mensualidad']-$Cantidad;
      if ($Cantidad == ($mensualidad['mensualidad']*10)) {
      }else{
      if ($dif < -50) {
        $Descrip = "AUMENTAR PAQUETE pago: ".$Cantidad." por: ".$Descripcion;
        if (mysqli_query($conn,"INSERT INTO reportes(id_cliente, descripcion, fecha) VALUES ($IdCliente, '$Descrip', '$Fecha_hoy')")) {
          echo '<script>M.toast({html:"Se registro el reporte (AUMENTAR)", classes: "rounded"})</script>';
        }
      }elseif ($dif > 0) {
        $Descrip = "DISMINUIR PAQUETE pago: ".$Cantidad." por: ".$Descripcion;
        if (mysqli_query($conn,"INSERT INTO reportes(id_cliente, descripcion, fecha) VALUES ($IdCliente, '$Descrip', '$Fecha_hoy')")) {
          echo '<script>M.toast({html:"Se registro el reporte (DISMINUIR)", classes: "rounded"})</script>';
        }
      }
      }    
    ?>
    <script>
      id_cliente = <?php echo $IdCliente; ?>;
      var a = document.createElement("a");
        a.target = "_blank";
        a.href = "../php/activar_pago.php?id="+id_cliente;
        a.click();
    </script>
    <?php   
  }else{
    echo '<script>M.toast({html:"Ha ocurrido un error.", classes: "rounded"})</script>';  
    }
  }
  ?>
  <div id="tabla">
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
    $sql_pagos = "SELECT * FROM pagos WHERE id_cliente = ".$IdCliente." ORDER BY id_pago DESC";
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
        <td><?php echo $pagos['fecha'];?></td>
        <td><a onclick="imprimir(<?php echo $pagos['id_pago'];?>);" class="btn btn-floating pink waves-effect waves-light"><i class="material-icons">print</i></a>
        </td>
        <td><a onclick="borrar(<?php echo $pagos['id_pago'];?>);" class="btn btn-floating red darken-4 waves-effect waves-light"><i class="material-icons">delete</i></a>
        </td>
      </tr>
    <?php
    $aux--;
    }//fin while
    }else{
      echo "<center><b><h3>Este cliente aún no ha registrado pagos</h3></b></center>";
    }
    ?>        
    </tbody>
   </table>
  </div>
<?php
}
echo $tabla;
echo $tabla1;
echo $tabla2;
mysqli_close($conn);
?>