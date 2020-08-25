<?php
include ('../php/conexion.php');
date_default_timezone_set('America/Mexico_City');
include('is_logged.php');
$id_user = $_SESSION['user_id'];
$Fecha_hoy = date('Y-m-d');

$Clave = $conn->real_escape_string($_POST['valorClave']);

$Partes = explode('$ic', $Clave);
$id = $Partes[1];
$user = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM users WHERE user_id = '$id'"));
$Realizo = $user['firstname'];

$Pass = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM sistempass WHERE id_user=$id"));
if ($Pass['pass'] == $Clave){
    $total = mysqli_fetch_array( mysqli_query($conn, "SELECT SUM(cantidad) AS precio FROM pagos WHERE id_user=$id_user AND corte = 0 AND tipo_cambio='Efectivo'"));
    $totalbanco = mysqli_fetch_array(mysqli_query($conn, "SELECT SUM(cantidad) AS precio FROM pagos WHERE id_user=$id_user AND corte = 0 AND tipo_cambio='Banco'"));
    $totalcredito = mysqli_fetch_array(mysqli_query($conn, "SELECT SUM(cantidad) AS precio FROM pagos WHERE id_user=$id_user AND corte = 0 AND tipo_cambio='Credito'"));
    
    $cantidad=$total['precio'];
    $banco = $totalbanco['precio'];
    $credito = $totalcredito['precio'];

    $corte = 0;
     //Insertar corte.....
    if ($cantidad != "" OR $banco != "" OR $credito != "") {
        if ($banco == "") {
            $banco = 0;
        }
        if ($cantidad == "") {
            $cantidad = 0;
        }
        if ($credito == "") {
            $credito = 0;
        }

        if (mysqli_query($conn,"INSERT INTO cortes (usuario, fecha, cantidad, banco, credito, realizo, msj) VALUES ($id_user, '$Fecha_hoy', '$cantidad', '$banco', '$credito', '$Realizo', 0)")) {
            $ultimo =  mysqli_fetch_array(mysqli_query($conn, "SELECT MAX(id_corte) AS id FROM cortes WHERE usuario=$id_user"));           
            $corte = $ultimo['id'];
            $CantidadD = $conn->real_escape_string($_POST['valorCantidad']);
            $Descripcion = $conn->real_escape_string($_POST['valorDescripcion']);
            if ($CantidadD > 0 AND $Descripcion != "") {
                mysqli_query($conn,"INSERT INTO deducibles (id_corte, cantidad, descripcion, fecha, usuario) VALUES ('$corte', '$CantidadD', '$Descripcion', '$Fecha_hoy', '$id_user')");
            }
            ?>
            <script>
                setTimeout("location.href='cortes_pagos.php'", 1000);
                id_corte = <?php echo $corte; ?>;
                var a = document.createElement("a");
                    a.target = "_blank";
                    a.href = "../php/corte_pago.php?id="+id_corte;
                    a.click();
            </script>
            <?php
            #SI ALMENOS UNO DE LOS TOTALES ES MAYOR A CERO PROCEDEMOS A ENVIAR EL MENSAJE POR TELEGRAM
            $Ping = shell_exec("ping 8.8.8.8");//SENTENCIA QUE SIRVE PARA HACER PING MEDIANTE LA CONSOLA DE LA PC
            #COMPARAMOS LOS RESULTADOS DEL PING ECHO SI HACE PING SIN PERDIDAS A LA DIRECCION DE GOOGLE PARA COMPROBAR QUE ALLA INTERNET
            if (strpos($Ping, "perdidos = 0") AND strpos($Ping, "Respuesta desde 8.8.8.8: bytes=32")) {
                #SI HACE PING SI HABRA INTERNET Y POR TANTO NO MARCARA ERROR INCLUIMOS EL ARCHIVO QUE ENVIA EL MSJ  
                include ('msj_botTelegramCorte.php');                  
            }
        }
    }
}else{
    echo '<script>M.toast({html:"Clave incorrecta intente nuevamnete...", classes: "rounded"})</script>';
}
?>
