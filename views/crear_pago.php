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
    }
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
$AÑO = date('Y');

$AÑO1 = strtotime('+1 year', strtotime($AÑO));
$AÑO1 = date('Y', $AÑO1);
$Pago = mysqli_fetch_array(mysqli_query($conn, "SELECT descripcion FROM pagos WHERE id_cliente = '$no_cliente'  AND tipo = 'Mensualidad' ORDER BY id_pago DESC LIMIT 1"));
//AQUI COLOCAMOS EL MISMO AÑO EN CASO DE SER ENERO MAS
$ver = explode(" ", $Pago['descripcion']);
$MAS = false;
if (count($ver)>1) {
  $UltimoAño= (int) $ver[1];
  if ($UltimoAño>$AÑO) {
    $AÑO1 = strtotime('+2 year', strtotime($AÑO));
    $MAS = date('Y', $AÑO1);
    $AÑO = $ver[1];
  }
}
?>
<script>
M.toast({html: 'Otro tipo de pagos en opción : OTROS PAGOS', classes: 'rounded'});
    
function imprimir(id_pago){
  var a = document.createElement("a");
      a.target = "_blank";
      a.href = "../php/imprimir.php?IdPago="+id_pago;
      a.click();
}
function borrar(IdPago){
  var textoIdCliente = $("input#id_cliente").val();
  $.post("../php/borrar_pago.php", { 
          valorIdPago: IdPago,
          valorIdCliente: textoIdCliente
  }, function(mensaje) {
  $("#tabla").html(mensaje);
  }); 
}

function resto_dias(){
  var f = new Date();
  var dia = f.getDate();

  if(document.getElementById('resto').checked==true){
    M.toast({html: 'Calculando días restantes', classes: 'rounded'});
    
    var MensualidadAux = $("input#cantidadAux").val();
    var Mensualidad = parseInt(MensualidadAux);
    document.formMensualidad.descuento.value = "";

    document.formMensualidad.descuento.value = (Mensualidad/31)*dia;  
  }else{
    M.toast({html:"Calculando mensualidad", classes: "rounded"});
    var MensualidadAux = $("input#cantidadAux").val();
    var Mensualidad = parseInt(MensualidadAux);
    document.formMensualidad.descuento.value = 0;
  }
}

function promo(){
  if(document.getElementById('todos').checked==true){
    M.toast({html: 'Se ha activado la promoción.', classes: 'rounded'});
    
    var MensualidadAux = $("input#cantidadAux").val();
    var Mensualidad = parseInt(MensualidadAux);
    document.formMensualidad.cantidad.value = "";

    document.formMensualidad.cantidad.value = 10*Mensualidad;  
  }else{
    M.toast({html:"Se ha desactivado la promoción.", classes: "rounded"});
    var MensualidadAux = $("input#cantidadAux").val();
    var Mensualidad = parseInt(MensualidadAux);
    document.formMensualidad.cantidad.value = MensualidadAux;
  } 
}

function encender(){
  if(document.getElementById('enciende').checked==true){
    textoOrden = "Encender";  
  }else{    
    textoOrden = "Apagar";
  }
  textoIdCliente = <?php echo $no_cliente; ?>;
  $.post("../php/enciende_apaga.php", { 
          valorOrden: textoOrden,
          valorCliente:textoIdCliente,
  }, function(mensaje) {
  $("#Orden").html(mensaje);
  }); 
}

