<!DOCTYPE html>
<html lang="en">
<head>
<?php
  include('fredyNav.php');
  include ('../php/cobrador.php');
?>
<title>SIC | Compras</title>
<script>
function buscar_compras(){
  var texto = $("input#busqueda").val();
  $.post("../php/buscar_compras.php", {
      texto: texto,
    }, function(mensaje){
        $("#comprasALL").html(mensaje);
  });
};
function buscar_compras2(){
  var texto = $("input#busqueda2").val();
  $.post("../php/buscar_compras2.php", {
      texto: texto,
    }, function(mensaje){
        $("#comprasNo").html(mensaje);
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
<body onload="buscar_compras();buscar_compras2();">
  <div class="container">
    <div class="row" >
      <h3 class="hide-on-med-and-down">Nueva Compra</h3>
      <h5 class="hide-on-large-only">Nueva Compra</h5>
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
        <a onclick="insert_pedidos();" class="waves-effect waves-light btn pink left right">REGISTRAR COMPRA<i class="material-icons center right">send</i></a>
      </div>
    </div>    
    <div id="resultado_pedido">
      <div class="row"> <br><br>
          <h3 class="hide-on-med-and-down col s12 m6 l6">Compras</h3>
          <h5 class="hide-on-large-only col s12 m6 l6">Compras</h5>          
      </div>
      <div class="row"> <br><br>
        <h4 class="hide-on-med-and-down col s12 m6 l6">No Autorizadas</h4>
        <h6 class="hide-on-large-only col s12 m6 l6">No Autorizadas</h6>
        <form class="col s12 m6 l6">
          <div class="row">
            <div class="input-field col s12">
              <i class="material-icons prefix">search</i>
              <input id="busqueda2" name="busqueda2" type="text" class="validate" onkeyup="buscar_compras2();">
              <label for="busqueda2">Buscar No Autorizadas (ej: #Folio, Nombre de Cliente, IdOrden)</label>
            </div>
          </div>
        </form>
      </div>
      <table class="bordered highlight responsive-table">
          <thead>
            <tr>
              <th>Estatus</th>
              <th>Folio</th>
              <th>Descripción</th>
              <th>Fecha Creacion</th>
              <th>Fecha Cerrado</th>
              <th>Registró</th>
              <th>Ver</th>
              <th>Borrar</th>
            </tr>
          </thead>
          <tbody id="comprasNo">
            
          </tbody>
      </table><br><br>

      <div class="row"> <br><br>
        <h4 class="hide-on-med-and-down col s12 m6 l6">Autorizadas</h4>
        <h6 class="hide-on-large-only col s12 m6 l6">Autorizadas</h6>
        <form class="col s12 m6 l6">
          <div class="row">
            <div class="input-field col s12">
              <i class="material-icons prefix">search</i>
              <input id="busqueda" name="busqueda" type="text" class="validate" onkeyup="buscar_compras();">
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
              <th>Descripción</th>
              <th>Fecha Creacion</th>
              <th>Fecha Cerrado</th>
              <th>Fecha Autorizado</th>
              <th>Registró</th>
              <th>Ver</th>
              <th>Borrar</th>
              <th>Registrada</th>
            </tr>
          </thead>
          <tbody id="comprasALL">
          </tbody>
      </table><br><br>
    </div>
  </div>
</body>
</main>
</html>