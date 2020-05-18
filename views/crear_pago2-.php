<html>
<head>
  <title>SIC | Realizar Pago</title>
<?php 
include('fredyNav.php');
include('../php/conexion.php');
date_default_timezone_set('America/Mexico_City');
$Fecha_Hoy = date('Y-m-d');
$AÑO = date('Y');
$no_cliente = $_POST['no_cliente'];
?>
<script>
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
  $("#mostrar_pagos").html(mensaje);
  }); 
}

function resto_dias(){
  var f = new Date();
  var dia = 35 - f.getDate();
  if(dia >30){
    dia = dia - 30;
  }
  if(document.getElementById('resto').checked==true){
    M.toast({html: 'Calculando días restantes', classes: 'rounded'});
    
    var MensualidadAux = $("input#cantidadAux").val();
    var Mensualidad = parseInt(MensualidadAux);
    document.formMensualidad.cantidad.value = "";

    document.formMensualidad.cantidad.value = (dia*Mensualidad)/30;  
  }else{
    Materialize.toast("Calculando mensualidad", 4000, "rounded");
    var MensualidadAux = $("input#cantidadAux").val();
    var Mensualidad = parseInt(MensualidadAux);
    document.formMensualidad.cantidad.value = MensualidadAux;
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
    Materialize.toast("Se ha desactivado la promoción.", 4000, "rounded");
    var MensualidadAux = $("input#cantidadAux").val();
    var Mensualidad = parseInt(MensualidadAux);
    document.formMensualidad.cantidad.value = MensualidadAux;
  } 
}

<?php
$id_cliente = $no_cliente;
?>
function encender(){
  if(document.getElementById('enciende').checked==true){
    textoOrden = "Encender";  
  }else{    
    textoOrden = "Apagar";
  }
  textoIdCliente = <?php echo $id_cliente; ?>;
  $.post("../php/enciende_apaga.php", { 
          valorOrden: textoOrden,
          valorCliente:textoIdCliente,
  }, function(mensaje) {
  $("#Orden").html(mensaje);
  }); 
}

function insert_pago(tipo) { 
  
  M.toast({html: 'Entra.', classes: 'rounded'}); 
  if(tipo == 1){
    textoTipo = "Mensualidad";
    link = "../php/insert_pago.php";
    var textoCantidad = $("input#cantidad").val();
    var textoMes = $("select#mes").val();
    textoDescripcion = textoMes+" "+<?php echo $AÑO; ?>;
    if (document.getElementById('recargo').checked==true) {
      var Mensualidad = parseInt(textoCantidad);
      textoCantidad = Mensualidad+50;
      textoDescripcion = textoDescripcion+ " +RECARGO";

      M.toast({html: 'Descripción: '+textoDescripcion+' cantidad: '+textoCantidad, classes: 'rounded'});
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
  if (textoCantidad == "" || textoCantidad ==0) {
      M.toast({html: 'El campo Cantidad se encuentra vacío o en 0.', classes: 'rounded'});
    }else{

    if (tipoPago == "") { M.toast({html: 'No se ha seleccionado un tipo de pago.', classes: 'rounded'});
    }else{
      
    }  
    }    
};
</script>
</head>
<main>
<body>
<?php
$id = $_SESSION['user_id'];
$area = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM users WHERE user_id=$id"));
include('../php/conexion.php');
$no_cliente = $_POST['no_cliente'];
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
$color = 'green';
if ($Saldo < 0) {
  $color = 'red darken-2';
}
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
         <b>Fecha de Instalación: </b><?php echo $datos['fecha_instalacion'];?><br>
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
        <b>SALDO: </b> <span class="new badge <?php echo $color ?>" data-badge-caption="">$<?php echo $Saldo; ?><br>
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
        <div class="col s12 m3 l3">
          <p>
            <br>
            <input type="checkbox" onclick="promo();" id="todos"/>
            <label for="todos">Promoción anual</label>
          </p>
        </div>
        <div class="col s12 m3 l3">
          <p>
            <br>
            <input type="checkbox" onclick="resto_dias();" id="resto"/>
            <label for="resto">Calcular días restantes</label>
          </p>
        </div>
        <?php
        if($area['area'] != "Cobrador"){
        ?>
        <div class="col s12 m3 l3">
          <p>
            <br>
            <input type="checkbox" id="banco"/>
            <label for="banco">Banco</label>
          </p>
        </div>
        <?php
        }
        ?>
        <div class="col s12 m3 l3">
          <p>
            <br>
            <input type="checkbox" id="credito"/>
            <label for="credito">Credito</label>
          </p>
        </div>
      </div>
      <br><br><br>
      <div class="row col s12 m4 l4">
        <div class="input-field">
          <i class="material-icons prefix">payment</i>
          <input id="cantidad" type="number" class="validate" data-length="6" value="<?php echo $mensualidad['mensualidad'];?>" required>
          <input id="cantidadAux" type="hidden" class="validate" data-length="6" value="<?php echo $mensualidad['mensualidad'];?>" required>
          <label for="cantidad">Cantidad (Mensualidad  de $<?php echo $mensualidad['mensualidad'];?>.00):</label>
        </div>
      </div>
      <div class="row col s12 m5 l5"><br>
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
      <div class="col s12 m3 l3">
          <p>
            <br>
            <input type="checkbox" id="recargo"/>
            <label for="recargo">Recargo</label>
          </p>
      </div>
      <input id="id_cliente" value="<?php echo htmlentities($datos['id_cliente']);?>" type="hidden">
    </form>
    <a onclick="insert_pago(1);"  target="_blank" class="waves-effect waves-light btn pink right"><i class="material-icons right">send</i>Registrar Pago</a>
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
        <?php
        if($area['area'] != "Cobrador"){
        ?>
        <div class="col s12 m3 l3">
          <p>
            <br>
            <input type="checkbox" id="banco"/>
            <label for="banco">Banco</label>
          </p>
        </div>
        <?php
        }
        ?>
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
      <a onclick="insert_pago(2);mostrar_pagos();" class="waves-effect waves-light btn pink right"><i class="material-icons right">send</i>Registrar Pago</a>
      <br>
  <!-- ---------------------------- TABLA FORMULARIO 2  ---------------------------------------->
  <h4>Historial</h4>
  <div id="mostrar_pagos2">
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
        <?php
        if($area['area'] != "Cobrador"){
        ?>
        <div class="col s12 m3 l3">
          <p>
            <br>
            <input type="checkbox" id="banco"/>
            <label for="banco">Banco</label>
          </p>
        </div>
        <?php
        }
        ?>
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
          <label for="descripcion3">Descripción (opcional):</label>
        </div>
      </div>
      </form>
      <a onclick="insert_pago(3);mostrar_pagos();" class="waves-effect waves-light btn pink right"><i class="material-icons right">send</i>Registrar Pago</a>
    </div><br>
 <!-- ---------------------------- TABLA FORMULARIO 3  ---------------------------------------->
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
</div><!--------------------------  CONTAINER  ---------------------------------------->
</body>
</main>
</html>