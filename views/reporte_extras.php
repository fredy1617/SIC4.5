<!DOCTYPE html>
<html>
<head>
	<title>SIC | Horas Extra</title>
<?php
include('fredyNav.php');
include('../php/admin-tec.php');
include('../php/cobrador.php');
?>
<script>
	function reporte_extras(){
        var textoDe = $("input#fecha_de").val();
        var textoA = $("input#fecha_a").val();
        if (textoDe == "" || textoA == ""){
            M.toast({html:"Ingrese un rango de fechas.", classes: "rounded"});
        }else{
            $.post("../php/buscar_extras.php", {
              valorDe: textoDe,
              valorA: textoA
            }, function(mensaje) {
                $("#mostrar_extras").html(mensaje);
            }); 
        }
	};
</script>
</head>
<body>
	<div class="container"><br>
    	<h3 class="hide-on-med-and-down">Horas Extra: </h3>
      	<h5 class="hide-on-large-only">Horas Extra: </h5>
        <br>
        <div class="row">
            <div class="col s12 l4 m4">
                <label for="fecha_de">De:</label>
                <input id="fecha_de" type="date">    
            </div>
            <div class="col s12 l4 m4">
                <label for="fecha_a">A:</label>
                <input id="fecha_a" type="date" >
            </div><br>
            <div>
                <button class="btn waves-light waves-effect right pink" onclick="reporte_extras();"><i class="material-icons prefix">send</i></button>
            </div>
        </div>
    	<div id="mostrar_extras"></div>	
	</div>
</body>
</html>