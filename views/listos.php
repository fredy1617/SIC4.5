<html>
<head>
	<title>SIC | Listos</title>
<?php 
include('fredyNav.php');
include('../php/conexion.php');
include('../php/cobrador.php');
?>
<!--Inicia Script de dispositivos-->
<script>
  function buscar() {
    var texto = $("input#busqueda").val();
  $.post("../php/buscar_listos.php", {
          texto: texto,
        }, function(mensaje) {
            $("#datos").html(mensaje);
        }); 
  };
  function almacen(IdDispositivo){
    $.post("../php/ir_almacen.php", { 
            valorIdDispocitivo: IdDispositivo,
    }, function(mensaje) {
    $("#mostrar_resp").html(mensaje);
    }); 
  };
</script>
<!--Termina script dispositivos-->
</head>
<main>
<body onload="buscar();">
<div  id="mostrar_resp"></div>
<div class="container">
  <div class="row">
  <br><br>
  <h3 class="hide-on-med-and-down col s12 m6 l6">Listos:</h3>
  <h5 class="hide-on-large-only col s12 m6 l6">Listos:</h5>
  <form class="col s12 m6 l6">
    <div class="row">
        <div class="input-field col s12">
        <i class="material-icons prefix">search</i>
        <input id="busqueda" name="busqueda" type="text" class="validate" onkeyup="buscar();">
        <label for="busqueda">Buscar.. (Ej. Folio, Nombre)</label>
      </div>
    </div>
  </form>
</div>
  <div class="row">
  <table class="bordered centered highlight responsive-table">
    <thead>
      <tr>
          <th>Folio</th>
          <th>Nombre</th>
          <th>Dispositivo</th>
          <th>Falla</th>
          <th>Observación</th>
          <th>Total</th>
          <th>Fecha Listo</th>
          <th>Salida</th>
          <th>Almacen</th>
      </tr>
    </thead>
    <tbody id="datos">
    </tbody>
  </table>
  </div>
</div><br>
</body>
</main>
</html>
