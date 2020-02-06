<html>
<head>
	<title>SIC | Editar Central</title>
</head>
<?php 
include('fredyNav.php');
include('../php/cobrador.php');
if (isset($_POST['id_central']) == false) {
  ?>
  <script>    
    function atras() {
      M.toast({html: "Regresando a centrales.", classes: "rounded"})
      setTimeout("location.href='centrales.php'", 800);
    };
    atras();
  </script>
  <?php
}else{
$id_central = $_POST['id_central'];
$central = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM centrales WHERE id='$id_central'"));
$id_comunidad = $central['comunidad'];
$comunidad = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM comunidades WHERE id_comunidad='$id_comunidad'"));
?>
<script>
function update_central(IdCentral) {
    var textoNombres = $("input#nombres").val();
    var textoTelefono = $("input#telefono").val();
    var textoComunidad = $("select#comunidad").val();
    var textoDireccion = $("textarea#direccion").val();
    var textoDescripcion = $("textarea#descripcion").val();
    var textoCoordenada = $("input#coordenadas").val();

    if (textoNombres == "") {
      M.toast({html: 'El campo Nombre(s) se encuentra vacío.', classes: 'rounded'});
    }else if(textoTelefono.length < 10){
      M.toast({html: 'El telefono tiene que tener al menos 10 dijitos.', classes: 'rounded'});
    }else if(textoComunidad == "0"){
      M.toast({html: 'No se ha seleccionado una comunidad aún.', classes: 'rounded'});
    }else if(textoDireccion == ""){
      M.toast({html: 'El campo Dirección se encuentra vacío.', classes: 'rounded'});
    }else{
      $.post("../php/update_central.php", {
          valorIdCentral: IdCentral,
          valorNombres: textoNombres,
          valorTelefono: textoTelefono,
          valorComunidad: textoComunidad,
          valorDireccion: textoDireccion,
          valorDescripcion: textoDescripcion,
          valorCoordenada: textoCoordenada
        }, function(mensaje) {
            $("#resultado_central").html(mensaje);
        }); 
    }
};
</script>

<body>
<div class="container">
  <div class="row" >
      <h3 class="hide-on-med-and-down">Editar Central</h3>
      <h5 class="hide-on-large-only">Editar Central</h5>
  </div>
  <div id="resultado_central">
  </div>
   <div class="row">
    <form class="col s12">
      <div class="row">  
      <div class="col s12 m6 l6">
        <br>
        <div class="input-field">
          <i class="material-icons prefix">account_circle</i>
          <input id="nombres" type="text" class="validate" data-length="30" value="<?php echo $central['nombre'];?>" required>
          <label for="nombres">Nombre:</label>
        </div> 
        <div class="input-field">
          <i class="material-icons prefix">phone</i>
          <input id="telefono" type="text" class="validate" data-length="13" value="<?php echo $central['telefono'];?>" required>
          <label for="telefono">Teléfono:</label>
        </div>               
        <div class="input-field">
          <i class="material-icons prefix">location_on</i>
          <textarea id="direccion" class="
         materialize-textarea validate" data-length="100" required><?php echo $central['direccion'];?></textarea>
          <label for="direccion">Direccion:</label>
        </div>
      </div>
         <!-- AQUI SE ENCUENTRA LA DOBLE COLUMNA EN ESCRITORIO.-->
      <div class="col s12 m6 l6">
        <br>
        <div class="input-field">
          <i class="material-icons prefix">location_on</i>
          <input id="coordenadas" type="text" class="validate" data-length="6" value="<?php echo $central['coordenadas'];?>" required value="0">
          <label for="coordenadas">Coordenadas:</label>
        </div>
        <div class="input-field row">
          <i class="col s1"> <br></i>
          <select id="comunidad" class="browser-default col s11" required>
            <option value="<?php echo $comunidad['id_comunidad'];?>" selected><?php echo $comunidad['nombre'];?></option>
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
        <div class="input-field">
          <i class="material-icons prefix">edit</i>
          <textarea id="descripcion" class="materialize-textarea validate" data-length="100" required><?php echo $central['descripcion_gral'];?></textarea>
          <label for="descripcion">Descripcion General (ej: Cuenta con solares de 250W):</label>
        </div>
      </div>
      </div>
    </div>
</form>
      <a onclick="update_central(<?php echo $central['id'];?>);" class="waves-effect waves-light btn pink right"><i class="material-icons right">send</i>ENVIAR</a>
  </div> 
</div><br>
</body>
<?php } ?>
</html>
