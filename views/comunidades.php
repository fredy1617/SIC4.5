<!DOCTYPE html>
<html lang="en">
<head>
<?php
  include('fredyNav.php');
  include ('../php/cobrador.php');
?>
<title>SIC | Comunidades</title>
<script>
function buscar_comunidad(){
  var texto = $("input#busqueda").val();
  $.post("../php/buscar_comunidades.php", {
      texto: texto,
    }, function(mensaje){
        $("#ComunidadesALL").html(mensaje);
  });
}

function insert_comunidad() {
    var textoNombre = $("input#nombre").val();
    var textoInstalacion = $("input#instalacion").val();
    var textoServidor = $("select#servidor").val();
    var textoMunicipio = $("select#municipio").val();
  
    if (textoNombre == "") {
      M.toast({html :"Por favor ingrese el nombre la comunidad.", classes: "rounded"});
    }else if(textoInstalacion == 0){
      M.toast({html :"El precio de instalación no puede quedar en 0.", classes: "rounded"});
    }else if(textoServidor == 0){
      M.toast({html :"Por favor seleccione un Municipio.", classes: "rounded"});
    }else if(textoServidor == 0){
      M.toast({html :"Por favor seleccione un servidor.", classes: "rounded"});
    }else{
      $.post("../php/insert_comunidad.php", {
          valorNombre: textoNombre,
          valorInstalacion: textoInstalacion,
          valorMunicipio: textoMunicipio,
          valorServidor: textoServidor
        }, function(mensaje) {
            $("#resultado_comunidad").html(mensaje);
        }); 
    }
};
</script>
</head>
<main>
<body onload="buscar_comunidad();">
  <div class="container">
  <div class="row" >
    <h3 class="hide-on-med-and-down">Registrar Comunidad</h3>
    <h5 class="hide-on-large-only">Registrar Comunidad</h5>
  </div>
    <div class="row">
      <div class="input-field col s7 m4 l4">
         <i class="material-icons prefix">business</i>
        <input type="text" id="nombre">
        <label for="nombre">Comunidad:</label>
      </div>
      <div class="input-field col s7 m3 l3">
        <select id="municipio" class="browser-default">
          <option value="0" selected>Seleccione un municipio</option>
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
      <div class="input-field col s5 m2 l2">
         <i class="material-icons prefix">monetization_on</i>
        <input type="number" id="instalacion" value="0">
        <label for="instalacion">Costo de Instalación:</label>
      </div>
      <div class="input-field col s7 m3 l3">
        <select id="servidor" class="browser-default">
          <option value="0" selected>Seleccione un servidor</option>
          <?php
          require('../php/conexion.php');
          $sql_serv = mysqli_query($conn, "SELECT * FROM servidores");
          while($servidor = mysqli_fetch_array($sql_serv)){
            ?>
            <option value="<?php echo $servidor['id_servidor'];?>"><?php echo $servidor['nombre'];?></option>
            <?php
          }
          ?>
        </select>
      </div>
      <div class="input-field">
        <a onclick="insert_comunidad();" class="waves-effect waves-light btn pink left right"><i class="material-icons center">send</i></a>
      </div>
    </div>
    <div id="resultado_comunidad"></div>
    <div>
    <div class="row">
      <br><br>
      <h3 class="hide-on-med-and-down col s12 m6 l6">Comunidades</h3>
          <h5 class="hide-on-large-only col s12 m6 l6">Comunidades</h5>

          <form class="col s12 m6 l6">
          <div class="row">
            <div class="input-field col s12">
              <i class="material-icons prefix">search</i>
              <input id="busqueda" name="busqueda" type="text" class="validate" onkeyup="buscar_comunidad();">
              <label for="busqueda">Buscar(#Comunidad, Nombre)</label>
            </div>
          </div>
        </form>
    </div>
            <table class="bordered highlight responsive-table">
                <thead>
                    <tr>
                        <th>No. Comunidad</th>
                        <th>Nombre</th>
                        <th>Municipio</th>
                        <th>Servidor</th>
                        <th>Costo de Instalación</th>
                        <th>Editar</th>
                    </tr>
                </thead>
                <tbody id="ComunidadesALL">
                </tbody>
            </table>
            <br><br>
        </div>
  </div>
</body>
</main>
</html>