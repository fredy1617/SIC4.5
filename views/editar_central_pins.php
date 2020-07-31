<!DOCTYPE html>
<html lang="en">
<head>
<?php
  include('fredyNav.php');
?>
<title>SIC | Editar Central Pings</title>
<script>
function update_central_ping(id) {
    var textoComunidad = $("select#comunidad").val();
    var textoIP = $("input#ip").val();
    var textoDescripcion = $("input#descripcion").val();
  
    if(textoComunidad == 0){
      M.toast({html :"Seleccione una comunidad...", classes: "rounded"});
    }else if(textoIP == ""){
      M.toast({html :"Ingrese una Ip...", classes: "rounded"});
    }else{
      $.post("../php/update_central_ping.php", {
          valorId: id,
          valorComunidad: textoComunidad,
          valorIP: textoIP,
          valorDescripcion: textoDescripcion
        }, function(mensaje) {
            $("#resultado_update").html(mensaje);
        }); 
    }
};
</script>
</head>
<main>
<?php
require('../php/conexion.php');
if (isset($_POST['id']) == false) {
  ?>
  <script>    
    function atras() {
      M.toast({html: "Regresando al listado...", classes: "rounded"})
      setTimeout("location.href='centrales_pings.php'", 1000);
    }
    atras();
  </script>
  <?php
}else{
$id = $_POST['id'];
?>
<body>
<div id="resultado_update">
</div>
<?php

$Central = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM centrales_pings WHERE id=$id"));
$id_comunidad = $Central['comunidad'];
$comunidad1 = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM comunidades WHERE id_comunidad=$id_comunidad"));

?>
  <div class="container">
  <br>
  <div class="row" >
    <h3 class="hide-on-med-and-down">Editar Central</h3>
    <h5 class="hide-on-large-only">Editar Central</h5>
  </div>
    <div class="row">
      <div class="input-field col s7 m4 l4">
        <select id="comunidad" class="browser-default">
          <option value="<?php echo $comunidad1['id_comunidad'];?>"><?php echo $comunidad1['nombre'];?></option>
          <?php
          require('../php/conexion.php');
          $sql_comunidades = mysqli_query($conn, "SELECT * FROM comunidades ORDER BY nombre");
          while($comunidad = mysqli_fetch_array($sql_comunidades)){
            ?>
            <option value="<?php echo $comunidad['id_comunidad'];?>"><?php echo $comunidad['nombre'];?></option>
            <?php
          }
          ?>
        </select>
      </div>
      <div class="input-field col s5 m3 l3">
         <i class="material-icons prefix">settings_ethernet</i>
        <input type="text" id="ip" value="<?php echo $Central['ip'];?>">
        <label for="ip">IP:</label>
      </div>
      <div class="input-field col s7 m5 l5">
         <i class="material-icons prefix">edit</i>
        <input type="text" id="descripcion" value="<?php echo $Central['descripcion'];?>">
        <label for="descripcion">Descripcion:</label>
      </div>
      <div class="input-field">
        <a onclick="update_central_ping(<?php echo $id;?>);" class="waves-effect waves-light btn pink left right"><i class="material-icons center">send</i></a>
      </div>
    </div>
  </div>
</body>
<?php
}
?>
</main>
</html>