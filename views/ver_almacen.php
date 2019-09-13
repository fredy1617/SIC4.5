<!DOCTYPE html>
<html>
<head>
	<title>SIC | Almacen</title>
<?php
include('fredyNav.php');
include('../php/conexion.php');
include('../php/cobrador.php');
?>
<script>
	function buscar(){
		var texto = $("input#busqueda").val();
		$.post("../php/buscar_almacen.php",{texto: texto},
			function(mensaje){$("#datos").html(mensaje);});
	};
</script>
</head>
<body onload="buscar();">
	<div class="container">
		<div class="row">
			<h3 class="hide-on-med-and-down col s12 m6 l6">Almacen:</h3>
			<h5 class="hide-on-large-only col s12 m6 l6">Alamcen:</h5>
			<form class="col s12 m6 l6">
				<div class="row">
					<div class="input-field col s12">
						<i class="material-icons prefix">search</i>
						<input type="text" id="busqueda" name="busqueda" onkeyup="buscar();">
						<label for="busqueda"> Buscar.. (Ej. Folio, Nombre)</label>
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
						<th>Fecha Salida</th>
						<th>Salida</th>
					</tr>
				</thead>
				<tbody id="datos">
				</tbody>
			</table>
		</div><br>
	</div>
</body>
</html>