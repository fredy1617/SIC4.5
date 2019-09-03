<!DOCTYPE html>
<html>
<head>
	<title>SIC | Fichas</title>
<?php
include ("fredyNav.php");
?>
<script>
	function confirmar(){
		//Ingresamos un mensaje a mostrar
		var mensaje = confirm("¿Desea crear una ficha?");
		//Detectamos si el usuario acepto el mensaje
		if (mensaje) {
			M.toast({html: 'Creando ficha...', classes: 'rounded'});
			$.post("../php/crear_ficha.php", {
			}, function(mensaje){
				$("#res_ficha").html(mensaje);
			});
			
		}
		//Detectamos si el usuario denegó el mensaje
		else{
			M.toast({html: 'Cancelado', classes: 'rounded'});
		}
	};

	function imprime(ficha){
		M.toast({html:'Imprimiendo Ficha.', classes: 'rounded'});
		var a = document.createElement("a");
	      a.target = "_blank";
	      a.href = "../php/imprimir_ficha.php?Ficha="+ficha;
	      a.click();
	};
</script>
</head>
<body>
	<div class="container">
		<div class="row"><br>
			<h2>Generar Ficha:</h2>
			<a class="waves-effect waves-light btn pink right" onclick="confirmar();"><i class="material-icons left">featured_play_list</i>Generar</a>
		</div><br><br>
		<div id="res_ficha"></div>
		<div id="imprime"></div>		
	</div>
</body>
</html>