<!DOCTYPE html>
<html>
<head>
	<title>SIC | Clientes</title>
<?php 
include('fredyNav.php');
 ?>
<script >
	function buscar() {
    var texto = $("input#busqueda").val();
	$.post("../php/buscar_dispositivo.php", {
          texto: texto,
        }, function(mensaje) {
            $("#datos").html(mensaje);
        }); 
	};
	function regresa(IdDispositivo){
	  $.post("../php/regresar_listos.php", { 
	          valorIdDispocitivo: IdDispositivo,
	  }, function(mensaje) {
	  $("#mostrar_pagos").html(mensaje);
	  }); 
	};

</script>
</head>
<body onload="buscar();">
	<div class="container">
		<div class="row" id="cambiarP">
			<br><br>
			<h3 class="hide-on-med-and-down col s12 m6 l6">Dispositivos:</h3>
      		<h5 class="hide-on-large-only col s12 m6 l6">Dispositivos:</h5>
      		<div id="mostrar_pagos"></div>
      		<form class="col s12 m6 l6">
		      <div class="row">
		        <div class="input-field col s12">
		          <i class="material-icons prefix">search</i>
		          <input id="busqueda" name="busqueda" type="text" class="validate" onkeyup="buscar();">
		          <label for="busqueda">Buscar Por.. (Ej. Folio, Nombre)</label>
		        </div>
		      </div>
		    </form>
		</div>
		<div class="row">
			<table class="bordered highlight centered responsive-table">
			    <thead>
			      <tr>
			      	<th>Folio</th>
			        <th>Nombre</th>
			        <th>Telefono</th>
			        <th>Dispositivo</th>
			        <th>Estatus</th>
			        <th>Falla</th>
			        <th>Observación</th>
			        <th>Total</th>
			        <th>Entrada</th>
			        <th>Salida</th>
			        <th></th>
			      </tr>
			    </thead>
			    <tbody id="datos">
				</tbody>
			</table>
		</div>
	</div>
</body>
</html>