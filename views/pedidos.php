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
function insert_pedidos() {
    var textoNombre = $("input#nombre").val();
    var textoOrden = $("input#orden").val();
    if (textoOrden == 0 || textoOrden == '') {
      textoOrden = "N/A";
    }
    if (textoNombre == "") {
      M.toast({html :"Ingresa un nombre de cliente al pedido.", classes: "rounded"})
    }else{
      $.post("../php/insert_pedidos.php", {
          valorNombre: textoNombre,
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
</script>
</head>
<main>
<body onload="buscar_pedidos();">
  <div class="container">
    <div class="row" >
      <h3 class="hide-on-med-and-down">Nuevo Pedido</h3>
      <h5 class="hide-on-large-only">Nuevo Pedido</h5>
    </div>
    <div class="row">
      <div class="input-field col s12 m5 l5">
        <i class="material-icons prefix">people</i>
        <input type="text" id="nombre">
        <label for="nombre">Nombre del Cliente:</label>
      </div>
      <div class="input-field col s9 m3 l3">
        <i class="material-icons prefix">filter_1</i>
        <input type="number" id="orden">
        <label for="orden">Id Orden (Puede ir vacio):</label>
      </div>
      
      <div class="input-field">
        <a onclick="insert_pedidos();" class="waves-effect waves-light btn pink left right">REGISTRAR PEDIDO<i class="material-icons center right">send</i></a>
      </div>
    </div>    
    <div id="resultado_pedido">
      <div class="row"> <br><br>
          <h3 class="hide-on-med-and-down col s12 m6 l6">Pedidos</h3>
          <h5 class="hide-on-large-only col s12 m6 l6">Pedidos</h5>
          <form class="col s12 m6 l6">
            <div class="row">
              <div class="input-field col s12">
                <i class="material-icons prefix">search</i>
                <input id="busqueda" name="busqueda" type="text" class="validate" onkeyup="buscar_pedidos();">
                <label for="busqueda">Buscar(#Folio, Nombre de Cliente)</label>
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
              <th>Fecha</th>
              <th>Hora</th>
              <th>Registró</th>
              <th>Ver</th>
              <th>Borrar</th>
            </tr>
          </thead>
          <tbody id="PedidosALL">
          </tbody>
      </table><br><br>
    </div>
  </div>
</body>
</main>
</html>