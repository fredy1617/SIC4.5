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
          valorTipo: 'Otros Pagos',
          valorIdCliente: textoIdCliente
  }, function(mensaje) {
  $("#mostrar_pagos").html(mensaje);
  }); 
};
function insert_pago() {  
  textoTipo = "Otros Pagos";
  var textoCantidad = $("input#cantidad3").val();
  var textoDescripcion = $("input#descripcion3").val();

  if(document.getElementById('banco_otro').checked==true){
    textoTipo_Campio = "Banco";
  }else if (document.getElementById('credito_otro').checked==true) {
    textoTipo_Campio = "Credito";
  }else{
    textoTipo_Campio = "Efectivo"; 
  }

  var textoIdCliente = $("input#id_cliente").val();
  var textoRespuesta = $("input#respuesta").val();

  if (textoCantidad == "" || textoCantidad ==0) {
      M.toast({html: 'El campo Cantidad se encuentra vacío o en 0.', classes: 'rounded'});
  }else {
      $.post("../php/insert_otros_pagos.php" , {
          valorTipo_Campio: textoTipo_Campio,
          valorTipo: textoTipo,
          valorCantidad: textoCantidad,
          valorDescripcion: textoDescripcion,
          valorIdCliente: textoIdCliente,
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
$resultado = mysqli_query($conn, $sql);
$datos = mysqli_fetch_array($resultado);
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
         <?php if ($area['area'] != 'Cobrador') { ?><b>Telefono: </b><?php echo $datos['telefono'];?><br> <?php }?>
         <b>Comunidad: </b><?php echo $comunidad['nombre'];?><br>
         <?php if ($area['area'] != 'Cobrador') { ?>
         <b>Dirección: </b><?php echo $datos['direccion'];?><br>
         <b>Referencia: </b><?php echo $datos['referencia'];?><br>
         <?php }?>
         <b>Fecha Corte Internet: </b><?php echo $datos['fecha_corte'];?><br>
         <b>Fecha Corte Telefono: </b><?php echo '';?><br>
         <?php if ($area['area'] != 'Cobrador') { ?><b>Observación: </b><?php echo $datos['descripcion']; ?><br><?php }?>
         <hr>
        <b>SALDO: </b> <span class="new badge <?php echo $color1 ?>" data-badge-caption="">$<?php echo $Saldo; ?><br>
      </p>
    </li>
  </ul>
  <div id="imprimir"></div>  
  <h3 class="hide-on-med-and-down pink-text "><< Otros Pagos >></h3>
  <h5 class="hide-on-large-only  pink-text"><< Otros Pagos >></h5>
<!---------------------------  TABs o MENU  -------------------------------------->
<!------------------------  FORMULARIO 3 Tabs  ---------------------------------->
    <div  class="col s12">
      <br><br>
      <div class="row">
      <form class="col s12">
      <br>
        <div class="row col s12 m3 l3">
        <div class="input-field">
          <i class="material-icons prefix">payment</i>
          <input id="cantidad3" type="number" class="validate" data-length="6" value="0" required>
          <label for="cantidad3">Cantidad:</label>
        </div>
      </div>
      <div class="row col s12 m5 l5">
        <div class="input-field">
          <i class="material-icons prefix">description</i>
          <input id="descripcion3" type="text" class="validate" data-length="100" required>
          <label for="descripcion3">Descripción:</label>
        </div>
      </div>
      <?php if ($user_id == 59 OR $user_id == 38 OR $user_id == 10 OR $user_id == 49) { ?>
        <div class="col s6 m2 l2">
          <p>
            <br>
            <input type="checkbox" id="banco"/>
            <label for="banco">Banco</label>
          </p>
        </div>
        <?php } ?>
        <div class="col s6 m2 l2">
          <p>
            <br>
            <input type="checkbox" id="credito_otro"/>
            <label for="credito_otro">Credito</label>
          </p>
        </div>
      </form>      
      <input id="id_cliente" value="<?php echo htmlentities($datos['id_cliente']);?>" type="hidden">
      <input id="respuesta" value="<?php echo htmlentities($respuesta);?>" type="hidden">
      <a onclick="insert_pago();" class="waves-effect waves-light btn pink right"><i class="material-icons right">send</i>Registrar Pago</a>
    </div><br>
 <!-------------------------- TABLA FORMULARIO 3  --------------------------------->
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
  </div><br>
</div>
</div><!----------------- row de TAB o MENU  ------------------------------------>
</div><!-----------------------  CONTAINER -------------------------------------->
</body>
<?php } ?>
</main>
</html>