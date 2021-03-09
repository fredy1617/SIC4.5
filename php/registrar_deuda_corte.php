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
#RECIBIMOS EL LA VARIABLE id, deuda, cobrador CON EL METODO POST DEL DOCUMENTO  confirmar_pagos.php DEL FORMULARIO (MODAL) CONFIRMAR PAGO DEUDA
$Id = $conn->real_escape_string($_POST['id']);
$Deuda = $conn->real_escape_string($_POST['deuda']);
$Cobrador = $conn->real_escape_string($_POST['cobrador']);
#CREAR UNA DEUDA AL CORTE PARA AGREGARSELA COBRADOR
mysqli_query($conn,"INSERT INTO deudas_cortes (id_corte, cantidad, cobrador) VALUES('$Id', '$Deuda', '$Cobrador')");
#CAMBIAMOS EL ESTAUS DEL CORTE A confirmar = 1
if (mysqli_query($conn, "UPDATE cortes SET confirmar = 1 WHERE id_corte = $Id")) {
    #MANDAMOS IMPRIMIR EL TICKET DE CONFRNACION CON EL ID DEL CORTE 
    echo '<script>ticket_confirmar('.$Id.');</script>';
    #INCLUIMOS EL ARCHIVO PARA ENVIAR EL MENSAJE POR TELEGRAM DEL CORTE
    include ('msj_botTelegramCorte.php');
    ?>
    <script>
    	var a = document.createElement("a");
	    a.href = "../views/cortes_pagos.php";
        a.click();
    </script>
    <?php
}
?>