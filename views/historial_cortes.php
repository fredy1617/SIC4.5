<!DOCTYPE html>
<html>
<head>
	<title>SIC | Historial Cortes</title>
<?php 
	include('fredyNav.php');
	include('../php/conexion.php');
  include('../php/cobrador.php');
 	include('../php/superAdmin.php');
?>
<script>
  function buscar_corte() {
      var textoDe = $("input#fecha_de").val();
      var textoA = $("input#fecha_a").val();
      var textoUsuario = $("select#usuario").val();
      if (textoUsuario == "") {
        M.toast({html:"Selecciona un usuario.", classes: "rounded"});
      }else{
        $.post("../php/buscar_corte.php", {
            valorDe: textoDe,
            valorA: textoA,
            valorUsuario: textoUsuario
          }, function(mensaje) {
              $("#resultado_pagos").html(mensaje);
          }); 
      }
  };
</script>
</head>
<body>
	<div class="container">
		<div class="row">
			<h3 class="hide-on-med-and-down">Historial de Cortes</h3>
  			<h5 class="hide-on-large-only">Historial de Cortes</h5>
		</div><br><br>
        <div class="row">
            <div class="col s12 l4 m4">
                <label for="fecha_de">De:</label>
                <input id="fecha_de" type="date">    
            </div>
            <div class="col s12 l4 m4">
                <label for="fecha_a">A:</label>
                <input id="fecha_a"  type="date">
            </div>

            <div class="input-field col s12 l4 m4">
              <select id="usuario" class="browser-default">
                <option value="0" selected>Seleccione un usuario</option>
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
                <button class="btn waves-light waves-effect right pink" onclick="buscar_corte();"><i class="material-icons prefix">send</i></button>
            </div>
        </div>
	    <div id="resultado_pagos">
	    </div>        
	</div>
</body>
</html>