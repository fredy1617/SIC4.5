<!DOCTYPE html>
<html>
<head>
	<title>SIC | Reporte de Refacciones</title>
<?php
include ('fredyNav.php');
include ('../php/cobrador.php');
include ('../php/admin.php');
?>
<script>
  function buscar_refacciones(){
	var textoDe = $("input#fecha_de").val();
	var textoA = $("input#fecha_a").val();
	if (textoDe == "" || textoA == "")
	{
        M.toast({html:"Ingrese un rango de fechas.", classes: "rounded"});
	}else{
	$.post("../php/buscar_refacciones.php", {
		  valorDe: textoDe,
		  valorA: textoA
		}, function (mensaje){
			$("#datos").html(mensaje);
		});
	}
  };
</script>
</head>
<body>
	<div class="container">
		<div class="row">
			<h3 class="hide-on-med-and-down">Reporte de Refacciones:</h3>	
			<h5 class="hide-on-large-only">Reporte de Refacciones:</h5>	
		</div>
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
                <button class="btn waves-light waves-effect right pink" onclick="buscar_refacciones();"><i class="material-icons prefix">send</i></button>
            </div>
		</div>
		<div class="row" id="datos">			
		</div>
	</div>
</body>
</html>