<!DOCTYPE html>
<html>
<head>
	<title>SIC | Clientes Especiales</title>
<?php
include('fredyNav.php');
include ('../php/cobrador.php');
?>
<script>
function llenar(){
  if(document.getElementById('Mantenimiento').checked==true){
    M.toast({html: 'Mantenimiento seleccionado...', classes: 'rounded'});    
    document.fomulario.nombres.value = "Servicios";
    document.fomulario.apellido_P.value = "Integrales";
    document.fomulario.apellido_M.value = "de Computación";
    document.fomulario.telefono.value = "4339256286";
  }else{
    M.toast({html:"Se quito Mantenimiento...", classes: "rounded"});
    document.fomulario.nombres.value = "";
    document.fomulario.apellido_P.value = "";
    document.fomulario.telefono.value = "";
    document.fomulario.apellido_M.value = "";
  } 
};
function insert_cliente() {
    var textoNombres = $("input#nombres").val();
    var textoAM = $("input#apellido_M").val();
    var textoAP = $("input#apellido_P").val();
    var textoTelefono = $("input#telefono").val();
    var textoComunidad = $("select#comunidad").val();
    var textoDescripcion = $("textarea#descripcion").val();
    var textoColor = $("input#color").val();
    var textoCerca = $("textarea#cercas").val();    
    var textoEsp = $("textarea#especificacion").val();
    var textoUsuario = $("select#usuario").val();
    if (document.getElementById('Mantenimiento').checked==true) {
	    textoMantenimiento = 1;
	}else{
	    textoMantenimiento = 0; 
	}
  
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
    }else if(textoUsuario == "0"){
      M.toast({html: 'No se ha seleccionado un Usuario aún.', classes: 'rounded'});
    }else if(textoDescripcion == ""){
      M.toast({html: 'El campo Descripcion se encuentra vacío.', classes: 'rounded'});
    }else if(textoColor == ""){
      M.toast({html: 'El campo Color se encuentra vacío.', classes: 'rounded'});
    }else if(textoCerca == ""){
      M.toast({html: 'El campo Cerca De se encuentra vacío.', classes: 'rounded'});
    }else if(textoEsp == ""){
      M.toast({html: 'El campo Especificación se encuentra vacío.', classes: 'rounded'});
    }else{
      $.post("../php/insert_cliente_especial.php", {
          valorNombres: textoNombres+' '+textoAP+' '+textoAM,
          valorTelefono: textoTelefono,
          valorComunidad: textoComunidad,
          valorDescripcion: textoDescripcion,
          valorReferencia: 'Casa de color: '+textoColor+', Cercas de '+textoCerca+' ('+textoEsp+')',
          valorUsuario: textoUsuario,
          valorMantenimiento: textoMantenimiento,
        }, function(mensaje) {
            $("#reporte_especial").html(mensaje);
        }); 
    }
};
</script>
</head>
<body onload="llenar()">
	<div class="container">
	  <br>
	  <div>
	    <h3 class="hide-on-med-and-down">Registar Reporte Especial</h3>
	    <h5 class="hide-on-large-only">Registar Reporte Especial</h5>
	  </div><br>
	  <div id="reporte_especial">
	  </div>
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
	        </div>  
	        <div class="col s12 m6 l6">
	        <br>
	        <div class="input-field">
	          <i class="material-icons prefix">phone</i>
	          <input id="telefono" type="text" class="validate" data-length="13" required>
	          <label for="telefono">Teléfono:</label>
	        </div>  
	        <div class="input-field row">
	          <i class="col s1"> <br></i>
	          <select id="comunidad" class="browser-default col s10" required>
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
		        <div class="row">
		        <div class="input-field row col s12 m7 l7">
		          <i class="col s1"> <br></i>
		          <select id="usuario" class="browser-default col s10" required>
		            <option value="0" selected >Usuario</option>
		            <?php
		                $sql = mysqli_query($conn,"SELECT * FROM users");
		                while($usuario = mysqli_fetch_array($sql)){
		                  ?>
		                    <option value="<?php echo $usuario['user_id'];?>"><?php echo $usuario['user_name'];?></option>
		                  <?php
		                } 
		                mysqli_close($conn);
		            ?>
		          </select>
		        </div>
		        <div class="col s12 m4 l4 row">
		            <br>
		            <input type="checkbox"  checked id="Mantenimiento" name="Mantenimiento"  onclick="llenar()" />
		            <label for="Mantenimiento">Mantenimiento</label>
		        </div>
		    	</div>
		        <div class="input-field row">
		          <i class="material-icons prefix">comment</i>
		          <textarea id="descripcion" class="
		         materialize-textarea validate" data-length="100" required></textarea>
		          <label for="descripcion">Descripcion:</label>
		        </div>
		      </div>
	         <!-- AQUI SE ENCUENTRA LA DOBLE COLUMNA EN ESCRITORIO.-->
	        <div class="col s12 m6 l6">
	          <h6><i class="material-icons prefix">comment</i> <b>Referencia:</b></h6>
	        <div class="input-field">
	          <input id="color" type="text" class="validate" data-length="20" required>
	          <label for="color">Casa de Color:</label>
	        </div>
	        <div class="input-field">
	          <textarea id="cercas" class="materialize-textarea validate" data-length="100" required></textarea>
	          <label for="cercas">Cercas De:  ej. (Escuela, Iglesia)</label>
	        </div>
	        <div class="input-field">
	          <textarea id="especificacion" class="materialize-textarea validate" data-length="150" required></textarea>
	          <label for="especificacion">Especificación: ej. (Dos pisos, Porton blanco)</label>
	        </div>	        
	       </div>
	      </div>
		</form>
      <a onclick="insert_cliente();" class="waves-effect waves-light btn pink right"><i class="material-icons right">send</i>ENVIAR</a>
  </div> 
</div><br>
</div>
</body>
</html>