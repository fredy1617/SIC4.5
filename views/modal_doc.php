<?php
include('../php/conexion.php');
$id = $conn->real_escape_string($_POST['valorId']);
?>
<script>
	$(document).ready(function(){
	    $('#modalDoc').modal();
	    $('#modalDoc').modal('open'); 
	 });
</script>
<div id="modalDoc" class="modal">
    <div class="modal-content">
     <h5>Seleccionar Documento Nuevo PDF:</h5> 
     <h6 class="red-text"><b>ATENCION!! seleccionar un archivo PDF ya que solo acepta archivos con extención PDF</b></h6> 
      <form id="respuesta" action="../php/subir_doc.php" method="post" enctype="multipart/form-data">
      <div class="input-field col s12 m6 l6">
          <div class="file-field input-field">
            <div class="btn">
              <span>COTIZACION</span>
              <input type="file" name="documento" id = "documento" required>
            </div>
            <div class="file-path-wrapper">
              <input class="file-path validate" type="text" placeholder="Subir Documento PDF">
            </div>
          </div>
          <input id="id" name="id" type="hidden" value="<?php echo $id ?>">
      </div><br><br><br><br><br>
      <button href="#" class="modal-action modal-close waves-effect waves-green btn red accent-2">Cancelar<i class="material-icons right">close</i></button>
      <button class="btn waves-effect waves-light pink right" type="submit" name="action">Subir<i class="material-icons right">file_upload</i></button>
      </form>
    </div>
</div>