function insert_pago(tipo) {  
  if(tipo == 1){

    textoTipo = "Mensualidad";
    link = "../php/insert_pago.php";
    var textoCantidad = $("input#cantidad").val();
    var textoMes = $("select#mes").val();
    var textoUltimo = $("input#ultimo").val();
    var textoDescuento = $("input#descuento").val();
    var textoHasta = $("input#hasta").val();

    textoDescripcion = textoMes+" "+<?php echo $AÑO; ?>;
    if (document.getElementById('todos').checked==true) {
      <?php
      $ANUAL = $AÑO1;
      if ($MAS) {
        $ANUAL = strtotime('+2 years', strtotime($AÑO));
        $ANUAL = date('Y', $ANUAL);
      }
      ?>
       textoDescripcion = textoMes+" "+<?php echo $ANUAL; ?>;
    }else if (textoUltimo == textoDescripcion){
        if(document.getElementById('todos').checked==true){
          textoDescripcion = textoMes+" "+<?php echo $AÑO1; ?>;
        }
    }else if (textoUltimo == "DICIEMBRE "+<?php echo $AÑO; ?>) {
      textoDescripcion = textoMes+" "+<?php echo $AÑO1; ?>;
    }else if (textoUltimo == "DICIEMBRE "+<?php echo $AÑO; ?>+" + RECARGO" ) {
      textoDescripcion = textoMes+" "+<?php echo $AÑO1; ?>;
    }

    if (document.getElementById('recargo').checked==true) {
      var Mensualidad = parseInt(textoCantidad);
      textoCantidad = Mensualidad+50;
      textoDescripcion = textoDescripcion+ " + RECARGO";
    }
    if (textoDescuento != 0) {
      textoDescripcion = textoDescripcion+" - Descuento: $"+textoDescuento;
    }
  }else if(tipo == 2){
    textoTipo = "tel";    
    link = "../php/insert_pago_tel.php";
    var textoCantidad = $("input#cantidad2").val();
    var textoDescripcion = $("input#descripcion2").val();
    var tipoPago = $("select#selectTipo").val();
  }else{

    link = "../php/insert_otros_pagos.php";
    textoTipo = "Otros Pagos";
    var textoCantidad = $("input#cantidad3").val();
    var textoDescripcion = $("input#descripcion3").val();
  }
  if(document.getElementById('todos').checked==true){
    textoPromo = "si";
  }else{
    textoPromo = "no";
  }

  if(document.getElementById('banco').checked==true || document.getElementById('banco_tel').checked==true || document.getElementById('banco_otro').checked==true){
    textoTipo_Campio = "Banco";
  }else{
    if (document.getElementById('credito').checked==true || document.getElementById('credito_tel').checked==true ||document.getElementById('credito_otro').checked==true) {textoTipo_Campio = "Credito";}
    else{textoTipo_Campio = "Efectivo";} 
  }

  var textoIdCliente = $("input#id_cliente").val();
  var textoRespuesta = $("input#respuesta").val();


  if (textoCantidad == "" || textoCantidad ==0) {
      M.toast({html: 'El campo Cantidad se encuentra vacío o en 0.', classes: 'rounded'});
    }else if (textoMes == 0) {
      M.toast({html: 'Seleccione un mes.', classes: 'rounded'});
    }else {
    if (tipoPago == "") { M.toast({html: 'No se ha seleccionado un tipo de pago.', classes: 'rounded'});}
    else{
      $.post(link , { 
          valorPromo: textoPromo,
          valorTipo_Campio: textoTipo_Campio,
          valorTipo: textoTipo,
          valorCantidad: textoCantidad,
          valorDescripcion: textoDescripcion,
          valorIdCliente: textoIdCliente,
          valorTipoTel: tipoPago,
          valorDescuento: textoDescuento,
          valorHasta: textoHasta,
          valorRespuesta: textoRespuesta
        }, function(mensaje) {
            $("#mostrar_pagos").html(mensaje);
            $("#tabla").html(tabla);
            $("#tabla1").html(tabla1);
            $("#tabla2").html(tabla2);
        });
    }   
      }    
};
</script>

<main>
<body>
<?php
$sql = "SELECT * FROM clientes WHERE id_cliente=$no_cliente";
$resultado = mysqli_query($conn, $sql);
$datos = mysqli_fetch_array($resultado);
//Sacamos la mensualidad
$id_mensualidad=$datos['paquete'];
$mensualidad = mysqli_fetch_array(mysqli_query($conn, "SELECT mensualidad FROM paquetes WHERE id_paquete='$id_mensualidad'"));

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

$Instalacion = $datos['fecha_instalacion'];
$nuevafecha = strtotime('+6 months', strtotime($Instalacion));
$Vence = date('Y-m-d', $nuevafecha);
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
        <b>Telefono: </b><?php echo $datos['telefono'];?><br>
         <b>Comunidad: </b><?php echo $comunidad['nombre'];?><br>
         <b>Dirección: </b><?php echo $datos['direccion'];?><br>
         <b>Referencia: </b><?php echo $datos['referencia'];?><br>
         <b>Fecha Corte: </b><span id="corte"><?php echo $datos['fecha_corte'];?></span><br>
         <b>Fecha de Instalación: </b><?php echo $Instalacion;?><br>
         <?php
         $color = "green";
         $Estatus = "Vigente";
         if ($Hoy > $Vence) {
              $color = "red accent-4";
              $Estatus = "Vencido";
          }
         if ($datos['contrato'] == 1) {
          ?> 
         <b>Vencimiento de Contrato: </b><?php echo $Vence;?><span class="new badge <?php echo $color; ?>" data-badge-caption=""><?php echo $Estatus; ?></span><br>
         <?php 
         }
         ?>
         <b>Observación: </b><?php echo $datos['descripcion']; ?>
         <span class="new badge pink hide-on-med-and-up" data-badge-caption="<?php echo $datos['fecha_corte'];?>"></span><br><br>
         <b>Internet: </b> 
         <!-- Switch -->
         <?php 
         $estado="";
         if ($datos['fecha_corte']>$Fecha_Hoy) {
           $estado = "checked";
         } 
         ?>
          <div class="switch right">
            <label>
              Off
              <input type="checkbox" <?php echo $estado; ?> onclick="encender();" id="enciende">
              <span class="lever"></span>
              On
            </label>
          </div>
          <br>
         <hr>
        <b>SALDO: </b> <span class="new badge <?php echo $color1 ?>" data-badge-caption="">$<?php echo $Saldo; ?><br>
      </p>
      <?php
      if(date('Y-m-d')<=$datos['fecha_corte']){
        ?>
        <a class="secondary-content"><span class="new badge pink hide-on-small-only" data-badge-caption="<?php echo $datos['fecha_corte'];?>"></span></a>
        <?php
      }else{
        ?>
        <a href="#!" class="secondary-content"><span class="new badge pink hide-on-med-and-down" data-badge-caption="<?php echo $datos['fecha_corte'];?>"></span></a>
        <?php
      }
        ?>
    </li>
  </ul>
  <div id="imprimir"></div>
