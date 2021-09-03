<script >
  function insert_pago2(caso) {  
    var textoIdCliente = $("input#valorIdCliente").val();
    var textoTipo_Campio = $("input#valorTipo_Campio").val();
    var textoCantidad = $("input#valorCantidad").val();
    var textoDescripcion = $("input#valorDescripcion").val();
    var textoFecha = $("input#valorFechaCorte").val();
    if (caso == 2) {
      var textoFecha = $("input#valorFechaCorte2").val();
      var textoCantidad2 = $("input#valorCantidad2").val();
      var textoDescripcion2 = $("input#valorDescripcion2").val();
    }
    $.post("../php/diasRegistroNormal.php" , { 
      valorTipo_Campio: textoTipo_Campio,
      valorCantidad: textoCantidad,
      valorDescripcion: textoDescripcion,
      valorIdCliente: textoIdCliente,
      valorFechaCorte: textoFecha,
      valorCantidad2: textoCantidad2,
      valorDescripcion2: textoDescripcion2,
      caso: caso
    }, function(mensaje) {
      $("#tabla2").html(mensaje);
    });     
  };
</script>
<?php
session_start();
include('../php/conexion.php');
date_default_timezone_set('America/Mexico_City');
$id_user = $_SESSION['user_id'];
$Fecha_hoy = date('Y-m-d');
$Hora = date('H:i:s');

