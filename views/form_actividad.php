<!DOCTYPE html>
<html>
	<head>
		<title>SIC | Actividad</title>
	<?php
	include('fredyNav.php');
	include ('../php/cobrador.php');
	?>
	<script>
		function insert_actividad(bandera) {
		    var textoDescripcion = $("textarea#descripcion").val();
		    textoApoyo = 0;
	        for(var i=1;i<=bandera;i++){
	            if(document.getElementById('tecnico'+i).checked==true){
	              var textoApoyo = $("input#tecnico"+i).val();
	            }
	        }   
	        if(document.getElementById('campo').checked==true){
	            textoCampo = 1;
	          }else{
	            textoCampo = 0;
	          }		  
		    if(textoDescripcion == ""){
		      M.toast({html: 'El campo Descripcion se encuentra vacío.', classes: 'rounded'});
		    }else{
		      $.post("../php/insert_actividad_bitacora.php", {
                valorApoyo: textoApoyo,
                valorSolucion: textoDescripcion,
                valorCampo: textoCampo
		      }, function(mensaje) {
		        $("#insert").html(mensaje);
		      }); 
		    }
		};
	</script>
	</head>
	<body>
		<div id="insert"></div>
		<div class="container"><br><br><br>
		  <div class="row">
		    <h3 class="hide-on-med-and-down">Registar Actividad</h3>
		    <h5 class="hide-on-large-only">Registar Actividad</h5>
		  </div><br>
		  <div class="row">
		    <form class="col s12">
		      <div class="row">
		        <div class="col s12 m6 l6">
			        <div class="input-field">
			          <i class="material-icons prefix">comment</i>
				      <textarea id="descripcion" class="
				         materialize-textarea validate" data-length="100" required></textarea>
				      <label for="descripcion">Descripcion del actividad:</label>
			        </div>
			        <div class="col s12 m6 l6">
	                  <p>
	                    <input type="checkbox" id="campo" />
	                    <label for="campo">En Campo</label>
	                  </p>
                	</div>
		        </div>
		          <!-- AQUI SE ENCUENTRA LA DOBLE COLUMNA EN ESCRITORIO.-->
		        <div class="col s12 m6 l6"><br>
	            	<label>APOYO (solo seleccionar uno):</label>
			        <p>
	                  <?php
	                  $bandera = 1; 
	                  $sql_tecnico = mysqli_query($conn,"SELECT * FROM users WHERE area='Taller' OR area='Redes'  OR user_id = 49 OR user_id = 28 OR user_id = 25");
	                  while($tecnico = mysqli_fetch_array($sql_tecnico)){
	                    ?>
	                    <div class="col s12 m6 l4">
	                      <input type="checkbox" value="<?php echo $tecnico['user_id'];?>" id="tecnico<?php echo $bandera;?>"/>
	                      <label for="tecnico<?php echo $bandera;?>"><?php echo $tecnico['user_name'];?></label>
	                    </div>
	                    <?php
	                    $bandera++;
	                  }$bandera--;
	                  ?>
	                </p>
			    </div>
		      </div>
		    </form>
	  	  </div><br>
	      <a onclick="insert_actividad(<?php echo $bandera;?>);" class="waves-effect waves-light btn pink right"><i class="material-icons right">send</i>REGISTRAR</a>
		</div><br>
	</body>
</html>