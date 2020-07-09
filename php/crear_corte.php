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
echo $Realizo;

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
        mysqli_query($conn,"INSERT INTO cortes(usuario, fecha, cantidad, banco, credito, realizo) VALUES ($id_user, '$Fecha_hoy', '$cantidad', '$banco', '$credito', '$Realizo')");
        $ultimo =  mysqli_fetch_array(mysqli_query($conn, "SELECT MAX(id_corte) AS id FROM cortes WHERE usuario=$id_user"));           
        $corte = $ultimo['id'];
    }
    ?>
    <script type="text/javascript">
      	setTimeout("location.href='cortes_pagos.php'", 1000);
		id_corte = <?php echo $corte; ?>;
		var a = document.createElement("a");
		    a.target = "_blank";
		    a.href = "../php/corte_pago.php?id="+id_corte;
		    a.click();
	</script>
	<?php
}else{
    echo '<script>M.toast({html:"Clave incorrecta intente nuevamnete...", classes: "rounded"})</script>';
}
?>
