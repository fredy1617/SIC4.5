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
	$.post("../php/buscar_cliente.php", {
          texto: texto,
        }, function(mensaje) {
            $("#datos").html(mensaje);
        }); 
	};
</script>
</head>
<body onload="buscar();">
	<div class="container">
		<div class="row">
			<br><br>
			<h3 class="hide-on-med-and-down col s12 m6 l6">Clientes:</h3>
      		<h5 class="hide-on-large-only col s12 m6 l6">Clientes:</h5>
      		<form class="col s12 m6 l6">
		      <div class="row">
		        <div class="input-field col s12">
		          <i class="material-icons prefix">search</i>
		          <input id="busqueda" name="busqueda" type="text" class="validate" onkeyup="buscar();">
		          <label for="busqueda">Buscar.. (Ej. #Cliente, Nombre)</label>
		        </div>
		      </div>
		    </form>
		</div>
		<div class="row">
			<table class="bordered highlight centered responsive-table">
			    <thead>
			      <tr>
			      	<th># Cliente</th>
			        <th>Nombre</th>
			        <th>Servicio</th>
			        <th>Comunidad</th>
			        <th>Telefono</th>
			        <th>Ip</th>
			        <th>Editar</th>
			      </tr>
			    </thead>
			    <tbody id="datos">
				</tbody>
			</table>
		</div>
	</div>
</body>
</html>