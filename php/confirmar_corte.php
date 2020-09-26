<script>
  //FUNCION QUE RIDERECCIONA A UNA NUEVA PESTAÑA DONDE SE CREARA EL TICKET DE CONFIRMACION
  function ticket_confirmar(r,id){
    var a = document.createElement("a");
        a.target = "_blank";
        //REDIRECCIONA Y ENVIAMOS UN LETRA Y EL ID DEL CORTE
        a.href = "../php/ticket_confirmar.php?r="+r+"&id="+id;
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
	#VERIFICAMOS SI LAS CANTIDADES SON DIFERENTES
    if ($Cantidad != $Cantidad_Corte) {
    	$Deuda = $Cantidad_Corte-$Cantidad;//SE LE RESTA A LA CATIDAD EL CORTE LA CANTIDAD RECIBIDA PARA VER EL FALTANTE
    	$Cobrador = $Corte['usuario'];//SACAMOS EL ID DEL COBRADOR DEL CORTE
    	#CREAR UNA DEUDA AL CORTE PARA AGREGARSELA COBRADOR
    	if (mysqli_query($conn,"INSERT INTO deudas_cortes (id_corte, cantidad, cobrador) VALUES('$Id', '$Deuda', '$Cobrador')")) {
			echo '<script>M.toast({html:"Deuda generada!", classes: "rounded"})</script>';
    		#SI LAS CANTIDADES SON IGUALES CAMBIAR ESTAUS DEL CORTE A confirmar = 1
	    	if (mysqli_query($conn, "UPDATE cortes SET confirmar = 1 WHERE id_corte = $Id")) {
	    		#ENVIAR ALERTA DE CAMBIO Y CONFORMACION DEL CORTE
				  echo '<script>M.toast({html:"Corte confirmado!", classes: "rounded"})</script>';
          #MANDAMOS IMPRIMIR EL TICKET DE CONFRNACION CON EL ID DEL CORTE ENVIAMOS LA LETRA d PARA DECIRLE QUE GENERO UNA DEUDA
          echo '<script>ticket_confirmar("d",'.$Id.');</script>';
	      }
    	}
    }else{
    	#SI LAS CANTIDADES SON IGUALES CAMBIAR ESTAUS DEL CORTE A confirmar = 1
    	if (mysqli_query($conn, "UPDATE cortes SET confirmar = 1 WHERE id_corte = $Id")) {
    		#ENVIAR ALERTA DE CAMBIO Y CONFORMACION DEL CORTE
			  echo '<script>M.toast({html:"Corte confirmado!", classes: "rounded"})</script>';
        #MANDAMOS IMPRIMIR EL TICKET DE CONFRNACION CON EL ID DEL CORTE ENVIAMOS LA LETRA c PARA DECIRLE QUE ENTREGO COMPLETO
        echo '<script>ticket_confirmar("c",'.$Id.');</script>';
      }
    }
}else{
	#SI NO ENCUENTRA UN CORTE MANDAR UNA ALERTA
	echo '<script>M.toast({html:"No se encontro ningun corte con ese Id sin confirmar!", classes: "rounded"})</script>';
	?>
  <script>
	  //RECARGAMOS LA PAGINA cortes_pagos.php EN 2000 Milisegundos = 2 SEGUNDOS
    setTimeout("location.href='cortes_pagos.php'", 2000);
  </script>
  <?php
}    
?>
