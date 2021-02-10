<script>
  //FUNCION QUE RIDERECCIONA A UNA NUEVA PESTAÑA DONDE SE CREARA EL TICKET DE CONFIRMACION
  function ticket_confirmar(id){
    var a = document.createElement("a");
        a.target = "_blank";
        //REDIRECCIONA Y ENVIAMOS UN LETRA Y EL ID DEL CORTE
        a.href = "../php/ticket_confirmar.php?id="+id;
        a.click();
  }
</script>
<?php
#INCLUIMOS EL ARCHIVO CON LA CONEXION A LA BASE DE DATOS
include('../php/conexion.php');
#RECIBIMOS EL LA VARIABLE valorId CON EL METODO POST DEL DOCUMENTO corte_pagos.php DEL FORMULARIO CONFIRMAR PAGO
$Id = $conn->real_escape_string($_POST['valorId']);
#SELECCIONAMOS EL CORTE SIN CONFIRMAR SEGUN EL $Id QUE RECIBIMOS
$sql_corte =  mysqli_query($conn, "SELECT * FROM cortes WHERE id_corte = $Id AND confirmar = 0"); 
$MJS = '';//VARIABLE DEL MENSAJE DEL MODAL VACIA
#VERIFICAMOS SI EXISTE UN CORTE SIN CONFORMAR CON ESE ID   
if (mysqli_num_rows($sql_corte) > 0) {
  #RECIBIMOS EL LA VARIABLE valorCantidad CON EL METODO POST DEL DOCUMENTO corte_pagos.php DEL FORMULARIO CONFIRMAR PAGO
  $Cantidad = $conn->real_escape_string($_POST['valorCantidad']);
  #EL SQL LO CONVERTIMOS A UN ARREGLO PARA PODER MANUPULAR LA INFORMACION
  $Corte = mysqli_fetch_array($sql_corte);
  #TOMAMOS LA INFORMACION DEL DEDUCIBLE SEGUN EL ID DEL CORTE
  $sql_Deducible = mysqli_query($conn, "SELECT * FROM deducibles WHERE id_corte = '$Id'");  
  #VERIFICAMOS SI HUBO ALGUN DEDUCIBLE PARA RESTARSELO A LA CANTIDAD DEL CORTE
  if (mysqli_num_rows($sql_Deducible) > 0) {
    $Deducible = mysqli_fetch_array($sql_Deducible);
    $Deducir = $Deducible['cantidad'];
  }else{
    $Deducir = 0;
  }
  #TOMAMOS LA CANTIDAD EN EFECTIVO DEL CORTE Y LE RESTAMOS EL DEDUCIBLE EN CASO DE QUE SI HAYA (VIATICOS)
  $Cantidad_Corte = $Corte['cantidad']-$Deducir;
  #VERIFICAMOS SI LA CANTIDAD INGRESEDA ES MENOS A EL CORTE
  if ($Cantidad < $Cantidad_Corte) {
    #SI LAS CANTIDADES SON DIFERETES GENERAMOS UNA DEUDA
    $Deuda = $Cantidad_Corte-$Cantidad;//SE LE RESTA A LA CANTIDAD EL CORTE LA CANTIDAD RECIBIDA PARA VER EL FALTANTE
    $Cobrador = $Corte['usuario'];//SACAMOS EL ID DEL COBRADOR DEL CORTE
    #MOSTRAR MODAL DE SE CRARA UNA DEUDA SI LA RESPUESTA ES SI CREAR LA DEUDA SI NO SOLO ESTAR EN LA PAGINA DE CORTES ---------
    ?>
    <script>
      $(document).ready(function(){
          $('#Alerta_corte').modal();
          $('#Alerta_corte').modal('open'); 
      });
    </script>
    <!-- Modal MENSAJE DE ALERTA CORTE IMPOTANTE! -->
    <div id="Alerta_corte" class="modal"><br>
      <div class="modal-content">
        <h5 class="red-text darken-2 center"><b>La cantidad ingresada es menor a la cantidad de corte (menos los deducibles) por lo tanto se creara un saldo pendiente por: $<?php echo $Deuda; ?> pesos </b></h5>
      </div><br>
      <div class="modal-footer container">
        <form method="post" action="../php/registrar_deuda_corte.php"><input id="id" name="id" type="hidden" value="<?php echo $Id ?>"><input id="deuda" name="deuda" type="hidden" value="<?php echo $Deuda ?>"><input id="cobrador" name="cobrador" type="hidden" value="<?php echo $Cobrador ?>"><button class="modal-action modal-close waves-effect waves-ligth  pink lighten-2 btn-flat"><b>REALIZAR<i class="material-icons right">done</i></b></button></form>
          <a href="#" class="modal-action modal-close waves-effect waves-green green btn-flat"><b>Cerrar<i class="material-icons right">close</i></b></a>
      </div>  
    </div>
    <!--Cierre modal MENSAJE DE ALERTA CORTE IMPOTANTE! -->
    <?php
  }else if ($Cantidad > $Cantidad_Corte) {    
    #SI NO MOSTRAR MODAL DE NO PUEDES COLOCAR UNA CANTIDAD MAYOR A LA DEL CORTE Y MOSTRAR LAS CANTIDADES MENOS DEDUCIBLES ------------
    ?>
    <script>
      $(document).ready(function(){
          $('#Alerta_corte').modal();
          $('#Alerta_corte').modal('open'); 
      });
    </script>
    <?php
    $MSJ = 'No se puede poner una cantidad mayor a la del corte! <br> Cantidad del corte = $'.$Cantidad_Corte.' (Corte - deducibles)';

  }else{
    #SI NO QUIERE DECIR QUE LA CANTIDAD A CONFIRMAR ES IGUAL A LA DEL CORTE Y SOLO:
    #CAMBIAMOS EL ESTAUS DEL CORTE A confirmar = 1
    if (mysqli_query($conn, "UPDATE cortes SET confirmar = 1 WHERE id_corte = $Id")) {
      #ENVIAR ALERTA DE CAMBIO Y CONFORMACION DEL CORTE
      echo '<script>M.toast({html:"Corte confirmado!", classes: "rounded"})</script>';
      #MANDAMOS IMPRIMIR EL TICKET DE CONFRNACION CON EL ID DEL CORTE 
      echo '<script>ticket_confirmar('.$Id.');</script>';
      #INCLUIMOS EL ARCHIVO PARA ENVIAR EL MENSAJE POR TELEGRAM DEL CORTE
      include ('msj_botTelegramCorte.php');
      ?>
      <script>
        //RECARGAMOS LA PAGINA cortes_pagos.php EN 1300 Milisegundos = 1.3 SEGUNDOS
        setTimeout("location.href='cortes_pagos.php'", 1300);
      </script>
      <?php
    }
  }
}else{
  #SI NO ENCUENTRA UN CORTE MANDAR UNA ALERTA MODAL DE NO HAY CORTE ---------
  ?>
  <script>
    $(document).ready(function(){
        $('#Alerta_corte').modal();
        $('#Alerta_corte').modal('open'); 
    });
  </script>
  <?php
 $MSJ = 'No se encontro ningun corte con este Id ('.$Id.') sin confirmar!';
  
}    
?>
<!-- Modal MENSAJE DE ALERTA CORTE IMPOTANTE! -->
<div id="Alerta_corte" class="modal"><br>
  <div class="modal-content">
    <h5 class="red-text darken-2 center"><b><?php echo $MSJ ?></b></h5>
  </div><br>
  <div class="modal-footer container">
      <a href="#" class="modal-action modal-close waves-effect waves-green pink lighten-2 btn-flat"><b>Cerrar<i class="material-icons right">close</i></b></a>
  </div>  
</div>
<!--Cierre modal MENSAJE DE ALERTA CORTE IMPOTANTE! -->
