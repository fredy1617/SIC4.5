<!DOCTYPE html>
<html>
<head>
	<title>SIC | Reportes Atendidos</title>
<?php 
include('fredyNav.php');
include('../php/conexion.php');
include('../php/cobrador.php');
$sql = mysqli_query($conn, "SELECT * FROM reportes WHERE atendido = 1 OR atendido != NULL ORDER BY id_reporte DESC");
?>
<script>
	function buscar_reporte(tipo) {
		if (tipo == 1) {
			textoTipo = "tecnico";
			var textoDe = $("input#fecha_de1").val();
		    var textoA = $("input#fecha_a1").val();
		    var textoUsuario = $("select#usuario").val();
		    if (textoUsuario == "") {
	      		M.toast({html:"Selecciona un usuario.", classes: "rounded"});
	    	}
		}else{
			textoUsuario = "";
			textoTipo = "cliente";
			var textoDe = $("input#fecha_de").val();
		    var textoA = $("input#fecha_a").val();
		}
	    $.post("../php/buscar_reporte.php", {
	    	  valorTipo: textoTipo,
	          valorDe: textoDe,
	          valorA: textoA,
	          valorUsuario: textoUsuario,
	        }, function(mensaje) {
	            $("#resultado_pagos").html(mensaje);
	        }); 
	};
</script>
</head>
<body>
	<div class="container">
		<div class="row">
			<h3 class="hide-on-med-and-down">Reportes Atendidos</h3>
  			<h5 class="hide-on-large-only">Reportes Atendidos</h5>
		</div>
	 	<br><br>
<!-- ----------------------------  TABs o MENU  ---------------------------------------->
	<div class="row">
	    <div class="col s12">
	    <ul id="tabs-swipe-demo" class="tabs">
	      <li class="tab col s6"><a class="active black-text" href="#test-swipe-1">Tecnico</a></li>
	      <li class="tab col s6"><a class="black-text" href="#test-swipe-2">General</a></li>
	    </ul>
	    </div>
<!-- ----------------------------  FORMULARIO 1 Tabs  ---------------------------------------->
		<div  id="test-swipe-1" class="col s12">
	        <div class="row">
	            <div class="col s12 l4 m4">
	                <label for="fecha_de1">De:</label>
	                <input id="fecha_de1" type="date">    
	            </div>
	            <div class="col s12 l4 m4">
	                <label for="fecha_a1">A:</label>
	                <input id="fecha_a1"  type="date">
	            </div>
	            <div class="input-field col s12 l4 m4">
	              <select id="usuario" class="browser-default">
	                <option value="" selected>Seleccione un usuario</option>
	                <?php 
	                $sql_tecnico = mysqli_query($conn,"SELECT * FROM users ");
	                while($tecnico = mysqli_fetch_array($sql_tecnico)){
	                  ?>
	                    <option value="<?php echo $tecnico['user_id'];?>"><?php echo $tecnico['user_name'];?></option>
	                  <?php
	                }
	                ?>
	              </select>
	            </div>
	            <br><br><br>
	            <div>
	                <button class="btn waves-light waves-effect right pink" onclick="buscar_reporte(1);"><i class="material-icons prefix">send</i></button>
	            </div>
	        </div>
	    </div>
<!-- ----------------------------  FORMULARIO 2 Tabs  ---------------------------------------->
		<div  id="test-swipe-2" class="col s12">
	        <div class="row">
	            <div class="col s12 l4 m4">
	                <label for="fecha_de">De:</label>
	                <input id="fecha_de" type="date">    
	            </div>
	            <div class="col s12 l4 m4">
	                <label for="fecha_a">A:</label>
	                <input id="fecha_a"  type="date">
	            </div><br>
	            <div>
	                <button class="btn waves-light waves-effect right pink" onclick="buscar_reporte(2);"><i class="material-icons prefix">send</i></button>
	            </div>
	        </div>
	    </div>

    </div>
	    <div id="resultado_pagos">
	    </div>        
	</div>
</body>
</html>