<!-- ----------------------------  TABs o MENU  ---------------------------------------->
  <div class="row">
    <div class="col s12">
    <ul id="tabs-swipe-demo" class="tabs">
      <li class="tab col s3"><a class="active black-text" href="#test-swipe-1">Mensualidad</a></li>
      <li class="tab col s6"><a class="black-text" href="#test-swipe-2">Pago de teléfono</a></li>
      <li class="tab col s3"><a class="black-text" href="#test-swipe-3">Otros Pagos</a></li>
    </ul>
    </div>

<!-- ----------------------------  FORMULARIO 1 Tabs  ---------------------------------------->
    <div id="test-swipe-1" class="col s12">
      <br>
      <div class="row">
      <form class="col s12" name="formMensualidad">
      <div class="row">
        <div class="col s6 m2 l2">
          <p>
            <br>
            <input type="checkbox" onclick="promo();" id="todos"/>
            <label for="todos">Promoción anual</label>
          </p>
        </div>
        <div class="col s6 m3 l3">
          <p>
            <br>
            <input type="checkbox" onclick="resto_dias();" id="resto"/>
            <label for="resto">Calcular días restantes</label>
          </p>
        </div>
        <div class="col s6 m2 l2">
          <p>
            <br>
            <input type="checkbox" id="banco"/>
            <label for="banco">Banco</label>
          </p>
        </div>
        <div class="col s6 m2 l2">
          <p>
            <br>
            <input type="checkbox" id="credito"/>
            <label for="credito">Credito</label>
          </p>
        </div>
        <div class="col s6 m3 l3" >
              <label for="hasta">Fecha de Promesa:</label>
              <input id="hasta" type="date">    
        </div>
      </div>
      <br><br><br>
      <div class="row">
      <div class="row col s12 m4 l4">
        <div class="input-field">
          <i class="material-icons prefix">payment</i>
          <input id="cantidad" type="number" class="validate" data-length="6" value="<?php echo $mensualidad['mensualidad'];?>" required>
          <input id="cantidadAux" type="hidden" class="validate" data-length="6" value="<?php echo $mensualidad['mensualidad'];?>" required>
          <label for="cantidad">Cantidad (Mensualidad  de $<?php echo $mensualidad['mensualidad'];?>.00):</label>
        </div>
      </div>
      <div class="row col s8 m3 l3"><br>
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
      <div class="col s4 m2 l2">
          <p>
            <br>
            <?php 
             $estado="";
             if ($datos['fecha_corte']<$Fecha_Hoy) {
               $estado = "checked";
             } 
             ?>
            <input type="checkbox" <?php echo $estado;?> id="recargo"/>
            <label for="recargo">Recargo</label>
          </p>
      </div>
      <div class="row col s12 m3 l3">
        <div class="input-field">
          <i class="material-icons prefix">money_off</i>
          <input id="descuento" type="number" class="validate" data-length="6" required value="0">
          <label for="descuento">Descuento ($ 0.00):</label>
        </div>
      </div>      
      </div>
      <input id="id_cliente" value="<?php echo htmlentities($datos['id_cliente']);?>" type="hidden">
      <input id="respuesta" value="<?php echo htmlentities($respuesta);?>" type="hidden">
      <input id="ultimo" value="<?php echo htmlentities($Pago['descripcion']);?>" type="hidden">
    </form>
    <a onclick="insert_pago(1);" class="waves-effect waves-light btn pink right "><i class="material-icons right">send</i>Registrar Pago</a>
    </div>
    <br>

