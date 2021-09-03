<?php
include('../php/conexion.php');

$Folio = $conn->real_escape_string($_POST['valorFolio']);

?>
<script>
  $(document).ready(function(){
      $('#fecha').modal();
      $('#fecha').modal('open'); 
  });
   function instertar_fecha(){
    var textoFolio = $("input#folio").val();
    var textoFecha = $("input#fecha_req").val();
    if (textoFecha == "") {
      M.toast({html :"Ingresa una fecha...", classes: "rounded"});
    }else{
	    $.post("../php/insert_fecha.php", {
	          valorFolio: textoFolio,
	          valorFecha: textoFecha
	        }, function(mensaje) {
	            $("#res_fecha").html(mensaje);
	    });
	}
  };
</script>
<!-- Modal fecha q IMPOTANTE! -->
<div id="fecha" class="modal"><br>
  <div class="modal-content">
    <h5 class="indigo-text darken-2 center" id="res_fecha"><b>AGREGAR FECHA REQUERIDO AL PEDIDO No.<?php echo $Folio; ?></b></h5><br>
    
    <h5> >>> Fecha <<< </h5>
    <form class="row">
    	<div class="col s1"><br></div>
    	<div class="input-field col s12 m8 l8">
            <div class="col s12 l6 m6">
                <label for="fecha_req">Fecha Requerido:  </label><br>
                <input id="fecha_req" type="date" >
            </div>
            <input id="folio" type="hidden" value="<?php echo $Folio; ?>">
        </div>
    </form>
  </div>
  <div class="modal-footer">
      <a onclick="instertar_fecha();" class="modal-close waves-effect waves-light btn green accent-4 "><b>Continuar</b></a>
      <a href="detalles_pedido.php?folio=<?php echo $Folio; ?>" class="btn waves-effect red accent-4 waves-light"><b>Cancelar</b></a>
  </div><br>
</div>
<!--Cierre modal  FECHA q IMPOTANTE! -->