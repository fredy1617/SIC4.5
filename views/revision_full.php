<!DOCTYPE html>
<html>
<head>
	<title>SIC | Revisión Full</title>
<?php
include ('fredyNav.php');
include ('../php/cobrador.php');
?>
<script>
	function busqueda(){
		var textoIndex = $("select#index").val();
		M.toast({html: "Buscando...", classes: "rounded"});
		$.post("../php/busqueda_revision_full.php", {
          valorIndex: textoIndex,
          }, function(mensaje) {
              $("#resultado_busqueda").html(mensaje);
        });
	};
</script>
</head>
<body>
	<div class="container"><br>
		<div class="row">
			<div class="col s12 m6 l6">
			<h3> Revisión Full:</h3>
			</div>
			<div class="col s8 m2 l2">
			<label><i class="material-icons">assignment</i> Index:</label>
			<div class="input-field">
	          <select id="index" class="browser-default" required>
	            <option value="1" selected>1</option>
	            <option value="2">2</option>
	            <option value="3">3</option>
	            <option value="4">4</option>
	            <option value="5">5</option>
	            <option value="6">6</option>	            
	          </select>
	        </div>
	        </div>
	        <div class="input-field col s2 m2 l2"><br><br>
		        <a onclick="busqueda();" class="waves-effect waves-light btn pink left right"><i class="material-icons center">send</i></a>
		    </div>
		</div>
		<table class="bordered  highlight responsive-table">
		<thead>
			<tr>
				<th>No.Cliente</th>
				<th>Nombre</th>
				<th>Fecha de Corte</th>
				<th>Utimo Pago</th>
				<th>Cantidad</th>
				<th>Ip</th>
				<th>Servidor</th>
				<th>Editar</th>
			</tr>
		</thead>	
		<tbody id="resultado_busqueda">
		
		</tbody>
		</table><br><br>
	</div>
</body>
</html>