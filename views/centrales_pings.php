<!DOCTYPE html>
<html lang="en">
<head>
<?php
  include('fredyNav.php');
  include ('../php/cobrador.php');
?>
<title>SIC | Central_Ping</title>
<script>
function buscar_central(){
  var texto = $("input#busqueda").val();
  $.post("../php/buscar_central.php", {
      texto: texto,
    }, function(mensaje){
        $("#CentalALL").html(mensaje);
  });
}

function insert_central_ping() {
    var textoComunidad = $("select#comunidad").val();
    var textoIP = $("input#ip").val();
    var textoDescripcion = $("input#descripcion").val();
  
    if(textoComunidad == 0){
      M.toast({html :"Seleccione una comunidad...", classes: "rounded"});
    }else if(textoIP == ""){
      M.toast({html :"Ingrese una Ip...", classes: "rounded"});
    }else{
      $.post("../php/insert_central_ping.php", {
          valorComunidad: textoComunidad,
          valorIP: textoIP,
          valorDescripcion: textoDescripcion
        }, function(mensaje) {
            $("#resultado_central").html(mensaje);
        }); 
    }
};
</script>
</head>
<main>
<body onload="buscar_central();">
  <div class="container">
  <div class="row" >
    <h3 class="hide-on-med-and-down">Registrar Central</h3>
    <h5 class="hide-on-large-only">Registrar Central</h5>
  </div>
    <div class="row">
      <div class="input-field col s7 m4 l4">
        <select id="comunidad" class="browser-default">
          <option value="0" selected>Seleccione una comunidad</option>
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
        <input type="text" id="ip">
        <label for="ip">IP:</label>
      </div>
      <div class="input-field col s7 m5 l5">
         <i class="material-icons prefix">edit</i>
        <input type="text" id="descripcion">
        <label for="descripcion">Descripcion:</label>
      </div>
      <div class="input-field">
        <a onclick="insert_central_ping();" class="waves-effect waves-light btn pink left right"><i class="material-icons center">send</i></a>
      </div>
    </div>
    
    <div id="resultado_central">
    <div class="row">
      <br><br>
      <h3 class="hide-on-med-and-down col s12 m6 l6">Central Pings</h3>
        <h5 class="hide-on-large-only col s12 m6 l6">Central Pings</h5>

        <form class="col s12 m6 l6">
          <div class="row">
            <div class="input-field col s12">
              <i class="material-icons prefix">search</i>
              <input id="busqueda" name="busqueda" type="text" class="validate" onkeyup="buscar_central();">
              <label for="busqueda">Buscar(#Central, IP)</label>
            </div>
        </div>
        </form>
    </div>
      <table class="bordered highlight responsive-table">
        <thead>
          <tr>
            <th>No. Cental</th>
            <th>Comunidad</th>
            <th>Descripcion</th>
            <th>IP</th>
            <th>Servidor</th>
            <th>Editar</th>
          </tr>
        </thead>
        <tbody id="CentalALL">
        </tbody>
      </table><br><br>
    </div>
  </div>
</body>
</main>
</html>