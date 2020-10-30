<?php
#INCLUIMOS EL ARCHIVO EL CUAL HACE LA CONEXION DE LA BASE DE DATOS PARA ACCEDER A LA INFORMACION DEL SISTEMA
include('../php/conexion.php');
#RECIBIMOS EL LA VARIABLE valorSerie CON EL METODO POST QUE ES EL NUMERO DE SERIE DEL MATERIAL
$Serie1 = $conn->real_escape_string($_POST['valorSerieV']);
?>
<script>
  //AL ENTRAR AL DOCUMENTO MANDAMOS LLAMAR EN AUTOMATICO AL MODAL modalverificar
	$(document).ready(function(){
	    $('#modalverificar').modal();
	    $('#modalverificar').modal('open'); 
	});
  //FUNCION LA CUAL ENVIA EL MOTIVO Y EL NUMERO DE SERIE PARA QUE SEA ELIMINADO EL MATERIAL Y SE REGISTRE EN LA TABLA DE HISTORIAL DE BORRADOS (historial_stock)
  function eliminar(){
    var textoMotivo = $("input#motivo").val();
    var textoSerie = $("input#serie").val();
    $.post("../php/eliminar_material.php", {
          valorMotivo: textoMotivo,
          valorSerie: textoSerie
        }, function(mensaje) {
            $("#Continuar").html(mensaje);
    });
  };
</script>
<!-- SE CREA EL MODAL EN EL CUAL SE VERIFICA SI SE ELIMINARA EL MATERIAL Y SE COLOCARA EL MOTIVO -->
<div id="modalverificar" class="modal modal-fixed-footer row col s10 container">
    <div class="modal-content"><br><br><br>
      <h4 id="resultado_ruta" class="red-text">¿Seguro de eliminar el material no. serie: <?php echo $Serie1; ?>?</h4><br><br>
      <h5>Motivo por el cual se eliminara:</h5> 
      <form>
      <div class="input-field col s12 m8 l8">
          <i class="material-icons prefix">create</i>
          <input id="motivo" type="text" class="validate" data-length="50" required>
          <label for="motivo">Motivo: Ej. (Devolucion de luis por baja potencia)</label>
          <input id="serie" name="serie" type="hidden" value="<?php echo $Serie1 ?>">
      </div>
      </form>
    </div>
    <div class="modal-footer container">
    <a class="modal-action modal-close waves-effect waves-green btn-flat" onclick="eliminar();">Eliminar<i class="material-icons right">delete</i></a>
      <a href="stock.php" class="modal-action modal-close waves-effect waves-green btn-flat">Cancelar<i class="material-icons right">close</i></a>
    </div>
</div>