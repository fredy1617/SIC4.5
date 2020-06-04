<!DOCTYPE html>
<html>
<head>
	<title>SIC | Orden de Servicio</title>
<?php
include('fredyNav.php');
if (isset($_POST['no_cliente']) == false) {
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
$no_cliente = $_POST['no_cliente'];
$Cliente =  mysqli_fetch_array(mysqli_query($conn,"SELECT * FROM especiales WHERE id_cliente=$no_cliente"));
$id_comunidad = $Cliente['lugar'];
$comunidad =  mysqli_fetch_array(mysqli_query($conn,"SELECT * FROM comunidades WHERE id_comunidad=$id_comunidad"));
?>
<script>
function create_orden(id) {
    var textoNombreC = $("input#nombresC").val();
    var textoTelefono = $("input#telefono").val();
    var textoComunidad = $("select#comunidad").val();
    var textoSolicitud = $("textarea#solicitud").val();
    var textoReferencia = $("textarea#referencia").val();
  
    if (textoNombreC == "") {
      M.toast({html: 'El campo Nombre(s) se encuentra vacío.', classes: 'rounded'});
    }else if(textoNombreC.length < 10){
      M.toast({html: 'Almenos debe llevar un Apellido.', classes: 'rounded'});
    }else if(textoTelefono == ""){
      M.toast({html: 'El campo Telefono se encuentra vacío.', classes: 'rounded'});
    }else if(textoComunidad == "0"){
      M.toast({html: 'No se ha seleccionado una comunidad aún.', classes: 'rounded'});
    }else if(textoSolicitud == ""){
      M.toast({html: 'El campo Solicitud se encuentra vacío.', classes: 'rounded'});
    }else if(textoReferencia == ""){
      M.toast({html: 'El campo Referencia se encuentra vacío.', classes: 'rounded'});
    }else{
      $.post("../php/create_orden.php", {
          valorNuevo: 'No',
          valorNombres: textoNombreC,
          valorTelefono: textoTelefono,
          valorComunidad: textoComunidad,
          valorSolicitud: textoSolicitud,
          valorReferencia: textoReferencia,
          id:id
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
	          <div class="input-field col s12 m7 l7">
	            <i class="material-icons prefix">account_circle</i>
	            <input id="nombresC" type="text" class="validate" data-length="30" required  value="<?php echo $Cliente['nombre']; ?>">
	            <label for="nombresC">Nombre (s)   Apellido Paterno   Apellido Materno:</label>
	          </div> 
	          <div class="input-field col s12 m5 l5">
		        <i class="material-icons prefix">phone</i>
		        <input id="telefono" type="text" class="validate" data-length="13" required  value="<?php echo $Cliente['telefono']; ?>">
		        <label for="telefono">Teléfono:</label>
		      </div> 
	        </div>
	        <div class="row"  id="datos"></div> 
	        <h6><br><i class="material-icons prefix">comment</i><b> Referencia:</b></h6>
	        <div class="input-field col s12 m8 l8">
	          <textarea id="referencia" class="materialize-textarea validate" data-length="100" required> <?php echo $Cliente['referencia']; ?></textarea>
	          <label for="referencia">Casa de Color  Cercas De:  ej. (Escuela, Iglesia)  Especificación: ej. (Dos pisos, Porton blanco):</label>
	        </div>
	        <div class="col s12 m4 l4"> <br>
		        <div class="input-field row">
		          <i class="col s1"> <br></i>
		          <select id="comunidad" class="browser-default col s10" required>
		            <option value="<?php echo $Cliente['lugar']; ?>" selected><?php echo $comunidad['nombre']; ?>:</option>
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
	         <!-- AQUI SE ENCUENTRA LA DOBLE COLUMNA EN ESCRITORIO.-->
	        <div class="col s12 m7 l7">
	        	<div class="input-field row">
		          <i class="material-icons prefix">comment</i>
		          <textarea id="solicitud" class="
		         materialize-textarea validate" data-length="100" required></textarea>
		          <label for="solicitud">Solicitud Del Cliente:</label>
		        </div>       
	       </div>
	      </div>
		</form>
      <a onclick="create_orden(<?php echo $no_cliente;?>);" class="waves-effect waves-light btn pink right"><i class="material-icons right">send</i>GUARDAR</a>
  </div> 
</div><br>
</div>
</body>
<?php } ?>
</html>