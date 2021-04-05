<?php
include('../php/conexion.php');
$id_ruta = $conn->real_escape_string($_POST['valorIdRuta']);
?>
<script>
	$(document).ready(function(){
	    $('#pedidos').modal();
	    $('#pedidos').modal('open'); 
	});
</script>

<!-- Modal pedidos IMPOTANTE! -->
<div id="pedidos" class="modal"><br>
  <div class="modal-content">
    <h5 class="red-text darken-2 center"><b>¿QUE TIPO DE PAGO DESEA REALIZAR? <?php echo $id_ruta ?></b></h5>
  </div><br>
  <div class="modal-footer">
     <a href="#" class="modal-action modal-close waves-effect waves-green btn-flat">Cerrar<i class="material-icons right">close</i></a>
  </div><br>
</div>
<!--Cierre modal PAGOS IMPOTANTE! -->