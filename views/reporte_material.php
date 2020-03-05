<!DOCTYPE html>
<html lang="en">
<head>
<?php
  include('fredyNav.php');
  include('../php/cobrador.php');
?>
<title>SIC | Reporte Materiales</title>
<script>
  function buscar_material() {
      var textoDe = $("input#fecha_de").val();
      var textoA = $("input#fecha_a").val();
      var textoUsuario = $("select#usuario").val();
        $.post("../php/buscar_material.php", {
            valorDe: textoDe,
            valorA: textoA,
            valorUsuario:textoUsuario
          }, function(mensaje) {
              $("#resultado").html(mensaje);
          }); 
  };
</script>
</head>
<main>
<body>
	<div class="container">
      <br>
    	<h3 class="hide-on-med-and-down">Reporte de Materiales</h3>
      <h5 class="hide-on-large-only">Reporte de Materiales</h5>
        <br>
        <div class="row">
            <div class="col s12 l4 m4">
                <label for="fecha_de">De:</label>
                <input id="fecha_de" type="date" >    
            </div>
            <div class="col s12 l4 m4">
                <label for="fecha_a">A:</label>
                <input id="fecha_a" type="date" >
            </div>
            <div class="input-field col s12 l3 m3">
                <select id="usuario" class="browser-default">
                  <option value="" selected>Seleccione un usuario</option>
                  <?php 
                  $sql_usuario = mysqli_query($conn,"SELECT * FROM users WHERE area != 'Cobrador' AND area != 'Oficina' AND user_id != 10 AND user_id != 56 AND user_id != 61 AND user_id != 62  AND user_id != 54");
                  while($usuario = mysqli_fetch_array($sql_usuario)){
                    ?>
                      <option value="<?php echo $usuario['user_name'];?>"><?php echo $usuario['user_name'];?></option>
                    <?php
                  }
                  ?>
                </select>
              </div><br>
            <div>
                <button class="btn waves-light waves-effect right pink" onclick="buscar_material();"><i class="material-icons prefix">send</i></button>
            </div>
        </div>
    <div id="resultado">
    </div>        
  </div>
</body>
</main>
</html>