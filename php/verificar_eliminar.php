<?php
include('../php/conexion.php');
$IdCliente = $conn->real_escape_string($_POST['valorIdCliente']);
?>
<script>
	$(document).ready(function(){
	    $('#modalverificar').modal();
	    $('#modalverificar').modal('open'); 
	 });
   function eliminar_cliente(){
    var textoMotivo = $("input#motivo").val();
    var textoIdCliente = $("input#id_cliente").val();
    $.post("../php/eliminar_instalacion.php", {
          valorMotivo: textoMotivo,
          valorIdCliente: textoIdCliente
        }, function(mensaje) {
            $("#respuesta").html(mensaje);
    });
  };
</script>

<div id="modalverificar" class="modal modal-fixed-footer">
    <div class="modal-content"><br>
        <h3 id="resultado_ruta" class="red-text">¿Seguro de eliminar el cliente no. <?php echo $IdCliente; ?>?</h3><br>
     <h5>Motivo por el cual se eliminara:</h5> 
      <form id="respuesta">
      <div class="input-field col s12 m7 l7">
          <i class="material-icons prefix">create</i>
          <input id="motivo" type="text" class="validate" data-length="50" required>
          <label for="motivo">Motivo: Ej. (Cancelación del Servicio)</label>
          <input id="id_cliente" name="id_cliente" type="hidden" value="<?php echo $IdCliente ?>">
      </div>
      </form>
    </div>
    <div class="modal-footer container">
    <a class="modal-action modal-close waves-effect waves-green btn-flat" onclick="eliminar_cliente();recargar2();">Eliminar<i class="material-icons right">delete</i></a>
      <a href="#" class="modal-action modal-close waves-effect waves-green btn-flat">Cancelar<i class="material-icons right">close</i></a>
    </div>
</div>