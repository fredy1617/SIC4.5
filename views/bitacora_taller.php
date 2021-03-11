<!DOCTYPE html>
<html lang="en">
<head>
<?php
  include('fredyNav.php');
  include('../php/cobrador.php');
?>
<title>SIC | Trabajo Taller</title>
<script>
  function buscar_trabajo_taller() {
      var textoDe = $("input#fecha_de").val();
      var textoA = $("input#fecha_a").val();
      var textoUsuario = $("select#usuario").val();
        $.post("../php/buscar_trabajo_taller.php", {
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
    	<h3 class="hide-on-med-and-down">Trabajo Realizado Taller</h3>
      <h5 class="hide-on-large-only">Trabajo Realizado Taller</h5>
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
                  $sql_usuario = mysqli_query($conn,"SELECT * FROM users WHERE area = 'Taller'");
                  while($usuario = mysqli_fetch_array($sql_usuario)){
                    ?>
                      <option value="<?php echo $usuario['user_id'];?>"><?php echo $usuario['user_name'];?></option>
                    <?php
                  }
                  ?>
                </select>
              </div><br>
            <div>
                <button class="btn waves-light waves-effect right pink" onclick="buscar_trabajo_taller();"><i class="material-icons prefix">send</i></button>
            </div>
        </div>
    <div id="resultado">
    </div>        
  </div>
</body>
</main>
</html>