<html>
<head>
  <title>SIC | Realizar Pago</title>
</head>
<?php 
include('fredyNav.php');
include('../php/conexion.php');
date_default_timezone_set('America/Mexico_City');
$Fecha_Hoy = date('Y-m-d');
$no_cliente = 0;
if (isset($_POST['no_cliente']) == false) {
  if (isset($_GET['cliente']) == false) {
    ?>
    <script>    
      function atras() {
        M.toast({html: "Regresando a clientes.", classes: "rounded"})
        setTimeout("location.href='clientes.php'", 800);
      };
      atras();
    </script>
    <?php
  }else{
    $no_cliente = $_GET['cliente'];
  }
}else{
$no_cliente = $_POST['no_cliente'];
}#VERIFICAMOS QUE RECIBAMOS UN ID DE CLIENTE VALIDO
if ($no_cliente > 0) {
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
function irconsumo(){  
  textoIdCliente = <?php echo $no_cliente; ?>;
  $.post("../php/ir_consumo.php", { 
    valorCliente:textoIdCliente,
  }, function(mensaje) {
  $("#consumo_ir").html(mensaje);
  }); 
};
function borrar(IdPago){
  var textoIdCliente = $("input#id_cliente").val();
  $.post("../php/borrar_pago.php", { 
          valorIdPago: IdPago,
          valorIdCliente: textoIdCliente,
          valorTipo : "Mensualidad"
  }, function(mensaje) {
  $("#mostrar_pagos").html(mensaje);
  }); 
};
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
};
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
};
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
};
function insert_pago() {  
    textoTipo = "Mensualidad";
    var textoCantidad = $("input#cantidad").val();
    var textoMes = $("select#mes").val();
    var textoAño = $("select#año").val();
    var textoDescuento = $("input#descuento").val();
    var textoHasta = $("input#hasta").val();
    var textoRef = $("input#ref").val();
    //Todo esto solo para agregar la descripcion automatica
    textoDescripcion = textoMes+" "+textoAño;
      

      if (document.getElementById('recargo').checked==true) {
        var Mensualidad = parseInt(textoCantidad);
        textoCantidad = Mensualidad+50;
        textoDescripcion = textoDescripcion+ " + RECARGO (Reconexion o Pago Tardio)";
      }
      if (textoDescuento != 0) {
        textoDescripcion = textoDescripcion+" - Descuento: $"+textoDescuento;
      }

    if(document.getElementById('todos').checked==true){
      textoPromo = "si";
    }else{
      textoPromo = "no";
    }

    if(document.getElementById('banco').checked==true){
      textoTipo_Campio = "Banco";
    }else if (document.getElementById('credito').checked==true) {
      textoTipo_Campio = "Credito";
    }else if (document.getElementById('san').checked==true) {
      textoTipo_Campio = "SAN";
    }else{
      textoTipo_Campio = "Efectivo";
    } 

    var textoIdCliente = $("input#id_cliente").val();
    var textoRespuesta = $("input#respuesta").val();

    if (textoCantidad == "" || textoCantidad ==0) {
        M.toast({html: 'El campo Cantidad se encuentra vacío o en 0.', classes: 'rounded'});
    }else if (textoMes == 0) {
        M.toast({html: 'Seleccione un mes.', classes: 'rounded'});
    }else if (textoAño == 0) {
        M.toast({html: 'Seleccione un año.', classes: 'rounded'});
    }else if ((document.getElementById('banco').checked==true || document.getElementById('san').checked==true) && textoRef == "") {
        M.toast({html: 'Los pagos en banco y san deben de llevar una referencia.', classes: 'rounded'});
    }else if (document.getElementById('banco').checked==false && document.getElementById('san').checked==false && textoRef != "") {
        M.toast({html: 'Pusiste referencia y no elegiste Banco o SAN.', classes: 'rounded'});
    }else {
        $.post("../php/insert_pago.php" , { 
            valorPromo: textoPromo,
            valorTipo_Campio: textoTipo_Campio,
            valorTipo: textoTipo,
            valorCantidad: textoCantidad,
            valorDescripcion: textoDescripcion,
            valorIdCliente: textoIdCliente,
            valorDescuento: textoDescuento,
            valorHasta: textoHasta,
            valorRef: textoRef,
            valorRespuesta: textoRespuesta,
            valorMes: textoMes,
            valorAño: textoAño
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
//Sacamos la mensualidad
$id_mensualidad=$datos['paquete'];
$mensualidad = mysqli_fetch_array(mysqli_query($conn, "SELECT mensualidad FROM paquetes WHERE id_paquete='$id_mensualidad'"));
//Sacamos la Comunidad
$id_comunidad = $datos['lugar'];
$comunidad = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM comunidades WHERE id_comunidad='$id_comunidad'"));
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
//VER CUANTOS DIAS HAN PASADO DESDE EL ULTIMO CORTE SOLO SI LA FECHA DE CORTE ES MENOR A HOY
$Descuento = 0;
$corteInt = mysqli_fetch_array(mysqli_query($conn,"SELECT * FROM int_cortes ORDER BY id DESC LIMIT 1"));
if ($datos['fecha_corte'] < $Fecha_Hoy ) {
  $mesA = date('Y-m');
  $ver = explode("-", $corteInt['fecha']);
  $ver2 = explode("-", $datos['fecha_corte']);
  $mesC = $ver[0].'-'.$ver[1];
  $mesF = $ver2[0].'-'.$ver2[1];
  $date1 = new DateTime($Fecha_Hoy);
  $date2 = new DateTime($corteInt['fecha']);

  //Le restamos a la fecha date1-date2
  $diff = $date1->diff($date2);
  $Dias_pasaron= $diff->days;
  if ($mesA == $mesC and $mesA == $mesF) {
     $xDia = $mensualidad['mensualidad']/30;
     $Descuento = $Dias_pasaron*$xDia;
     $Descuento = round($Descuento, 0, PHP_ROUND_HALF_DOWN);
  }
}
$user_id = $_SESSION['user_id'];
$area = mysqli_fetch_array(mysqli_query($conn, "SELECT area FROM users WHERE user_id='$user_id'"));
?>
<div id="consumo_ir"></div>
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
         <?php if ($area['area'] != 'Cobrador') { ?><b>Telefono: </b><?php echo $datos['telefono'];?><br> <?php }?>
         <b>Comunidad: </b><?php echo $comunidad['nombre'].', '.$comunidad['municipio'];?><br>
         <?php if ($area['area'] != 'Cobrador') { ?>
         <b>Dirección: </b><?php echo $datos['direccion'];?><br>
         <b>Referencia: </b><?php echo $datos['referencia'];?><br>
         <?php }?>
         <b>Fecha Corte: </b><span id="corte"><?php echo $datos['fecha_corte'];?></span><br> 
         <b>Fecha Corte Mensual: <?php echo $corteInt['fecha'];?></b><a onclick="irconsumo();" class="waves-effect waves-light btn pink right"><i class="material-icons right">equalizer</i>CONSUMO</a> <br>
         <?php if ($area['area'] != 'Cobrador') { ?>
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
         <?php } ?>
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
          <?php }?>
         <hr>
        <b>SALDO: </b> <span class="new badge <?php echo $color1 ?>" data-badge-caption="">$<?php echo $Saldo; ?><br>
      </p>
      <?php if(date('Y-m-d')<=$datos['fecha_corte']){ ?>
        <a class="secondary-content"><span class="new badge pink hide-on-small-only" data-badge-caption="<?php echo $datos['fecha_corte'];?>"></span></a>
        <?php
      }else{
        ?>
        <a href="#!" class="secondary-content"><span class="new badge pink hide-on-med-and-down" data-badge-caption="<?php echo $datos['fecha_corte'];?>"></span></a>
      <?php } ?>
    </li>
  </ul>
  <div id="imprimir"></div><br>
  <h3 class="hide-on-med-and-down pink-text "><< Internet >></h3>
  <h5 class="hide-on-large-only  pink-text"><< Internet >></h5>
<!-- ----------------------------  TABs o MENU  ---------------------------------------->
  <div class="row">
    <div class="col s12">
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
        <div class="col s6 m2 l2">
          <p>
            <br>
            <input type="checkbox" onclick="resto_dias();" id="resto"/>
            <label for="resto">Calcular días restantes</label>
          </p>
        </div>
        <?php if (in_array($user_id, array(10, 70, 49, 88, 38, 84, 90))) { 
          $Ser = '';
        }else{ $Ser = 'disabled="disabled"';}?>
        <div class="col s6 m1 l1">
          <p>
            <br>
            <input type="checkbox" id="banco" <?php echo $Ser;?>/>
            <label for="banco">Banco</label>
          </p>
        </div>
        <div class="col s6 m1 l1">
          <p>
            <br>
            <input type="checkbox" id="san" <?php echo $Ser;?>/>
            <label for="san">SAN</label>
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
            <input type="checkbox" id="credito"/>
            <label for="credito">Credito</label>
          </p>
        </div>
        <div class="col s6 m2 l2" >
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
      <div class="row col s8 m2 l2"><br>
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
      <div class="row col s8 m2 l2"><br>
        <select id="año" class="browser-default">
          <option value="0" selected>Seleccione Año</option>
          <option value="2020">2020</option>
          <option value="2021">2021</option>        
          <option value="2022">2022</option>         
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
      <div class="row col s12 m2 l2">
        <div class="input-field">
          <i class="material-icons prefix">money_off</i>
          <input id="descuento" type="number" class="validate" data-length="6" required value="<?php echo $Descuento;?>">
          <label for="descuento">Descuento ($ 0.00):</label>
        </div>
      </div>      
      </div>
      <input id="id_cliente" value="<?php echo htmlentities($datos['id_cliente']);?>" type="hidden">
      <input id="respuesta" value="<?php echo htmlentities($respuesta);?>" type="hidden">
    </form>
    <a onclick="insert_pago();" class="waves-effect waves-light btn pink right "><i class="material-icons right">send</i>Registrar Pago</a>
    </div>
    <br>
<!-- ----------------------------  TABLA DE FORM 1  ---------------------------------------->
    <h4>Historial </h4>
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
            <th>Cambio</th>
            <th>Imprimir</th>
            <th>Borrar</th>
          </tr>
        </thead>
      <tbody>
      <?php
      $sql_pagos = "SELECT * FROM pagos WHERE id_cliente = ".$datos['id_cliente']." AND tipo != 'Dispositivo' ORDER BY id_pago DESC";
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
          <td><?php echo $pagos['tipo_cambio']; ?></td>
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
</div>
</div><!------------------ row de TAB o MENU  ------------------------------------->
</div><!-------------------------  CONTAINER  -------------------------------------->
</body>
<?php } ?>
</main>
</html>