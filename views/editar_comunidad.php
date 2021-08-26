<!DOCTYPE html>
<html lang="en">
<head>
<?php
  include('fredyNav.php');
?>
<title>SIC | Editar Comunidad</title>
<script>
  function update_comunidad() {
      var textoIdComunidad = $("input#id_comunidad").val();
      var textoNombre = $("input#nombre").val();
      var textoInstalacion = $("input#instalacion").val();
      var textoServidor = $("select#servidor").val();
      var textoMunicipio = $("select#municipio").val();
    
      if (textoNombre == "") {
        M.toast({html:"Por favor ingrese el nombre la comunidad.", classes: "rounded"});
      }else if(textoInstalacion == 0){
        M.toast({html:"El precio de instalación no puede quedar en 0.", classes: "rounded"});
      }else if(textoMunicipio == 0 || textoMunicipio == ''){
        M.toast({html:"Por favor seleccione un Municipio.", classes: "rounded"});
      }else if(textoServidor == 0){
        M.toast({html:"Por favor seleccione un servidor.", classes: "rounded"});
      }else{
        $.post("../php/update_comunidad.php", {
          valorIdComunidad: textoIdComunidad,
            valorNombre: textoNombre,
            valorMunicipio: textoMunicipio,
            valorInstalacion: textoInstalacion,
            valorServidor: textoServidor
          }, function(mensaje) {
              $("#resultado_update_comunidad").html(mensaje);
          }); 
      }
  };
</script>
</head>
<main>
<?php
require('../php/conexion.php');
if (isset($_POST['no_comunidad']) == false) {
  ?>
  <script>    
    function atras() {
      M.toast({html: "Regresando al listado...", classes: "rounded"})
      setTimeout("location.href='comunidades.php'", 1000);
    }
    atras();
  </script>
  <?php
}else{
$id_comunidad = $_POST['no_comunidad'];
?>
<body>
<div id="resultado_update_comunidad">
</div>
<?php

$comunidad = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM comunidades WHERE id_comunidad=$id_comunidad"));
$id_servidor = $comunidad['servidor'];
$servidores = mysqli_fetch_array(mysqli_query($conn, "SELECT id_servidor, nombre FROM servidores WHERE id_servidor=$id_servidor"));
?>
  <div class="container">
  <br>
  <h3 class="hide-on-med-and-down">Editando Comunidad</h3>
  <h5 class="hide-on-large-only">Editando Comunidad</h5>
  <br>
    <div class="row">
     <input type="hidden" id="id_comunidad" value="<?php echo $comunidad['id_comunidad'];?>">
      <div class="input-field col s12 m3 l3">
        <input type="text" id="nombre" value="<?php echo $comunidad['nombre'];?>">
        <label for="nombre">Nombre de la Comunidad:</label>
      </div>
      <div class="input-field col s7 m3 l3">
        <select id="municipio" class="browser-default">
          <option value="<?php echo $comunidad['municipio'];?>" selected><?php echo $comunidad['municipio'];?></option>
          <option value="SOMBRERETE">SOMBRERETE</option>
          <option value="CHALCHIHUITES">CHALCHIHUITES</option>
          <option value="FRESNILLO">FRESNILLO</option>
          <option value="JIMENEZ DEL TEUL">JIMENEZ DEL TEUL</option>
          <option value="MIGUEL AUZA">MIGUEL AUZA</option>
          <option value="RIO GRANDE">RIO GRANDE</option>
          <option value="SAIN ALTO">SAIN ALTO</option>
          <option value="VALPARAISO">VALPARAISO</option>
        </select>
      </div>
      <div class="input-field col s12 m3 l3">
        <input type="number" id="instalacion" value="<?php echo $comunidad['instalacion'];?>">
        <label for="instalacion">Costo de Instalación:</label>
      </div>
      <div class="input-field col s12 m3 l3">
        <select id="servidor" class="browser-default">
          <option value="<?php echo $servidores['id_servidor'];?>" selected><?php echo $servidores['nombre'];?></option>
          <?php          
          $sql_serv = mysqli_query($conn, "SELECT * FROM servidores");
          while($servidor = mysqli_fetch_array($sql_serv)){
            ?>
            <option value="<?php echo $servidor['id_servidor'];?>"><?php echo $servidor['nombre'];?></option>
            <?php
          }
          ?>
        </select>
      </div>
      <div class="input-field col s12 m12 l12">
        <a onclick="update_comunidad();" class="waves-effect waves-light btn pink left right"><i class="material-icons center">send</i></a>
      </div>
    </div>
    <br><br>
  </div>
</body>
<?php
}
?>
</main>
</html>