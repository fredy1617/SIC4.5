<?php
#INCLUIMOS EL ARCHIVO CON LA CONEXION A LA BASE DE DATOS
include ('../php/conexion.php');
#DEFINIMOS UNA ZONA HORARIA
date_default_timezone_set('America/Mexico_City');
#GENERAMOS UNA FECHA DEL DIA EN CURSO REFERENTE A LA ZONA HORARIA
$Fecha_hoy = date('Y-m-d');
#GENERAMOS UNA HORA EN CURSO REFERENTE A LA ZONA HORARIA
$Hora = date('H:i:s');
session_start();
$id_user = $_SESSION['user_id'];//ASIGNAMOS A UNA BARIABLE EL ID DEL USUARIO LOGUEADO

#RECIBIMOS EL LAS VARIABLES valorClave, valorCobrador CON EL METODO POST DEL DOCUMENTO corte_pagos.php DEL MODAL PARA CREAR EL CORTE
$Clave = $conn->real_escape_string($_POST['valorClave']);
$Cobrador = $conn->real_escape_string($_POST['valorCobrador']);

#VERIFICAMOS QUE LA CONTRASEÑA SEA IGUAL A LA CONTRASEÑA RECIBIDA EN $Clave
if ('cp123SIC' == $Clave){
    #SELECCIONAMOS LOS TOTALES DE CADA TIPO DE PAGO SEGUN LOS PAGOS QUE HIZO EL COBRADOR
    $total = mysqli_fetch_array( mysqli_query($conn, "SELECT SUM(cantidad) AS precio FROM pagos WHERE id_user=$id_user AND corteP = 0 AND tipo_cambio='Efectivo'"));
    $totalbanco = mysqli_fetch_array(mysqli_query($conn, "SELECT SUM(cantidad) AS precio FROM pagos WHERE id_user=$id_user AND corteP = 0 AND tipo_cambio='Banco'"));
    $totalcredito = mysqli_fetch_array(mysqli_query($conn, "SELECT SUM(cantidad) AS precio FROM pagos WHERE id_user=$id_user AND corteP = 0 AND tipo_cambio='Credito'"));
    #ASIGNAMOS LOS TOTALES A VARIABLES RESPACTIVAMENTE SI TIPO
    $cantidad=$total['precio'];
    $banco = $totalbanco['precio'];
    $credito = $totalcredito['precio'];

    //VERIFICAMOS SI HAY ALMENOS ALGUNA TOTAL  MAYOR A 0 PARA PODER CREAR EL CORTE
    if ($cantidad != "" OR $banco != "" OR $credito != "") {
        #SI ALGUNA TOTAL SALE EN STRING LE DAMOS EL VALOR 0 PARA EVITAR ERRORES
        if ($banco == "") {
            $banco = 0;
        }
        if ($cantidad == "") {
            $cantidad = 0;
        }
        if ($credito == "") {
            $credito = 0;
        }
        #SELECCIONAMOS UN CORTE QUE YA TENGA LOS MISMOS VALORES
        $sql_check = mysqli_query($conn, "SELECT id FROM cortes_parciales WHERE  fecha = '$Fecha_hoy' AND efectivo = '$cantidad' AND banco = '$banco' AND credito =  '$credito' AND cobrador = '$Cobrador'");
        $corte = 0;//DEFINIMOS EL CORTE EN 0 PARA NO TENER ERROR
        #VERIFICAMOS SI EXISTE YA UN CORTE CON ESTOS MISMO VALORES YA CREADO
        if (mysqli_num_rows($sql_check)>0) {
            #SI YA EXISTE UN CORTE TOMAMOS EL ID DE ESTE
            $ultimo = mysqli_fetch_array($sql_check);
            $corte = $ultimo['id'];//TOMAMOS EL ID DEL CORTE
        }else{
            #SI NO EXISTE CREAMOS EL CORTE.....
            if (mysqli_query($conn,"INSERT INTO cortes_parciales (cobrador, fecha, hora, efectivo, banco, credito) VALUES ('$Cobrador', '$Fecha_hoy', '$Hora', '$cantidad', '$banco', '$credito')")) {
                #SELECCIONAMOS EL ULTIMO CORTE CREADO
                $ultimo =  mysqli_fetch_array(mysqli_query($conn, "SELECT MAX(id) AS id FROM cortes_parciales WHERE cobrador='$Cobrador'"));           
                $corte = $ultimo['id'];//TOMAMOS EL ID DEL ULTIMO CORTE
            }
        }
        #VERIFICAMOS QUE EL ID DEL NO ESTE VACIO.
        if ($corte != 0) {           
            ?>
            <script>
                id_corte = <?php echo $corte; ?>;//RECIBIMOS EL ID DEL ULTIMO CORTE
                //EN OTRA VENTANA ABRIMOS EL TICKET DEL CORTE /php/corte_pago.php Y LE ENVIAMOS EL ID DEL ULTIMO CORTE
                var a = document.createElement("a");
                    a.target = "_blank";
                    a.href = "../php/corte_parcial.php?id="+id_corte;
                    a.click();
                //RECARGAMOS LA PAGINA cortes_pagos.php EN 1500 Milisegundos = 1.5 SEGUNDOS
                setTimeout("location.href='cortes_parciales.php'", 1500);
            </script>
            <?php                  
        }
    }
}else{
    #SI LA CLAVE NO ES IGUAL A LA CONTRASEÑA SELECCIONADA DEL USUARIO MANDAR ALERTA
    echo '<script>M.toast({html:"Clave incorrecta intente nuevamnete...", classes: "rounded"})</script>';
}
?>
