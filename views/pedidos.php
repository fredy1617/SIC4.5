<!DOCTYPE html>
<html lang="en">
<head>
<?php
  include('fredyNav.php');
  include ('../php/cobrador.php');
?>
<title>SIC | Pedidos</title>
<script>
function buscar_pedidos(){
  var texto = $("input#busqueda").val();
  $.post("../php/buscar_pedidos.php", {
      texto: texto,
    }, function(mensaje){
        $("#PedidosALL").html(mensaje);
  });
};
function buscar_pedidos2(){
  var texto = $("input#busqueda2").val();
  $.post("../php/buscar_pedidos2.php", {
      texto: texto,
    }, function(mensaje){
        $("#pedidosNo").html(mensaje);
  });
};
function insert_pedidos() {
    var textoNombre = $("input#nombre").val();
    var textoOrden = $("input#orden").val();
    var textoFecha = $("input#fecha_req").val();

    if (textoOrden == 0 || textoOrden == '') {
      textoOrden = "N/A";
    }
    if (textoNombre == "") {
      M.toast({html :"Ingresa un nombre de cliente al pedido.", classes: "rounded"});
    }else{
      $.post("../php/insert_pedidos.php", {
          valorNombre: textoNombre,
          valorFecha: textoFecha,
          valorOrden: textoOrden
        }, function(mensaje) {
            $("#resultado_pedido").html(mensaje);
        }); 
    }
};
function borrar(folio){
  $.post("../php/borrar_pedido.php", { 
          valorFolio: folio
  }, function(mensaje) {
  $("#resultado_pedido").html(mensaje);
  }); 
};
function entregar(folio){
  M.toast({html :"FOLIO: "+folio, classes: "rounded"});
  $.post("../php/entregar_pedido.php", { 
          valorFolio: folio
  }, function(mensaje) {
  $("#resultado_pedido").html(mensaje);
  }); 
};
</script>
</head>
<main>
<body onload="buscar_pedidos();buscar_pedidos2();">
  <div class="container">
    <div class="row" >
      <h3 class="hide-on-med-and-down">Nuevo Pedido</h3>
      <h5 class="hide-on-large-only">Nuevo Pedido</h5>
    </div>
    <div class="row">
      <div class="input-field col s12 m3 l3">
        <i class="material-icons prefix">people</i>
        <input type="text" id="nombre">
        <label for="nombre">Nombre del Cliente:</label>
      </div>
      <div class="input-field col s9 m3 l3">
        <i class="material-icons prefix">filter_1</i>
        <input type="number" id="orden">
        <label for="orden">Id Orden (Puede ir vacio):</label>
      </div>
      <div class="col s12 l3 m3">
        <label for="fecha_req">Fecha Requerido (Puede ir vacio):</label>
        <input id="fecha_req" type="date" >
      </div>
      <div class="input-field">
        <a onclick="insert_pedidos();" class="waves-effect waves-light btn pink left right">REGISTRAR PEDIDO<i class="material-icons center right">send</i></a>
      </div>
    </div>    
    <div id="resultado_pedido">
      <div class="row"> <br><br>
          <h3 class="hide-on-med-and-down col s12 m6 l6">Pedidos</h3>
          <h5 class="hide-on-large-only col s12 m6 l6">Pedidos</h5>          
      </div>
      <div class="row"> <br><br>
        <h4 class="hide-on-med-and-down col s12 m6 l6">No Autorizados</h4>
        <h6 class="hide-on-large-only col s12 m6 l6">No Autorizados</h6>
        <form class="col s12 m6 l6">
          <div class="row">
            <div class="input-field col s12">
              <i class="material-icons prefix">search</i>
              <input id="busqueda2" name="busqueda2" type="text" class="validate" onkeyup="buscar_pedidos2();">
              <label for="busqueda2">Buscar No Autorizados (ej: #Folio, Nombre de Cliente, IdOrden)</label>
            </div>
          </div>
        </form>
      </div>
      <table class="bordered highlight responsive-table">
          <thead>
            <tr>
              <th>Estatus</th>
              <th>Folio</th>
              <th>Nombre</th>
              <th>Orden</th>
              <th>Fecha Y Hora Creacion</th>
              <th>Fecha Cerrado</th>
              <th>Fecha Requerido</th>
              <th>Registró</th>
              <th>Ver</th>
              <th>Borrar</th>
            </tr>
          </thead>
          <tbody id="pedidosNo">
            
          </tbody>
      </table><br><br>

      <div class="row"> <br><br>
        <h4 class="hide-on-med-and-down col s12 m6 l6">Autorizados</h4>
        <h6 class="hide-on-large-only col s12 m6 l6">Autorizados</h6>
        <form class="col s12 m6 l6">
          <div class="row">
            <div class="input-field col s12">
              <i class="material-icons prefix">search</i>
              <input id="busqueda" name="busqueda" type="text" class="validate" onkeyup="buscar_pedidos();">
              <label for="busqueda">Buscar Autorizados (ej: #Folio, Nombre de Cliente, IdOrden)</label>
            </div>
          </div>
        </form>
      </div>
      <table class="bordered highlight responsive-table">
          <thead>
            <tr>
              <th>Estatus</th>
              <th>Folio</th>
              <th>Nombre</th>
              <th>Orden</th>
              <th>Fecha Y Hora Creacion</th>
              <th>Fecha Cerrado</th>
              <th>Fecha Autorizado</th>
              <th>Fecha Requerido</th>
              <th>Registró</th>
              <th>Ver</th>
              <th>Borrar</th>
            </tr>
          </thead>
          <tbody id="PedidosALL">
          </tbody>
      </table><br><br>

      <h4 class="hide-on-med-and-down col s12 m6 l6">Surtidos</h4>
      <h6 class="hide-on-large-only col s12 m6 l6">Surtidos</h6>
      <table class="bordered highlight responsive-table">
          <thead>
            <tr>
              <th>Folio</th>
              <th>Nombre</th>
              <th>Orden</th>              
              <th>Fecha Y Hora Creacion</th>
              <th>Fecha Cerrado</th>
              <th>Fecha Autorizado</th>
              <th>Fecha Surtido</th>
              <th>Registró</th>
              <th>Ver</th>
              <th>Entregar</th>
            </tr>
          </thead>
          <tbody>
          <?php
          $consulta = mysqli_query($conn,"SELECT * FROM pedidos WHERE estatus = 'Completo' ORDER BY folio DESC");

          if (mysqli_num_rows($consulta) <= 0) {
            echo '<h5 class = "center">No se encontraron pedidos (Completados)</h5>';
          }else{
            //La variable $resultados contiene el array que se genera en la consulta, asi que obtenemos los datos y los mostramos en un bucle.
            while($pedido = mysqli_fetch_array($consulta)){
              $usuario = $pedido['usuario'];
              $datos = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM users WHERE user_id = $usuario"));
              $folio = $pedido['folio'];
              $color = ($pedido['cerrado'] == 0)? 'red': 'green';
          ?>
            <tr>
              <td><?php echo  $folio; ?></td>
              <td><?php echo  $pedido['nombre']; ?></td>
              <td><?php echo  $pedido['id_orden']; ?></td>
              <td><?php echo  $pedido['fecha']; ?><?php echo  $pedido['hora']; ?></td>
              <td><?php echo  $pedido['fecha_cerrado']; ?></td>
              <td><?php echo  $pedido['fecha_autorizado']; ?></td>
              <td><?php echo  $pedido['fecha_completo']; ?></td>
              <td><?php echo  $datos['firstname']; ?></td>
              <td><a href = "../views/detalles_pedido.php?folio=<?php echo  $folio; ?>" class="btn-floating btn-tiny waves-effect waves-light pink"><i class="material-icons">visibility</i></a></td>
              <td><a onclick="entregar(<?php echo  $folio; ?>)" class="btn btn-floating red darken-1 waves-effect waves-light"><i class="material-icons">exit_to_app</i></a></td>
            </tr>
          <?php 
            } //FIN WHILE 
          } // FIN ELSE
          ?>
          </tbody>
      </table><br><br>
    </div>
  </div>
</body>
</main>
</html>