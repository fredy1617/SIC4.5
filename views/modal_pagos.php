<?php
include('../php/conexion.php');
$id_cliente = $conn->real_escape_string($_POST['valorIdCliente']);
?>
<script>
	$(document).ready(function(){
	    $('#pagos').modal();
	    $('#pagos').modal('open'); 
	});
</script>

<!-- Modal PAGOS IMPOTANTE! -->
<div id="pagos" class="modal"><br>
  <div class="modal-content">
    <h5 class="red-text darken-2 center"><b>¿QUE TIPO DE PAGO DESEA REALIZAR?</b></h5>
  </div><br>
  <div class="modal-footer">
      <form method="post" action="../views/otros_pagos.php"><input id="no_cliente" name="no_cliente" type="hidden" value="<?php echo $id_cliente ?>"><button class="modal-action modal-close waves-effect waves-ligth  pink lighten-4 btn-flat"><b>OTROS PAGOS<i class="material-icons left">payment</i></b></button></form>
      <form method="post" action="../views/pagos_telefono.php"><input id="no_cliente" name="no_cliente" type="hidden" value="<?php echo $id_cliente ?>"><button class="modal-action modal-close waves-effect waves-ligth  pink lighten-3 btn-flat"><b>TELEFONO<i class="material-icons left">phone</i></b></button></form>
      <form method="post" action="../views/pagos_internet.php"><input id="no_cliente" name="no_cliente" type="hidden" value="<?php echo $id_cliente ?>"><button class="modal-action modal-close waves-effect waves-ligth  pink lighten-2 btn-flat"><b>INTERNET<i class="material-icons left">wifi</i></b></button></form>
  </div><br>
</div>
<!--Cierre modal PAGOS IMPOTANTE! -->