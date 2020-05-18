<!DOCTYPE html>
<html>
<head>
  <title>SIC | Activos</title>
<?php
include('fredyNav.php')
?>
<script>
  function buscar() {
    var comunidad = $("select#comunidad").val();
    if (comunidad == 0) {
      M.toast({html:"Seleccione una comunidad.", classes: "rounded"});
    }else{
      $.post("../php/buscar_activos.php", {
            comunidad: comunidad,
          }, function(mensaje) {
              $("#mostrar").html(mensaje);
      });
    } 
  };
</script>
</head>
<main>
<body onload="buscar();">
  <div class="container">
    <div class="row">
      <br><br>
      <h3 class="hide-on-med-and-down col s12 m7 l7">Clientes Por Comunidad:</h3>
          <h5 class="hide-on-large-only col s12 m7 l7">Clientes Por Comunidad:</h5>
          <form class="col s10 m4 l4">
          <div class="row"><br>
            <select id="comunidad" class="browser-default col s10" required onchange="buscar();">
              <option value="0" selected>SELECCIONE LA COMUNIDAD</option>
              <option value="Todos">TODOS</option>
              <?php
              require('../php/conexion.php');
                  $sql = mysqli_query($conn,"SELECT * FROM comunidades ORDER BY nombre");
                  while($comunidad = mysqli_fetch_array($sql)){
                    ?>
                      <option value="<?php echo $comunidad['id_comunidad'];?>"><?php echo $comunidad['nombre'];?></option>
                    <?php
                  } 
              ?>
            </select>
          </div>
          </form>
      </div>
  </div>
  <div id="mostrar"></div>
</body>
</main>
</html>