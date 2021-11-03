<!DOCTYPE html>
<html>
<head>
	<title>SIC | Orden de Servicio</title>
<?php
include('fredyNav.php');
include ('../php/cobrador.php');
if (isset($_POST['no_cliente']) == false) {
  ?>
  <script>    
    function atras() {
      M.toast({html: "SIN CLIENTE...", classes: "rounded"})
      setTimeout("location.href='form_orden.php'", 800);
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
	function showContent() {
    element2 = document.getElementById("content_o");
    var textoEstatus = $("select#estatus").val();

    if (textoEstatus == 'Cotizado') {
      element2.style.display='block';
    }
    else {
      element2.style.display='none';
    }     
  };
function create_orden(id) {
    var textoNombreC = $("input#nombresC").val();
    var textoTelefono = $("input#telefono").val();
    var textoComunidad = $("select#comunidad").val();
    var textoSolicitud = $("textarea#solicitud").val();
    var textoEstatus = $("select#estatus").val();
    var textoDpto = $("select#dpto").val();
    var textoReferencia = $("textarea#referencia").val();
    No = 'si';
    if (textoEstatus == 'Cotizado') {
      var textoCosto = $("input#costo").val();
      if (textoCosto == '' || textoCosto <= 0) {
        No = 'No';
        text = 'Colocar un costo valido a la orden.';
      }else{
        No = 'Si';
      }
    }else{
    	textoCosto = 0;
    }
  
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
    }else if(textoReferencia == ""){
      M.toast({html: 'El campo Referencia se encuentra vacío.', classes: 'rounded'});
    }else if(No == "No"){
      M.toast({html:""+text, classes: "rounded"})
    }else{
      $.post("../php/create_orden.php", {
          valorNuevo: 'No',
          valorNombres: textoNombreC,
          valorTelefono: textoTelefono,
          valorComunidad: textoComunidad,
          valorEstatus: textoEstatus,
          valorDpto: textoDpto,
          valorSolicitud: textoSolicitud,
          valorReferencia: textoReferencia,
          valorCosto: textoCosto,
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
	         <div class="col s12 m6 l2"> <br><br>
		        <div class="input-field row">
		          <select id="estatus" class="browser-default col s11" onchange="javascript:showContent()">
		            <option value="0" selected>Estatus:</option>
		            <option value="PorConfirmar">Por Confirmar</option>		            
		            <option value="Revisar">Revisar</option>		            
		            <option value="Cotizar">Cotizar</option>		            
		            <option value="Cotizado">Cotizado</option>		            
		          </select>
		        </div>   
		    </div>
		    <div class="col s12 m6 l2"> <br><br>
		        <div class="input-field row">
		          <select id="dpto" class="browser-default col s11">
		            <option value="0" selected>Departamento:</option>
		            <option value="1">Redes</option>		            
		            <option value="2">Taller</option>		            
		            <option value="3">Ventas</option>		            
		          </select>
		        </div>   
		    </div>
	        <div class="col s12 m7 l7">
	        	<div class="input-field row">
		          <i class="material-icons prefix">comment</i>
		          <textarea id="solicitud" class="
		         materialize-textarea validate" data-length="100" required></textarea>
		          <label for="solicitud">Solicitud Del Cliente o Trabjo a Realizar</label>
		        </div>       
	       </div>
	       <div class="input-field col s10 m4 l4" id="content_o" style="display: none;">
		        <i class="material-icons prefix">attach_money</i>
            <input id="costo" type="number" class="validate" data-length="100" required>
            <label for="costo">Costo de la orden:</label>
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