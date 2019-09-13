<html>
<head>
	<title>SIC | Pendientes</title>
<?php 
include('fredyNav.php');
include('../php/conexion.php');
include('../php/cobrador.php');
?>
<!--Inicia Script de dispositivos-->
<script>
  function buscar() {
    var texto = $("input#busqueda").val();

  $.post("../php/buscar_pendientes.php", {
          texto: texto,
        }, function(mensaje) {
            $("#datos").html(mensaje);
        }); 
};
</script>
<!--Termina script dispositivos-->
</head>
<main>
<body onload="buscar();">
<div class="container">
  <div class="row">
  <br><br>
  <h3 class="hide-on-med-and-down col s12 m6 l6">Pendientes:</h3>
  <h5 class="hide-on-large-only col s12 m6 l6">Pendientes:</h5>
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
          <th>Dias</th>
          <th>Folio</th>
          <th width="15%">Nombre</th>
          <th>Dispositivo</th>
          <th>Extras</th>
          <th>Falla</th>
          <th>Estatus</th>
          <th>Técnico</th>
          <th>Atender</th>
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
