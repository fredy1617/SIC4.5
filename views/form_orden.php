<!DOCTYPE html>
<html>
<head>
	<title>SIC | Orden de Servicio</title>
<?php
include('fredyNav.php');
?>
<script>
function create_orden() {
    var textoNombres = $("input#nombres").val();
    var textoAM = $("input#apellido_M").val();
    var textoAP = $("input#apellido_P").val();
    var textoTelefono = $("input#telefono").val();
    var textoComunidad = $("select#comunidad").val();
    var textoSolicitud = $("textarea#solicitud").val();
    var textoColor = $("textarea#color").val();
    var textoCerca = $("textarea#cercas").val();    
    var textoEsp = $("textarea#especificacion").val();
  
    if (textoNombres == "") {
      M.toast({html: 'El campo Nombre(s) se encuentra vacío.', classes: 'rounded'});
    }else if(textoAM == ""){
      M.toast({html: 'El campo Apellido Materno se encuentra vacío.', classes: 'rounded'});
    }else if(textoAP == ""){
      M.toast({html: 'El campo Apellido Paterno se encuentra vacío.', classes: 'rounded'});
    }else if(textoTelefono == ""){
      M.toast({html: 'El campo Telefono se encuentra vacío.', classes: 'rounded'});
    }else if(textoComunidad == "0"){
      M.toast({html: 'No se ha seleccionado una comunidad aún.', classes: 'rounded'});
    }else if(textoSolicitud == ""){
      M.toast({html: 'El campo Solicitud se encuentra vacío.', classes: 'rounded'});
    }else if(textoColor == ""){
      M.toast({html: 'El campo Color se encuentra vacío.', classes: 'rounded'});
    }else if(textoCerca == ""){
      M.toast({html: 'El campo Cerca De se encuentra vacío.', classes: 'rounded'});
    }else if(textoEsp == ""){
      M.toast({html: 'El campo Especificación se encuentra vacío.', classes: 'rounded'});
    }else{
      $.post("../php/create_orden.php", {
          valorNombres: textoNombres+' '+textoAP+' '+textoAM,
          valorTelefono: textoTelefono,
          valorComunidad: textoComunidad,
          valorSolicitud: textoSolicitud,
          valorReferencia: 'Casa de color: '+textoColor+', Cercas de '+textoCerca+' ('+textoEsp+')'
        }, function(mensaje) {
            $("#orden").html(mensaje);
        }); 
    }
};
</script>
</head>
<body>
	<div class="container">
	  <br>
	  <div>
	    <h3 class="hide-on-med-and-down">Registar Orden de Servicio</h3>
	    <h5 class="hide-on-large-only">Registar Orden de Servicio</h5>
	  </div><br>
	  <div id="orden"></div>
	   <div class="row">
	    <form class="col s12" name="fomulario">
	      <div class="row">
	        <div class="input-field col s12 m4 l4">
	          <i class="material-icons prefix">account_circle</i>
	          <input id="nombres" type="text" class="validate" data-length="30" required>
	          <label for="nombres">Nombre:</label>
	        </div> 
	        <div class="input-field col s12 m4 l4">
	          <input id="apellido_P" type="text" class="validate" data-length="30" required>
	          <label for="apellido_P">Apellido Paterno:</label>
	        </div>
	        <div class="input-field col s12 m4 l4">
	          <input id="apellido_M" type="text" class="validate" data-length="30" required>
	          <label for="apellido_M ">Apellido Materno:</label>
	        </div><br><br> <br><br>  
	        <h6><br><i class="material-icons prefix">comment</i><b> Referencia:</b></h6>
	        <div class="input-field col s12 m4 l4">
	          <textarea id="color" class="materialize-textarea validate" data-length="100" required></textarea>
	          <label for="color">Casa de Color:</label>
	        </div>
	        <div class="input-field col s12 m4 l4">
	          <textarea id="cercas" class="materialize-textarea validate" data-length="100" required></textarea>
	          <label for="cercas">Cercas De:  ej. (Escuela, Iglesia)</label>
	        </div>
	        <div class="input-field col s12 m4 l4">
	          <textarea id="especificacion" class="materialize-textarea validate" data-length="150" required></textarea>
	          <label for="especificacion">Especificación: ej. (Dos pisos, Porton blanco)</label>
	        </div>
	        <div class="col s12 m6 l6"> 
		        <div class="input-field row">
		          <i class="col s1"> <br></i>
		          <select id="comunidad" class="browser-default col s10" required>
		            <option value="0" selected>COMUNIDAD:</option>
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
		          <i class="material-icons prefix">phone</i>
		          <input id="telefono" type="text" class="validate" data-length="13" required>
		          <label for="telefono">Teléfono:</label>
		        </div>   
		    </div>
	         <!-- AQUI SE ENCUENTRA LA DOBLE COLUMNA EN ESCRITORIO.-->
	        <div class="col s12 m6 l6">
	        	<div class="input-field row">
		          <i class="material-icons prefix">comment</i>
		          <textarea id="solicitud" class="
		         materialize-textarea validate" data-length="100" required></textarea>
		          <label for="solicitud">Solicitud Del Cliente:</label>
		        </div>       
	       </div>
	      </div>
		</form>
      <a onclick="create_orden();" class="waves-effect waves-light btn pink right"><i class="material-icons right">send</i>GUARDAR</a>
  </div> 
</div><br>
</div>
</body>
</html>