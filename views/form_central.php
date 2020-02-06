<html>
<head>
	<title>SIC | Formulario Centrales</title>
<?php 
include('fredyNav.php');
include('../php/cobrador.php');
?>
<script>
function insert_central() {
    var textoNombres = $("input#nombres").val();
    var textoAM = $("input#apellido-M").val();
    var textoAP = $("input#apellido-P").val();
    var textoTelefono = $("input#telefono").val();
    var textoComunidad = $("select#comunidad").val();
    var textoDescripcion = $("textarea#descripcion").val();
    var textoDireccion = $("textarea#direccion").val();
    var textoCoordenada = $("input#coordenadas").val();

    if (textoNombres == "") {
      M.toast({html: 'El campo Nombre(s) se encuentra vacío.', classes: 'rounded'});
    }else if(textoAM == ""){
      M.toast({html: 'El campo Apellido Materno se encuentra vacío.', classes: 'rounded'});
    }else if(textoAP == ""){
      M.toast({html: 'El campo Apellido Paterno se encuentra vacío.', classes: 'rounded'});
    }else if(textoTelefono.length < 10){
      M.toast({html: 'El telefono tiene que tener al menos 10 dijitos.', classes: 'rounded'});
    }else if(textoComunidad == "0"){
      M.toast({html: 'No se ha seleccionado una comunidad aún.', classes: 'rounded'});
    }else if(textoDireccion == ""){
      M.toast({html: 'El campo Dirección se encuentra vacío.', classes: 'rounded'});
    }else{
      $.post("../php/insert_central.php", {
          valorNombres: textoNombres+' '+textoAP+' '+textoAM,
          valorTelefono: textoTelefono,
          valorComunidad: textoComunidad,
          valorDireccion: textoDireccion,
          valorDescripcion: textoDescripcion,
          valorCoordenada: textoCoordenada,
        }, function(mensaje) {
            $("#resultado_central").html(mensaje);
        }); 
    }
};
</script>
</head>
<main>
<body>
<div class="container">
  <div class="row" >
      <h3 class="hide-on-med-and-down">Registrar Central</h3>
      <h5 class="hide-on-large-only">Registrar Central</h5>
  </div>
  <div id="resultado_central">
  </div>
   <div class="row">
    <form class="col s12">
      <div class="row">
        <div class="input-field col s12 m4 l4">
          <i class="material-icons prefix">account_circle</i>
          <input id="nombres" type="text" class="validate" data-length="30" required>
          <label for="nombres">Nombre:</label>
        </div> 
        <div class="input-field col s12 m4 l4">
          <input id="apellido-P" type="text" class="validate" data-length="30" required>
          <label for="apellido-P">Apellido Paterno:</label>
        </div> 
        <div class="input-field col s12 m4 l4">
          <input id="apellido-M" type="text" class="validate" data-length="30" required>
          <label for="apellido-M ">Apellido Materno:</label>
        </div> 
        
      <div class="col s12 m6 l6">
        <br>
        <div class="input-field">
          <i class="material-icons prefix">phone</i>
          <input id="telefono" type="text" class="validate" data-length="13" required>
          <label for="telefono">Teléfono:</label>
        </div>               
        <div class="input-field">
          <i class="material-icons prefix">location_on</i>
          <textarea id="direccion" class="materialize-textarea validate" data-length="100" required></textarea>
          <label for="direccion">Direccion:</label>
        </div>
        <div class="input-field">
          <i class="material-icons prefix">edit</i>
          <textarea id="descripcion" class="materialize-textarea validate" data-length="100" required></textarea>
          <label for="descripcion">Descripcion General (ej: Cuenta con solares de 250W):</label>
        </div>
      </div>
         <!-- AQUI SE ENCUENTRA LA DOBLE COLUMNA EN ESCRITORIO.-->
      <div class="col s12 m6 l6">
        <br>
        <div class="input-field">
          <i class="material-icons prefix">location_on</i>
          <input id="coordenadas" type="text" class="validate" data-length="6" required value="0">
          <label for="coordenadas">Coordenadas:</label>
        </div><br>
        <div class="input-field row">
          <i class="col s1"> <br></i>
          <select id="comunidad" class="browser-default col s11" required>
            <option value="0" selected>Comunidad</option>
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
      </div>
      </div>
    </div>
</form>
      <a onclick="insert_central();" class="waves-effect waves-light btn pink right"><i class="material-icons right">send</i>ENVIAR</a>
  </div> 
</div><br>
</body>
</main>
</html>