<!-- ----------------------------  TABLA DE FORM 1  ---------------------------------------->
    <h4>Historial </h4>
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
      $sql_pagos = "SELECT * FROM pagos WHERE id_cliente = ".$datos['id_cliente']." && tipo = 'Mensualidad' ORDER BY id_pago DESC";
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
        <td><a onclick="borrar(<?php echo $pagos['id_pago'];?>);" class="btn btn-floating red darken-1 waves-effect waves-light"><i class="material-icons">delete</i></a>
        </td>
      </tr>
      
      <?php
      $aux--;
      }//Fin while
      }else{
      echo "<center><b><h3>Este cliente aún no ha registrado pagos</h3></b></center>";
    }
    ?> 
    </tbody>
  </table>    
  </div>
<br>
</div>

<!-- ----------------------------  FORMULARIO 2 Tabs  ---------------------------------------->
    <div id="test-swipe-2" class="col s12">
      <div class="row">
      <form class=" col s12">
      <div class="row">
        <div class="col s12 m3 l3">
          <p>
            <br>
            <input type="checkbox" id="banco_tel"/>
            <label for="banco_tel">Banco</label>
          </p>
        </div>
        <div class="col s12 m3 l3">
          <p>
            <br>
            <input type="checkbox" id="credito_tel"/>
            <label for="credito_tel">Credito</label>
          </p>
        </div>
      </div>
      <br><br>
          <div class="input-field col s12 m4 l4">
            <select id="selectTipo" required>
              <option value="" selected>Seleccione el tipo de pago</option>
              <option value="Mes-Tel">Mensualidad</option>
              <option value="Min-extra">Minutos extra</option>
            </select>
          </div>
          <div class="input-field col s12 m4 l4">
          <i class="material-icons prefix">payment</i>
          <input id="cantidad2" type="number" class="validate" data-length="6" required>
          <label for="cantidad2">Cantidad: </label>
        </div>
        <div class="input-field col s12 m4 l4">
          <i class="material-icons prefix">description</i>
          <input id="descripcion2" type="text" class="validate" value="Pago de teléfono " data-length="20">
          <label for="descripcion2">Descripción (opcional):</label>
        </div>
     
      </form>
       </div>
      <a onclick="insert_pago(2);" class="waves-effect waves-light btn pink right"><i class="material-icons right">send</i>Registrar Pago</a>
      <br>
  <!-- ---------------------------- TABLA FORMULARIO 2  ---------------------------------------->
  <h4>Historial</h4>
  <div id="tabla1">
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
        <th>Corte</th>
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
        <td><?php echo $pagos['fecha'];?></td>
        <?php if ($pagos['Cotejado'] ==1){
          $imagen = "nc.PNG";
          echo "<td><img src='../img/$imagen'</td>";
          }else if ($pagos['Cotejado'] == 2) {
            $imagen = "listo.PNG";
            echo "<td><img src='../img/$imagen'</td>";
          }else{  echo "<td>N/A</td>";  }
        if($pagos['tipo']== 'Mes-Tel'){
           $cortem = $pagos['Corte_tel'];
        }else{  $cortem= 'N/A';  }  
        ?>
        <td><?php echo $cortem;?></td>
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

 <!-- ----------------------------  FORMULARIO 3 Tabs  ---------------------------------------->
    <div id="test-swipe-3" class="col s12">
      <br><br>
      <div class="row">
      <form class="col s12">
        <div class="row">
        <div class="col s12 m3 l3">
          <p>
            <br>
            <input type="checkbox" id="banco_otro"/>
            <label for="banco_otro">Banco</label>
          </p>
        </div>
        <div class="col s12 m3 l3">
          <p>
            <br>
            <input type="checkbox" id="credito_otro"/>
            <label for="credito_otro">Credito</label>
          </p>
        </div>
      </div>
      <br><br>
        <div class="row col s12 m4 l4">
        <div class="input-field">
          <i class="material-icons prefix">payment</i>
          <input id="cantidad3" type="number" class="validate" data-length="6" value="0" required>
          <label for="cantidad3">Cantidad:</label>
        </div>
      </div>
      <div class="row col s12 m8 l8">
        <div class="input-field">
          <i class="material-icons prefix">description</i>
          <input id="descripcion3" type="text" class="validate" data-length="100" required>
          <label for="descripcion3">Descripción:</label>
        </div>
      </div>
      </form>
      <a onclick="insert_pago(3);" class="waves-effect waves-light btn pink right"><i class="material-icons right">send</i>Registrar Pago</a>
    </div><br>
 <!-- ---------------------------- TABLA FORMULARIO 3  ---------------------------------------->
  <h4>Historial</h4>
  <div id="tabla2">
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
    $sql_pagos = "SELECT * FROM pagos WHERE id_cliente = ".$datos['id_cliente']." && tipo = 'Otros Pagos' ORDER BY id_pago DESC";
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
<br>
</div>

</div><!------------------- row de TAB o MENU  ---------------------------------------->
  <div id="mostrar_pagos"></div>
</div><!--------------------------  CONTAINER  ---------------------------------------->
</body>
<?php
}
?>
</main>
</html>