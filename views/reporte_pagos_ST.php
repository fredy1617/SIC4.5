<!DOCTYPE html>
<html lang="en">
<head>
<?php
  include('fredyNav.php');
  include('../php/superAdmin.php');
?>
<title>SIC | Reporte Pagos</title>
<script>
  function busacr_pagos() {
      var textoDe = $("input#fecha_de").val();
      var textoA = $("input#fecha_a").val();
        $.post("../php/busacr_pagosST.php", {
            valorDe: textoDe,
            valorA: textoA
          }, function(mensaje) {
              $("#resultado").html(mensaje);
          }); 
  };
</script>
</head>
<main>
<body>
	<div class="container">
      <br>
    	<h3 class="hide-on-med-and-down">Reporte de Pagos</h3>
      <h5 class="hide-on-large-only">Reporte de Pagos</h5>
        <br>
        <div class="row">
            <div class="col s12 l5 m5">
                <label for="fecha_de">De:</label>
                <input id="fecha_de" type="date" >    
            </div>
            <div class="col s12 l5 m5">
                <label for="fecha_a">A:</label>
                <input id="fecha_a" type="date" >
            </div><br>
            <div>
                <button class="btn waves-light waves-effect right pink" onclick="busacr_pagos();"><i class="material-icons prefix">send</i></button>
            </div>
        </div>
    <div id="resultado">
    </div>        
  </div>
</body>
</main>
</html>