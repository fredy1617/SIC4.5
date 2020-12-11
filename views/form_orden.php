<!DOCTYPE html>
<html>
<head>
	<title>SIC | Orden de Servicio</title>
<?php
include('fredyNav.php');
include ('../php/cobrador.php');
?>
<script>
function buscar() {
    var texto = $("input#nombresC").val();
	$.post("../php/orden_cliente.php", {
          texto: texto,
        }, function(mensaje) {
            $("#datos").html(mensaje);
        }); 
};
function create_orden() {
    var textoNombreC = $("input#nombresC").val();
    var textoTelefono = $("input#telefono").val();
    var textoComunidad = $("select#comunidad").val();
    var textoEstatus = $("select#estatus").val();
    var textoDpto = $("select#dpto").val();
    var textoSolicitud = $("textarea#solicitud").val();
    var textoColor = $("textarea#color").val();
    var textoCerca = $("textarea#cercas").val();    
    var textoEsp = $("textarea#especificacion").val();
  
    if (textoNombreC == "") {
      M.toast({html: 'El campo Nombre(s) se encuentra vacío.', classes: 'rounded'});
    }else if(textoNombreC.length < 10){
      M.toast({html: 'Almenos debe llevar un Apellido.', classes: 'rounded'});
    }else if(textoTelefono == ""){
      M.toast({html: 'El campo Telefono se encuentra vacío.', classes: 'rounded'});
    }else if(textoComunidad == "0"){
      M.toast({html: 'No se ha seleccionado una comunidad aún.', classes: 'rounded'});
    }else if(textoEstatus == "0"){
      M.toast({html: 'No se ha seleccionado un Estatus aún.', classes: 'rounded'});
    }else if(textoDpto == "0"){
      M.toast({html: 'No se ha seleccionado una Departamento aún.', classes: 'rounded'});
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
          valorNuevo: 'Si',
          valorNombres: textoNombreC,
          valorTelefono: textoTelefono,
          valorComunidad: textoComunidad,
          valorEstatus: textoEstatus,
          valorDpto: textoDpto,
          valorSolicitud: textoSolicitud,
          valorReferencia: 'Casa de color: '+textoColor+', Cercas de '+textoCerca+' ('+textoEsp+')'
        }, function(mensaje) {
            $("#orden").html(mensaje);
        }); 
    }
};
</script>
</head>
<body onload="buscar();">
	<div class="container">
	  <br>
	  <div>
	    <h3 class="hide-on-med-and-down">Registar Orden de Servicio</h3>
	    <h5 class="hide-on-large-only">Registar Orden de Servicio</h5>
	  </div><br>
	  <div id="orden"></div>
	   <div class="row">
	    <div class="col s12" name="fomulario">
	        <div class="row">
	          <div class="input-field col s12 m7 l7">
	            <i class="material-icons prefix">account_circle</i>
	            <input id="nombresC" type="text" class="validate" data-length="30"  onkeyup="buscar();">
	            <label for="nombresC">Nombre (s)   Apellido Paterno   Apellido Materno:</label>
	          </div> 
	          <div class="input-field col s12 m5 l5">
		        <i class="material-icons prefix">phone</i>
		        <input id="telefono" type="text" class="validate" data-length="13" >
		        <label for="telefono">Teléfono:</label>
		      </div> 
	        </div>
	        <div class="row"  id="datos"></div> 
	        <h6><br><i class="material-icons prefix">comment</i><b> Referencia:</b></h6>
	        <div class="input-field col s12 m4 l4">
	          <textarea id="color" class="materialize-textarea validate" data-length="100" ></textarea>
	          <label for="color">Casa de Color:</label>
	        </div>
	        <div class="input-field col s12 m4 l4">
	          <textarea id="cercas" class="materialize-textarea validate" data-length="100" ></textarea>
	          <label for="cercas">Cercas De:  ej. (Escuela, Iglesia)</label>
	        </div>
	        <div class="input-field col s12 m4 l4">
	          <textarea id="especificacion" class="materialize-textarea validate" data-length="150" ></textarea>
	          <label for="especificacion">Especificación: ej. (Dos pisos, Porton blanco)</label>
	        </div>
	        <div class="col s12 m6 l3"> <br>
		        <div class="input-field row">
		          <select id="comunidad" class="browser-default col s10">
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
		    </div>
		    <div class="col s12 m6 l2"> <br>
		        <div class="input-field row">
		          <select id="estatus" class="browser-default col s11">
		            <option value="0" selected>Estatus:</option>
		            <option value="PorConfirmar">Por Confirmar</option>		            
		            <option value="Revisar">Revisar</option>		            
		            <option value="Cotizar">Cotizar</option>		            
		          </select>
		        </div>   
		    </div>
		    <div class="col s12 m6 l2"> <br>
		        <div class="input-field row">
		          <select id="dpto" class="browser-default col s11">
		            <option value="0" selected>Departamento:</option>
		            <option value="1">Redes</option>		            
		            <option value="2">Taller</option>		            
		            <option value="3">Ventas</option>		            
		          </select>
		        </div>   
		    </div>
	        <div class="col s12 m6 l5">
	        	<div class="input-field row">
		          <i class="material-icons prefix">comment</i>
		          <textarea id="solicitud" class="
		         materialize-textarea validate" data-length="100"></textarea>
		          <label for="solicitud">Solicitud Del Cliente:</label>
		        </div>       
	       </div>
	      </div>
		</div>
      <a onclick="create_orden();" class="waves-effect waves-light btn pink right"><i class="material-icons right">send</i>GUARDAR</a>
  </div> 
</div><br>
</div>
</body>
</html>