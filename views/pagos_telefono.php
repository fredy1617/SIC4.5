<html>
<head>
  <title>SIC | Realizar Pago</title>
</head>
<?php 
include('fredyNav.php');
include('../php/conexion.php');
date_default_timezone_set('America/Mexico_City');
$Fecha_Hoy = date('Y-m-d');
if (isset($_POST['no_cliente']) == false) {
  ?>
  <script>    
    function atras() {
      M.toast({html: "Regresando a clientes.", classes: "rounded"})
      setTimeout("location.href='clientes.php'", 1000);
    };
    atras();
  </script>
  <?php
}else{
$no_cliente = $_POST['no_cliente'];
if (isset($_POST['resp']) == false) {
  $respuesta = 'Ver';
}else{
  $respuesta = $_POST['resp'];
}
?>
<script>
function imprimir(id_pago){
  var a = document.createElement("a");
      a.target = "_blank";
      a.href = "../php/imprimir.php?IdPago="+id_pago;
      a.click();
};
function borrar(IdPago){
  var textoIdCliente = $("input#id_cliente").val();
  $.post("../php/borrar_pago.php", { 
          valorIdPago: IdPago,
          valorIdCliente: textoIdCliente,
          valorTipo : "Telefono"
  }, function(mensaje) {
  $("#mostrar_pagos").html(mensaje);
  }); 
};
function insert_pago() {    
  var textoCantidad = $("input#cantidad").val();
  var tipoPago = $("select#selectTipo").val();
  var textoMes = $("select#mes").val();
    var textoAño = $("select#añot").val();
  var textoRef = $("input#ref").val();

  if(document.getElementById('banco_tel').checked==true){
    textoTipo_Campio = "Banco";
  }else if (document.getElementById('credito_tel').checked==true) {
    textoTipo_Campio = "Credito";
  }else if (document.getElementById('san_tel').checked==true) {
    textoTipo_Campio = "SAN";
  }else{
    textoTipo_Campio = "Efectivo"; 
  }
  entra = 'Si';
  if (tipoPago == 'Mes-Tel') {
    if (textoMes == 0) {
      entra = 'No';
    }
  }
  var textoIdCliente = $("input#id_cliente").val();
  var textoRespuesta = $("input#respuesta").val();

  if (tipoPago == "") { 
    M.toast({html: 'No se ha seleccionado un tipo de pago.', classes: 'rounded'});
  }else if (textoCantidad == "" || textoCantidad ==0) {
      M.toast({html: 'El campo Cantidad se encuentra vacío o en 0.', classes: 'rounded'});
  }else  if (entra == 'No') {
      M.toast({html: 'Seleccione un mes.', classes: 'rounded'});
  }else if (textoAño == 0 && entra == 'No') {
        M.toast({html: 'Seleccione un año.', classes: 'rounded'});
  }else if ((document.getElementById('banco_tel').checked==true || document.getElementById('san_tel').checked==true)&& textoRef == "") {
        M.toast({html: 'Los pagos en banco deben de llevar una referencia.', classes: 'rounded'});
  }else if (document.getElementById('banco_tel').checked==false && document.getElementById('san_tel').checked==false && textoRef != "") {
        M.toast({html: 'Pusiste referencia y no elegiste Banco o SAN.', classes: 'rounded'});
  }else{
      $.post("../php/insert_pago_tel.php" , { 
          valorTipo_Campio:textoTipo_Campio,
          valorCantidad: textoCantidad,
          valorAño: textoAño,
          valorMes: textoMes,
          valorRef: textoRef,
          valorIdCliente: textoIdCliente,
          valorTipoTel: tipoPago,
          valorRespuesta: textoRespuesta
        }, function(mensaje) {
            $("#mostrar_pagos").html(mensaje);
        });
  }       
};
</script>
<main>
<body>
<?php
$sql = "SELECT * FROM clientes WHERE id_cliente=$no_cliente";
$datos = mysqli_fetch_array(mysqli_query($conn, $sql));
//Sacamos la Comunidad
$id_comunidad = $datos['lugar'];
$comunidad = mysqli_fetch_array(mysqli_query($conn, "SELECT nombre FROM comunidades WHERE id_comunidad='$id_comunidad'"));
//Sacamos la suma de todas las deudas y abonos...
$deuda = mysqli_fetch_array(mysqli_query($conn, "SELECT SUM(cantidad) AS suma FROM deudas WHERE id_cliente='$no_cliente'"));
$abono = mysqli_fetch_array(mysqli_query($conn, "SELECT SUM(cantidad) AS suma FROM pagos WHERE id_cliente = $no_cliente AND tipo = 'Abono'"));
//COMPARAMOS PARA VER SI LOS VALORES ESTAN VACIOS
if ($deuda['suma'] == "") {
  $deuda['suma'] = 0;
}else if ($abono['suma'] == "") {
  $abono['suma'] = 0;
}
//SE HACE LA RESTA Y SI EL SALDO ES NEGATIVO CAMBIAMOS EL COLOR
$Saldo = $abono['suma']-$deuda['suma'];
$color1 = 'green';
if ($Saldo < 0) {
  $color1 = 'red darken-2';
}

$user_id = $_SESSION['user_id'];
$area = mysqli_fetch_array(mysqli_query($conn, "SELECT area FROM users WHERE user_id='$user_id'"));
?>
<div class="container">
  <h3 class="hide-on-med-and-down">Realizando pago del cliente:</h3>
  <h5 class="hide-on-large-only">Realizando pago del cliente:</h5>
  <div id="Orden"></div>
  <div id="resultado_insert_pago"></div>
  <ul class="collection">
    <li class="collection-item avatar">
      <img src="../img/cliente.png" alt="" class="circle">
      <span class="title"><b>No. Cliente: </b><?php echo $datos['id_cliente'];?></span>
      <p><b>Nombre(s): </b><?php echo $datos['nombre'];?><br>
         <?php if ($area['area'] != 'Cobrador') { ?>
         <b>Telefono: </b><?php echo $datos['telefono'];?><br>
         <b>Extención: </b><?php echo $datos['tel_servicio'];?><br>
         <?php }?>
         <b>Comunidad: </b><?php echo $comunidad['nombre'];?><br>
         <?php if ($area['area'] != 'Cobrador') { ?>
         <b>Dirección: </b><?php echo $datos['direccion'];?><br>
         <b>Referencia: </b><?php echo $datos['referencia'];?><br>
         <b>Observación: </b><?php echo $datos['descripcion']; ?><br>
         <b>Fecha de suscripción: </b><?php echo $datos['fecha_instalacion']; ?><br>
         <?php
          }
         if ($datos['tel_cortado'] == 0) {
           $estado = "ACTIVO";
           $col = "green";
         }else{
           $estado = "CORTADO";
           $col = "red";
         }
         ?>
         <b>Fecha de Corte: </b><?php echo $datos['corte_tel']; ?> - <b class="<?php echo $col;?>-text"><?php echo $estado;?></b><br>
         <hr>
         <b>SALDO: </b> <span class="new badge <?php echo $color1 ?>" data-badge-caption="">$<?php echo $Saldo; ?><br>
      </p>
    </li>
  </ul>
  <div id="imprimir"></div>
<!-- --------------------------  TABs o MENU  -------------------------------------->
  <div class="row">
  <h3 class="hide-on-med-and-down pink-text "><< Telefono >></h3>
  <h5 class="hide-on-large-only  pink-text"><< Telefono >></h5>
<!-- -----------------------  FORMULARIO 2 Tabs  ----------------------------------->
    <div id="test-swipe-2" class="col s12">
      <div class="row">
      <form class=" col s12">
      <br><br>
          <div class="input-field col s12 m3 l3">
            <select id="selectTipo" required>
              <option value="" selected>Tipo de pago:</option>
              <option value="Mes-Tel">Mensualidad</option>
              <option value="Min-extra">Minutos extra</option>
            </select>
          </div>
          <div class="input-field col s5 m2 l2">
          <i class="material-icons prefix">payment</i>
          <input id="cantidad" type="number" class="validate" data-length="6" required>
          <label for="cantidad">Cantidad: </label>
        </div>
        <div class="row col s7 m2 l2"><br>
          <select id="mes" class="browser-default">
            <option value="0" selected>Seleccione Mes</option>
            <option value="ENERO">Enero</option>
            <option value="FEBRERO">Febrero</option>
            <option value="MARZO">Marzo</option>
            <option value="ABRIL">Abril</option>
            <option value="MAYO">Mayo</option>
            <option value="JUNIO">Junio</option>
            <option value="JULIO">Julio</option>
            <option value="AGOSTO">Agosto</option>
            <option value="SEPTIEMBRE">Septiembre</option>
            <option value="OCTUBRE">Octubre</option>
            <option value="NOVIEMBRE">Noviembre</option>
            <option value="DICIEMBRE">Diciembre</option>
          </select>
        </div>
         <div class="row col s7 m2 l2"><br>
          <select id="añot" class="browser-default">
            <option value="0" selected>Seleccione Año</option>
            <option value="2020">2020</option>
            <option value="2021">2021</option>
          </select>
        </div>  
        <?php if ($user_id == 38 OR $user_id == 10 OR $user_id == 77 OR $user_id == 49 OR $user_id == 70  OR $user_id == 75) { 
           $Ser = '';
        }else{ $Ser = 'disabled="disabled"';}?>
        <div class="col s6 m1 l1">
          <p>
            <br>
            <input type="checkbox" id="banco_tel" <?php echo $Ser;?>/>
            <label for="banco_tel">Banco</label>
          </p>
        </div>
        <div class="col s6 m1 l1">
          <p>
            <br>
            <input type="checkbox" id="san_tel" <?php echo $Ser;?>/>
            <label for="san_tel">SAN</label>
          </p>
        </div>
        <div class="col s6 m2 l2">
          <div class="input-field">
            <input id="ref" type="text" class="validate" data-length="15" required value="">
            <label for="ref">Referencia:</label>
          </div>
        </div>
        <div class="col s6 m2 l2">
          <p>
            <br>
            <input type="checkbox" id="credito_tel"/>
            <label for="credito_tel">Credito</label>
          </p>
        </div>   
      </form>
      </div>
      <input id="id_cliente" value="<?php echo htmlentities($datos['id_cliente']);?>" type="hidden">
      <input id="respuesta" value="<?php echo htmlentities($respuesta);?>" type="hidden">
      <a onclick="insert_pago();" class="waves-effect waves-light btn pink right"><i class="material-icons right">send</i>Registrar Pago</a>
      <br>
<!---------------------------- TABLA FORMULARIO 2  ---------------------------------->
  <h4>Historial</h4>
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
        <th>Cotejado</th>
        <th>Imprimir</th>
        <th>Borrar</th>
      </tr>
    </thead>
    <tbody>
    <?php
    $sql_pagos = "SELECT * FROM pagos WHERE tipo IN ('Min-extra', 'Mes-Tel') && id_cliente = ".$datos['id_cliente']." ORDER BY id_pago DESC  ";
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
        <?php if ($pagos['Cotejado'] ==1){
          $imagen = "nc.PNG";
          echo "<td><img src='../img/$imagen'</td>";
          }else if ($pagos['Cotejado'] == 2) {
            $imagen = "listo.PNG";
            echo "<td><img src='../img/$imagen'</td>";
          }else{  echo "<td>N/A</td>";  } 
        ?>
        <td><a onclick="imprimir(<?php echo $pagos['id_pago'];?>);" class="btn btn-floating pink waves-effect waves-light"><i class="material-icons">print</i></a>
        </td>
        <td><a onclick="borrar(<?php echo $pagos['id_pago'];?>);" class="btn btn-floating red darken-1 waves-effect waves-light"><i class="material-icons">delete</i></a>
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
<br>
</div>
</div><!------------------ row de TAB o MENU  -------------------------------------->
</div><!-------------------------  CONTAINER  -------------------------------------->
</body>
<?php } ?>
</main>
</html>