$Promo = $conn->real_escape_string($_POST['valorPromo']);
$Tipo_Campio = $conn->real_escape_string($_POST['valorTipo_Campio']);
$Tipo = $conn->real_escape_string($_POST['valorTipo']);
$Cantidad = $conn->real_escape_string($_POST['valorCantidad']);
$Descripcion = $conn->real_escape_string($_POST['valorDescripcion']);
$IdCliente = $conn->real_escape_string($_POST['valorIdCliente']);
$Descuento = $conn->real_escape_string($_POST['valorDescuento']);
$Hasta = $conn->real_escape_string($_POST['valorHasta']);
$ReferenciaB = $conn->real_escape_string($_POST['valorRef']);
$Respuesta = $conn->real_escape_string($_POST['valorRespuesta']);
$entra = 'No';
if ($Respuesta == 'Ver') {
    $sql_DEUDAS = mysqli_query($conn, "SELECT * FROM deudas WHERE liquidada = 0 AND id_cliente = '$IdCliente'");
    $sql_Abono = mysqli_query($conn, "SELECT * FROM pagos WHERE tipo = 'Abono' AND fecha = '$Fecha_hoy' AND id_cliente = '$IdCliente'");
    if (mysqli_num_rows($sql_DEUDAS)>0 AND mysqli_num_rows($sql_Abono) == 0) {
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
          <form method="post" action="../views/pagos_internet.php">
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
  $Mes = $conn->real_escape_string($_POST['valorMes']);
  $Año = $conn->real_escape_string($_POST['valorAño']);
  $ver = $Mes.' '.$Año;
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
        <form method="post" action="../views/pagos_internet.php"><input id="no_cliente" name="no_cliente" type="hidden" value="<?php echo $IdCliente ?>"><button class="btn waves-effect red accent-2 waves-light" type="submit" name="action">
        <b>Aceptar</b>
        </button></form>
      </div>
    </div>
    <?php
  }else{
    #ARREGLO EL CUAL DEFINE EL MES SIGUIENTE PARA LA FECHA DE CORTE DEL CLIENTE
    $array =  array('ENERO' => '02','FEBRERO' => '03', 'MARZO' => '04','ABRIL' => '05', 'MAYO' => '06', 'JUNIO' => '07', 'JULIO' => '08', 'AGOSTO' => '09', 'SEPTIEMBRE' => '10', 'OCTUBRE' => '11', 'NOVIEMBRE' => '12',  'DICIEMBRE' => '01');      
    $N_Mes = $array[$Mes];
    #-- AQUI TOMAMOS EL MES EN TURNO PARA COMPARAR
    $mes_sig = ['ENERO' ,'FEBRERO' , 'MARZO' ,'ABRIL' , 'MAYO' , 'JUNIO' , 'JULIO' , 'AGOSTO' , 'SEPTIEMBRE' , 'OCTUBRE' , 'NOVIEMBRE' ,  'DICIEMBRE' ][$N_Mes-1];
    #COMO ES DICIEMBRE ADELANTAMOS UN AÑO PORQUE LA FECHA DE CORTE YA ES EL SIGUINETE AÑO PAGO TODO DICIEMBRE
    if ($Mes == 'DICIEMBRE') {  $Año ++; }
    #FECHA DE CORTE SEGUN EL MES Y AÑO SELECCIONADO
    $FechaCorte = date($Año.'-'.$N_Mes.'-05'); 

    $cliente = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM clientes WHERE id_cliente = $IdCliente"));
    if ($Descuento == "") {
      $Descuento = 0;
    }
    $RegistrarCan = $Cantidad-$Descuento;//CANTIDAD A REGISTRAR EL PAGO SI HAY DESCUENTO SE LE RESTA 
    $id_mensualidad=$cliente['paquete'];
    $mensualidad = mysqli_fetch_array(mysqli_query($conn, "SELECT mensualidad FROM paquetes WHERE id_paquete='$id_mensualidad'"));
    $Tercio = ($mensualidad['mensualidad']/3);
    #VAMOS A COMPARAR EL TERCIO DE LA ACNTIDAD A PAGAR MAS $50 PARA VER SI ESTA PAGANDO MENOS DE 10 DIAS
    if (($mensualidad['mensualidad']-$Descuento)<=$Tercio) {
      #SI PAGA MENOS DEL EQUIOVALENTE A 10 DIAS MOSTRAR MODAL Y HACER ACCIONES
      $Descripcion2 = $mes_sig.' '.$Año;
      $N_Mes_Sig = $array[$mes_sig];
      if ($mes_sig == 'DICIEMBRE') {  $Año ++; }
      #FECHA DE CORTE SI ES HASTA EL MES SIGUIENTE
      $FechaCorte2 = date($Año.'-'.$N_Mes_Sig.'-05');
      $Cantidad2 = $mensualidad['mensualidad'];// ($Cantidad-50)+($Cantidad-50)%100;
      echo '<script>M.toast({html:"CANTIDAD A REGISTRAR MENOR AL EQUIVALENTE DE 10 DIAS.", classes: "rounded"})</script>';
      ?>
      <script>
       $(document).ready(function(){
          $('#modalDias').modal();
          $('#modalDias').modal('open'); 
       });
      </script>
      <!-- Modal Structure -->
      <div id="modalDias" class="modal">
        <div class="modal-content">
          <h4 class="red-text center">! Advertencia !</h4> <br>
          <p>
            <h6 class="blue-text center"><b>¡LA CANTIDAD A REGISTRAR ES IGUAL O MENOR AL EQUIVALENTE A PAGAR 10 DIAS DE INTERNET!</b></h6>            <br>
            <h6 class="blue-text center"><b>¡SE LE RECOMIENDA REGISTRAR EL MES SIGUIENTE PARA EVITAR CORTES DE SERVICO!</b></h6>            <br>
          </p>
        </div>
        <div class="modal-footer row">
          <form id="tabla2">
            <input id="valorIdCliente" name="valorIdCliente" type="hidden" value="<?php echo $IdCliente;?>">
            <input id="valorTipo_Campio" name="valorTipo_Campio" type="hidden" value="<?php echo $Tipo_Campio;?>">
            <input id="valorCantidad" name="valorCantidad" type="hidden" value="<?php echo $RegistrarCan;?>">
            <input id="valorDescripcion" name="valorDescripcion" type="hidden" value="<?php echo $Descripcion;?>">
            <input id="valorFechaCorte" name="valorFechaCorte" type="hidden" value="<?php echo $FechaCorte;?>">
            <input id="valorFechaCorte2" name="valorFechaCorte2" type="hidden" value="<?php echo $FechaCorte2;?>">
            <input id="valorCantidad2" name="valorCantidad2" type="hidden" value="<?php echo $Cantidad2;?>">
            <input id="valorDescripcion2" name="valorDescripcion2" type="hidden" value="<?php echo $Descripcion2;?>">
            <button class="btn waves-effect green accent-4 waves-light" onclick="insert_pago2(2)"><b>REGISTRAR <?php echo $mes_sig;?></b></button>
            <button class="btn waves-effect waves-light" onclick="insert_pago2(1);"><b>REGISTRAR SOLO <?php echo $Mes;?></b></button>
          </form><br>
        </div>
      </div>
    <?php
    }else{
      #SI EL PAGO ES NORMAL REGISTRAR     
       
      #--- CREAMOS EL SQL PARA LA INSERCION ---
      $sql = "INSERT INTO pagos (id_cliente, descripcion, cantidad, fecha, hora, tipo, id_user, corte, corteP, tipo_cambio, Cotejado) VALUES ($IdCliente, '$Descripcion', '$RegistrarCan', '$Fecha_hoy', '$Hora', '$Tipo', $id_user, 0, 0, '$Tipo_Campio', 0)";
      if ($Tipo_Campio == "Credito") {
        $mysql= "INSERT INTO deudas(id_cliente, cantidad, fecha_deuda, hasta, tipo, descripcion, usuario) VALUES ($IdCliente, '$RegistrarCan', '$Fecha_hoy', '$Hasta', '$Tipo', '$Descripcion', $id_user)";
        if ($Hasta == "") {
         $mysql = "INSERT INTO deudas(id_cliente, cantidad, fecha_deuda, tipo, descripcion, usuario) VALUES ($IdCliente, '$RegistrarCan', '$Fecha_hoy', '$Tipo', '$Descripcion', $id_user)";
        }
        mysqli_query($conn,$mysql);
        $ultimo =  mysqli_fetch_array(mysqli_query($conn, "SELECT MAX(id_deuda) AS id FROM deudas WHERE id_cliente = $IdCliente"));            
        $id_deuda = $ultimo['id'];
        $sql = "INSERT INTO pagos (id_cliente, descripcion, cantidad, fecha, hora, tipo, id_user, corte, corteP, tipo_cambio, id_deuda, Cotejado) VALUES ($IdCliente, '$Descripcion', '$RegistrarCan', '$Fecha_hoy', '$Hora', '$Tipo', $id_user, 0, 0, '$Tipo_Campio', $id_deuda, 0)";
      }
     
      #--- SE INSERTA EL PAGO -----------
      if(mysqli_query($conn, $sql)){
        echo '<script>M.toast({html:"El pago se dió de alta satisfcatoriamente.", classes: "rounded"})</script>';
        // Si el pago es de banco guardar la referencia....
        if (($Tipo_Campio == 'Banco' OR $Tipo_Campio == 'SAN') AND $ReferenciaB != '') {
          $ultimoPago =  mysqli_fetch_array(mysqli_query($conn, "SELECT MAX(id_pago) AS id FROM pagos WHERE id_cliente = $IdCliente"));            
          $id_pago = $ultimoPago['id'];
          mysqli_query($conn,  "INSERT INTO referencias (id_pago, descripcion) VALUES ('$id_pago', '$ReferenciaB')");
        }
        #ACTUALIZAMOS LA FECHA DE CORTE   --- IMPORTANTE----
        mysqli_query($conn, "UPDATE clientes SET fecha_corte='$FechaCorte' WHERE id_cliente='$IdCliente'");

        $dif = $mensualidad['mensualidad']-$Cantidad;
        #VERIFICAMOS SI LA CANTIDAD DE LA MENSUALIDAD FUE MODIFICADA Y SI REQUIERE AUMENTO O DISMINUCION DE VELOCIDAD
        if ($Cantidad == ($mensualidad['mensualidad']*10)) {
        }else{
          if ($dif < -50) {
            $Descrip = "AUMENTAR PAQUETE pago: ".$Cantidad." por: ".$Descripcion;
            if (mysqli_query($conn,"INSERT INTO reportes(id_cliente, descripcion, fecha, registro) VALUES ($IdCliente, '$Descrip', '$Fecha_hoy', $id_user)")) {
              echo '<script>M.toast({html:"Se registro el reporte (AUMENTAR)", classes: "rounded"})</script>';
            }
          }elseif ($dif > 0) {
            $Descrip = "DISMINUIR PAQUETE pago: ".$Cantidad." por: ".$Descripcion;
            if (mysqli_query($conn,"INSERT INTO reportes(id_cliente, descripcion, fecha, registro) VALUES ($IdCliente, '$Descrip', '$Fecha_hoy', $id_user)")) {
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
    }//FIN IF 10 DIAS???    
  }// FIN SE ELSE PAGO NO REPETIDO ??
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
    $sql_pagos = "SELECT * FROM pagos WHERE tipo != 'Dispositivo' AND id_cliente = ".$IdCliente." ORDER BY id_pago DESC";
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
}// FIN IF ENTRA ??
mysqli_close($conn);
?>