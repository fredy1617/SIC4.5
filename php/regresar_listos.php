<?php 
include('../php/conexion.php');
include('is_logged.php');
date_default_timezone_set('America/Mexico_City');

$IdDispocitivo = $conn->real_escape_string($_POST['valorIdDispocitivo']);

?>
<script>
	$(document).ready(function(){
	    $('#regresarPendientes').modal();
	    $('#regresarPendientes').modal('open'); 
	});
	function Mandar_Pendientes(){
		var textoNota = $("input#Nota").val();
	    var textoId = $("input#id").val();
	    $.post("../php/cambiarPendiente.php", {
	          valorNota: textoNota,
	          valorId: textoId, 
	        }, function(mensaje) {
	            $("#cambiarP").html(mensaje);
	    });
	};
</script>
<!--Ventana modal para regresar a PENDIENTES-->
<div id="regresarPendientes" class="modal modal-fixed-footer">
    <div class="modal-content">
        <h3 id="cambiarP">¿Estás seguro de regresar el Folio?</h3><br>
      <p class="center"><b>Al regresear el folio ira directo a pendientes y se le agregara la descripcion que coloques aqui:</b></p><br><br>
     <h5>Nota (¿Porque?):</h5> 
      <form>
      <div class="input-field col s12 m8 l8">
          <i class="material-icons prefix">people</i>
          <input id="Nota" type="text" class="validate" data-length="30" required>
          <label for="Nota">Nota: Ej. ("No funciono o agregacion")</label>
          <input id="id" name="id" type="hidden" value="<?php echo $IdDispocitivo ?>">
      </div>
      </form>
    </div>
    <div class="modal-footer container">
    <a class="modal-action modal-close waves-effect waves-green btn-flat" onclick="Mandar_Pendientes();">Continuar<i class="material-icons right">done</i></a>
      <a href="#" class="modal-action modal-close waves-effect waves-red btn-flat">Cancelar<i class="material-icons right">close</i></a>
    </div>
</div>
<!--.....Cierre de ventana modal para regresar a PENDIENTES-->
