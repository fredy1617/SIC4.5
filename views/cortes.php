<!DOCTYPE html>
<html lang="en">
<head>
<?php
  include('fredyNav.php');
  include('../php/conexion.php');
  include('../php/admin.php');
  //require_once('../API/api_mt_include2.php');
?>
<title>SIC | Cortando...</title>
</head>
<main>
<body>
	<div class="container">
    	<h3>Cortando...</h3>
        <?php
        date_default_timezone_set('America/Mexico_City');
            $hoy = date('Y-m-d');
            $clientes = mysqli_query($conn, "SELECT * FROM clientes WHERE fecha_corte < '$hoy' AND instalacion IS NOT NULL");
            while($cortes = mysqli_fetch_array($clientes)){
                $id_comunidad = $cortes['lugar'];
                $comunidad = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM comunidades WHERE id_comunidad='$id_comunidad'"));
                $id_servidor = $comunidad['servidor'];
                $servidor = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM servidores WHERE id_servidor='$id_servidor'"));
                print_r($servidor);              
            }
        ?>
        <br><br>
    </div>
    <br><br><br>
</body>
</main>
</html>