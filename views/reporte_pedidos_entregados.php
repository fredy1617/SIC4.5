<!DOCTYPE html>
<html lang="en">
<head>
<?php
  include('fredyNav.php');
  include ('../php/conexion.php');
  include ('../php/cobrador.php');
  include ('../php/superAdmin.php');
?>
<title>SIC | Reporte Pedidos</title>
<script>
function buscar_pedidos(tipo) {
  entra = "Si";
  if (tipo == 1) {
    var textoBuscar = $("input#busqueda").val(); 
  }else{
    var textoDe = $("input#fecha_de").val();
    var textoA = $("input#fecha_a").val();
    if (textoDe == "" && textoA == "") {
      M.toast({html:"Seleccione un rango de fecha.", classes: "rounded"});      
      entra = "No";    
    }    
  }
  if (entra == "Si") {
      $.post("../php/buscar_pedidos_entregados.php", {
          valorDe: textoDe,
          valorA: textoA,
          texto: textoBuscar,
          valorTipo: tipo
        }, function(mensaje) {
            $("#resultado_pedidos").html(mensaje);
        }); 
  }
};
</script>
</head>
<main>
<body>
	<div class="container">
      <br>
    	<h3 class="hide-on-med-and-down">Reporte de pedidos</h3>
      <h5 class="hide-on-large-only">Reporte de pedidos</h5><br>
      <!-- ----------------------------  TABs o MENU  ---------------------------------------->
    <div class="row">
      <div class="col s12">
        <ul id="tabs-swipe-demo" class="tabs">
          <li class="tab col s6"><a class="active black-text" href="#test-swipe-1">Por Fecha de Surtido</a></li>
          <li class="tab col s6"><a class="black-text" href="#test-swipe-2">Por Folio, Nombre u Orden</a></li>
        </ul>
      </div>
      <br><br><br><br>
      <!-- ----------------------------  FORMULARIO 1 Tabs  ---------------------------------------->
        <div  id="test-swipe-1" class="col s12">
          <div class="row">
            <div class="hide-on-med-and-down col s1">
              <br>
            </div>
            <div class="col s12 l4 m4">
                <label for="fecha_de">De:</label>
                <input id="fecha_de" type="date">    
            </div>
            <div class="col s12 l4 m4">
                <label for="fecha_a">A:</label>
                <input id="fecha_a"  type="date">
            </div>
            <br><br><br>
            <div>
              <button class="btn waves-light waves-effect right pink" onclick="buscar_pedidos(0);"> BUSCAR <i class="material-icons prefix right">send</i></button>
            </div>
          </div>
        </div>
        <!-- ----------------------------  FORMULARIO 2 Tabs  ---------------------------------------->
        <div  id="test-swipe-2" class="col s12">
          <div class="row">
            <div class="col s12 l4 m4">
                <h3>Pedidos: </h3> 
            </div>
            <div class="col s12 l7 m7">
                <div class="input-field row">
                  <i class="material-icons prefix">search</i>
                  <input id="busqueda" name="busqueda" type="text" class="validate" onkeyup="buscar_pedidos(1);">
                  <label for="busqueda">Buscar(Folio ej: 134, #Orden ej: 10023, Nombre ej: Material SIC)</label>
                </div>  
            </div>  
          </div>
        </div>
    <div id="resultado_pedidos">
    </div>        
    </div>
</body>
</main>
</html>