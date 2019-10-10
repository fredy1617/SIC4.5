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
  var textoCantidad = $("input#cantidad2").val();
  var textoDescripcion = $("input#descripcion2").val();
  var tipoPago = $("select#selectTipo").val();

  if(document.getElementById('banco_tel').checked==true){
    textoTipo_Campio = "Banco";
  }else if (document.getElementById('credito_tel').checked==true) {
    textoTipo_Campio = "Credito";
  }else{
    textoTipo_Campio = "Efectivo"; 
  }

  var textoIdCliente = $("input#id_cliente").val();
  var textoRespuesta = $("input#respuesta").val();

  if (textoCantidad == "" || textoCantidad ==0) {
      M.toast({html: 'El campo Cantidad se encuentra vacío o en 0.', classes: 'rounded'});
  }else if (tipoPago == "") { M.toast({html: 'No se ha seleccionado un tipo de pago.', classes: 'rounded'});
  }else{
      $.post("../php/insert_pago_tel.php" , { 
          valorTipo_Campio:textoTipo_Campio,
          valorCantidad: textoCantidad,
          valorDescripcion: textoDescripcion,
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
         <b>Observación: </b><?php echo $datos['descripcion']; ?><br>
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
</div><!------------------ row de TAB o MENU  -------------------------------------->
</div><!-------------------------  CONTAINER  -------------------------------------->
</body>
<?php } ?>
</main>
</html>