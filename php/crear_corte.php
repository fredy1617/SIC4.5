<?php
#INCLUIMOS EL ARCHIVO CON LA CONEXION A LA BASE DE DATOS
include ('../php/conexion.php');
#DEFINIMOS UNA ZONA HORARIA
date_default_timezone_set('America/Mexico_City');
#INCLUIMOS EL PHP DONDE VIENE LA INFORMACION DEL INICIO DE SESSION
include('is_logged.php');
$id_user = $_SESSION['user_id'];//ID DEL USUARIO LOGEADO EN LA SESSION DEL SISTEMA
#GENERAMOS UNA FECHA DEL DIA EN CURSO REFERENTE A LA ZONA HORARIA
$Fecha_hoy = date('Y-m-d');
#GENERAMOS UNA HORA EN CURSO REFERENTE A LA ZONA HORARIA
$Hora = date('H:i:s');

#RECIBIMOS EL LA VARIABLE valorClave CON EL METODO POST DEL DOCUMENTO corte_pagos.php DEL MODAL PARA CREAR EL CORTE
$Clave = $conn->real_escape_string($_POST['valorClave']);
#SEPARAMOS LA VARIABLE $Clave EN DOS PARTES ($ic) ES LA SEPARACION 
$Partes = explode('$ic', $Clave);
$id = $Partes[1];//TOMAMOS LA SEGUNDA PARTE DEL LA CLAVE QUE ES EL ID DEL USUARIO QUE REALIZA EL CORTE
#SELECCIONAMOS DE LA TABLA sistempass LA CONTRASEÑA SEGUN EL ID QUE ESTAMOS RECIBIENDO AL SEPARAR $Clave
$Pass = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM sistempass WHERE id_user=$id"));
#VERIFICAMOS QUE LA CONTRASEÑA QUE SELECIIONAMOS DEL USUARIO  DE LA TABLA sistempass SEA IGUAL A LA CONTRASEÑA RECIBIDA EN $Clave
if ($Pass['pass'] == $Clave){
    #SELECCIONAMOS LOS TOTALES DE CADA TIPO DE PAGO SEGUN LOS PAGOS QUE HIZO EL COBRADOR
    $total = mysqli_fetch_array( mysqli_query($conn, "SELECT SUM(cantidad) AS precio FROM pagos WHERE id_user=$id_user AND corte = 0 AND tipo_cambio='Efectivo'"));
    $totalbanco = mysqli_fetch_array(mysqli_query($conn, "SELECT SUM(cantidad) AS precio FROM pagos WHERE id_user=$id_user AND corte = 0 AND tipo_cambio='Banco'"));
    $totalcredito = mysqli_fetch_array(mysqli_query($conn, "SELECT SUM(cantidad) AS precio FROM pagos WHERE id_user=$id_user AND corte = 0 AND tipo_cambio='Credito'"));
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
        #SELECCIONAMOS EL USARIO CON EL ID QUE ESTAMOS RECIBIENDO CON LA $Clave
        $user = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM users WHERE user_id = '$id'"));
        $Realizo = $user['firstname'];//NOMBRE DEL USUARIO QUE ESTA REALIZANDO EL CORTE
        #SELECCIONAMOS UN CORTE QUE YA TENGA LOS MISMOS VALORES
        $sql_check = mysqli_query($conn, "SELECT id_corte FROM cortes WHERE usuario = '$id_user' AND fecha = '$Fecha_hoy' AND cantidad = '$cantidad' AND banco = '$banco' AND credito =  '$credito' AND realizo = '$Realizo'");
        $corte = 0;//DEFINIMOS EL CORTE EN 0 PARA NO TENER ERROR
        #VERIFICAMOS SI EXISTE YA UN CORTE CON ESTOS MISMO VALORES YA CREADO
        if (mysqli_num_rows($sql_check)>0) {
            #SI YA EXISTE UN CORTE TOMAMOS EL ID DE ESTE
            $ultimo = mysqli_fetch_array($sql_check);
            $corte = $ultimo['id_corte'];//TOMAMOS EL ID DEL CORTE
        }else{
            #SI NO EXISTE CREAMOS EL CORTE.....
            if (mysqli_query($conn,"INSERT INTO cortes (usuario, fecha, hora, cantidad, banco, credito, realizo, msj, confirmar) VALUES ($id_user, '$Fecha_hoy', '$Hora', '$cantidad', '$banco', '$credito', '$Realizo', 0, 0)")) {
                #SELECCIONAMOS EL ULTIMO CORTE CREADO
                $ultimo =  mysqli_fetch_array(mysqli_query($conn, "SELECT MAX(id_corte) AS id FROM cortes WHERE usuario=$id_user"));           
                $corte = $ultimo['id'];//TOMAMOS EL ID DEL ULTIMO CORTE
            }
        }
        #VERIFICAMOS QUE EL ID DEL NO ESTE VACIO.
        if ($corte != 0) {            
            #RECIBIMOS EL LA VARIABLE valorCantidad CON EL METODO POST DEL DOCUMENTO corte_pagos.php DEL MODAL PARA CREAR EL CORTE (VIATICOS)
            $CantidadD = $conn->real_escape_string($_POST['valorCantidad']);
            #RECIBIMOS EL LA VARIABLE valorDescripcion CON EL METODO POST DEL DOCUMENTO corte_pagos.php DEL MODAL PARA CREAR EL CORTE (VIATICOS)
            $DescripcionD = $conn->real_escape_string($_POST['valorDescripcion']);
            #VERIFICAMOS SI LAS DOS VARIABLES RECIBIDAS TIENEN VALORES Y NO ESTAN VACIAS
            if ($CantidadD > 0 AND $DescripcionD != "") {
                #SI LAS VARIABLES CONTIENEN ALGO CREAR UN DEDUCIBLE CON LOS VALORES RECIBIDOS (VIATICOS)
                mysqli_query($conn,"INSERT INTO deducibles (id_corte, cantidad, descripcion, fecha, usuario) VALUES ('$corte', '$CantidadD', '$DescripcionD', '$Fecha_hoy', '$id_user')");
            }
            ?>
            <script>
                id_corte = <?php echo $corte; ?>;//RECIBIMOS EL ID DEL ULTIMO CORTE
                //EN OTRA VENTANA ABRIMOS EL TICKET DEL CORTE /php/corte_pago.php Y LE ENVIAMOS EL ID DEL ULTIMO CORTE
                var a = document.createElement("a");
                    a.target = "_blank";
                    a.href = "../php/corte_pago.php?id="+id_corte;
                    a.click();
                //RECARGAMOS LA PAGINA cortes_pagos.php EN 1500 Milisegundos = 1.5 SEGUNDOS
                setTimeout("location.href='cortes_pagos.php'", 1500);
            </script>
            <?php                  
        }
    }
}else{
    #SI LA CLAVE NO ES IGUAL A LA CONTRASEÑA SELECCIONADA DEL USUARIO MANDAR ALERTA
    echo '<script>M.toast({html:"Clave incorrecta intente nuevamnete...", classes: "rounded"})</script>';
}
?>
