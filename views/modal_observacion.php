<?php
include('../php/conexion.php');

$Folio = $conn->real_escape_string($_POST['valorFolio']);
$id = $conn->real_escape_string($_POST['valorID']);

$material = mysqli_fetch_array( mysqli_query($conn, "SELECT * FROM detalles_pedidos WHERE folio = $Folio AND id = $id"));
?>
<script>
  $(document).ready(function(){
      $('#observacion').modal();
      $('#observacion').modal('open'); 
  });
   function instertar_observacion(){
    var textoFolio = $("input#folio").val();
    var textoIdMat = $("input#id").val();
    var textoDescripcion = $("input#descripcion").val();
    if (textoDescripcion == "") {
      M.toast({html :"Ingresa una descripcion a la observacion...", classes: "rounded"});
    }else{
	    $.post("../php/insert_observacion.php", {
	          valorFolio: textoFolio,
	          valorIdMat: textoIdMat,
	          valorDescripcion: textoDescripcion
	        }, function(mensaje) {
	            $("#materialALL").html(mensaje);
	    });
	}
  };
</script>
<!-- Modal OBSERVACION MATERIAL IMPOTANTE! -->
<div id="observacion" class="modal"><br>
  <div class="modal-content">
    <h5 class="indigo-text darken-2 center" id="materialALL"><b>AGREGAR OBSERVACION AL MATERIAL:</b></h5><br>
    <h6><b> -> <?php echo $material['descripcion']; ?></b></h6><br>
    <h5>Observacón:</h5>
    <form class="row">
    	<div class="col s1"><br></div>
    	<div class="input-field col s12 m8 l8">
            <i class="material-icons prefix">edit</i>
            <input id="descripcion" type="text" class="validate" data-length="200" required>
            <label for="descripcion">Observacion (ej: Solo colocar 3 tornillos en lugar de 10):</label>
            <input id="folio" type="hidden" value="<?php echo $Folio; ?>">
            <input id="id" type="hidden" value="<?php echo $id; ?>">
        </div>
    </form>
  </div><br>
  <div class="modal-footer">
      <a onclick="instertar_observacion();" class="modal-close waves-effect waves-light btn green accent-4 "><b>Continuar</b></a>

      <a href="detalles_pedido.php?folio=<?php echo $Folio; ?>" class="btn waves-effect red accent-4 waves-light"><b>Cancelar</b></a>
  </div><br>
</div>
<!--Cierre modal  OBSERVACION MATERIAL IMPOTANTE! -->