<!DOCTYPE html>
<html>
  <head>
  	<title>SIC | Historial Parciales</title>
    <?php 
    	include('fredyNav.php');
    	include('../php/conexion.php');
    ?>
    <script>
      function buscar_corteP() {
        var textoDe = $("input#fecha_de").val();
        var textoA = $("input#fecha_a").val();
        var textoCobrador = $("input#usuario").val();

        if (textoCobrador == "" ) {
          M.toast({html:"Selecciona un cobrador..", classes: "rounded"});
        }else if (textoDe == "" && textoA == "") {
          M.toast({html:"Selecciona un rango de fecha..", classes: "rounded"});
        }else{
          $.post("../php/buscar_corteP.php", {
            valorDe: textoDe,
            valorA: textoA,
            valorCobrador: textoCobrador
          }, function(mensaje) {
            $("#resultado_parcial").html(mensaje);
          }); 
        }
      };
    </script>
  </head>
  <body>
  	<div class="container">
  		<div class="row">
  			<h3 class="hide-on-med-and-down">Historial de Cortes Parciales</h3>
    		<h5 class="hide-on-large-only">Historial de Cortes Parciales</h5>
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
            $sql_user = mysqli_query($conn,"SELECT * FROM users ");
            while($user = mysqli_fetch_array($sql_user)){
              ?>
              <option value="<?php echo $user['user_id'];?>"><?php echo $user['user_name'];?></option>
              <?php
            }
            ?>
          </select>
        </div><br><br><br>
        <div>
          <button class="btn waves-light waves-effect right pink" onclick="buscar_corteP();"><i class="material-icons prefix">send</i></button>
        </div>
      </div>
  	  <div id="resultado_parcial"></div>        
  	</div>
  </body>
</html>