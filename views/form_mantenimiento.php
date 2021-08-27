<!DOCTYPE html>
<html>
<head>
	<title>SIC | Mantenimiento</title>
<?php
include('fredyNav.php');
include ('../php/cobrador.php');
?>
<script>
	function buscar() {
    var comunidad = $("select#comunidad").val();
    if (comunidad == 0) {
      M.toast({html:"Seleccione una comunidad.", classes: "rounded"});
    }else{
      $.post("../php/buscar_referencia.php", {
            comunidad: comunidad,
          }, function(mensaje) {
              $("#refe").html(mensaje);
      });
    } 
  };
function insert_mantenimiento() {
    var textoComunidad = $("select#comunidad").val();
    var textoDescripcion = $("textarea#descripcion").val();
    var textoReferencia = $("input#referencia").val();
  
    if (textoComunidad == 0) {
      M.toast({html: 'El campo Comunidad se encuentra vacío.', classes: 'rounded'});
    }else if(textoReferencia == ""){
      M.toast({html: 'El campo Referencia se encuentra vacío.', classes: 'rounded'});
    }else if(textoDescripcion == ""){
      M.toast({html: 'El campo Descripcion se encuentra vacío.', classes: 'rounded'});
    }else{
      $.post("../php/insert_mantenimiento.php", {
        valorComunidad: textoComunidad,
        valorDescripcion: textoDescripcion,
        valorReferencia: textoReferencia
      }, function(mensaje) {
        $("#insert").html(mensaje);
      }); 
    }
};
</script>
</head>
<body>
	<div id="insert"></div>
	<div class="container">
	  <br>
	  <div>
	    <h3 class="hide-on-med-and-down">Registar Mantenimiento</h3>
	    <h5 class="hide-on-large-only">Registar Mantenimiento</h5>
	  </div><br>
	  <div class="row">
	    <form class="col s12">
	      <div class="row">
	        <div class="col s12 m6 l6">
		        <div class="input-field row">
		          <i class="col s1"> <br></i>
		          <select id="comunidad" class="browser-default col s10" required onchange="buscar();">
		            <option value="0" selected>Comunidad</option>
		            <?php
		            require('../php/conexion.php');
		            $sql = mysqli_query($conn,"SELECT * FROM comunidades ORDER BY nombre");
		            while($comunidad = mysqli_fetch_array($sql)){ ?>
		                <option value="<?php echo $comunidad['id_comunidad'];?>"><?php echo $comunidad['nombre'].', '.$comunidad['municipio'];?></option>
		            <?php  }  ?>
		          </select>
		        </div>
		        <div class="input-field">
		          <i class="material-icons prefix">comment</i>
			      <textarea id="descripcion" class="
			         materialize-textarea validate" data-length="100" required></textarea>
			      <label for="descripcion">Descripcion del mantenimiento:</label>
		        </div>
	        </div>
	          <!-- AQUI SE ENCUENTRA LA DOBLE COLUMNA EN ESCRITORIO.-->
	        <div class="col s12 m6 l6"><br>
		        <h6><i class="material-icons prefix">comment</i> <b>Referencia:</b></h6>
		        <div class="input-field" id="refe">
		          <input id="referencia" type="text" class="validate" data-length="20" required>
		          <label for="referencia">Breve descripcion:</label>
		        </div>
		    </div>
	      </div>
	    </form>
      	<a onclick="insert_mantenimiento();" class="waves-effect waves-light btn pink right"><i class="material-icons right">send</i>REGISTRAR</a>
  	  </div>
	</div><br>
</div>
</body>
</html>