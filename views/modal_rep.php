<?php
include('../php/conexion.php');
date_default_timezone_set('America/Mexico_City');


$Nombre = $conn->real_escape_string($_POST['valorNombre']);
$Telefono = $conn->real_escape_string($_POST['valorTelefono']);
$Direccion = $conn->real_escape_string($_POST['valorDireccion']);
$Referencia = $conn->real_escape_string($_POST['valorReferencia']);
$Coordenadas = $conn->real_escape_string($_POST['valorCoordenada']);
$Descripcion = $conn->real_escape_string($_POST['valorDescripcion']);
$IdCliente = $conn->real_escape_string($_POST['valorIdCliente']);

$reporte_ant=mysqli_query($conn, "SELECT * FROM reportes WHERE id_cliente = $IdCliente ORDER by id_reporte DESC
LIMIT 1");
?>
<script>
 $(document).ready(function(){
    $('#mostrarmodal').modal();
    $('#mostrarmodal').modal('open'); 
 });

 function instertar_reporte(){
    var textoNombre = $("input#nombres").val();
    var textoTelefono = $("input#telefono").val();
    var textoDireccion = $("input#direccion").val();
    var textoReferencia = $("input#referencia").val();
    var textoCoordenadas = $("input#coordenadas").val();
    var textoDescripcion = $("input#descripcion").val();
    var textoIdCliente = $("input#id_cliente").val();
    $.post("../php/insert_reporte.php", {
          valorNombre: textoNombre,
          valorTelefono: textoTelefono,
          valorDireccion: textoDireccion,
          valorReferencia: textoReferencia,
          valorCoordenada: textoCoordenadas,
          valorDescripcion: textoDescripcion,
          valorIdCliente: textoIdCliente 
        }, function(mensaje) {
            $("#mostrar_pagos").html(mensaje);
    });
  };
</script>
<!-- Modal Structure -->
  <div id="mostrarmodal" class="modal">
    <div class="modal-content">
      <h4 class="red-text center">! Advertencia !</h4>
      <p>
        <h6 class="blue-text"><b>Este es el utlimo REPORTE de este usuario(<?php echo $IdCliente; ?>):</b></h6>
        <table class="bordered highlight responsive-table  " id="mostrar_pagos">
          <thead>
            <tr>
              <th>#</th>
              <th>Fecha</th>
              <th>Descripción</th>
              <th>Estatus</th>
            </tr>
          </thead>
          <tbody>
          <?php
          $aux = mysqli_num_rows($reporte_ant);
          if($aux>0){
          while($reporte = mysqli_fetch_array($reporte_ant)){
           
            if($reporte['atendido']==1){
              $atendido = '<span class="green new badge" data-badge-caption="Atendido">';
            }else if($reporte['atendido']==2){
              $atendido = '<span class="yellow darken-3 new badge" data-badge-caption="EnProceso">';
            }else{
              $atendido = '<span class="red new badge" data-badge-caption="Revisar">';
            }
            ?>
            <tr>
              <td><b><?php echo $aux;?></b></td>
              <td><?php echo $reporte['fecha'];?></td>
              <td><?php echo $reporte['descripcion'];?></td>
              <td><?php echo $atendido;?></td>
            </tr>
            <?php
            $aux--;
          }
          }else{
            echo "<center><b><h5>Este cliente aún no ha registrado reportes</h5></b></center>";
          }
          ?>        
        </tbody>
      </table><br>
      <h6 class="blue-text"><b>¿DESEA AGREGAR ESTE NUEVO REPORTE?</b></h6>
      <table class="bordered highlight responsive-table ">
          <thead>
            <tr>
              <th>#</th>
              <th>Fecha</th>
              <th>Descripción</th>
              <th>Estatus</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>1</td>
              <td><?php echo date('Y-m-d'); ?></td>
              <td><?php echo $Descripcion ?></td>
              <td><span class="red new badge" data-badge-caption="Revisar"></td>
            </tr>
          </tbody>
      </table>
      </p>
    </div>
    <div class="modal-footer">
      <form name="formMensualidad">
        <input id="nombres" name="nombres" type="hidden" value="<?php echo $Nombre ?>">
        <input id="telefono" name="telefono" type="hidden" value="<?php echo $Telefono ?>">
        <input id="direccion" name="direccion" type="hidden" value="<?php echo $Direccion ?>">
        <input id="referencia" name="referencia" type="hidden" value="<?php echo $Referencia ?>">
        <input id="coordenadas" name="coordenadas" type="hidden" value="<?php echo $Coordenadas ?>">
        <input id="descripcion" name="descripcion" type="hidden" value="<?php echo $Descripcion ?>">
        <input id="id_cliente" name="id_cliente" type="hidden" value="<?php echo $IdCliente ?>">
      </form>
      <a onclick="instertar_reporte();" class="modal-close waves-effect waves-light btn green accent-4 "><b>Continuar</b></a>

      <form method="post" action="../views/form_reportes.php"><input id="no_cliente" name="no_cliente" type="hidden" value="<?php echo $IdCliente ?>"><button class="btn waves-effect red accent-4 waves-light" type="submit" name="action">
      <b>Cancelar</b>
      </button></form>
    </div>
  </div>