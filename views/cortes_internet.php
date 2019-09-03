<!DOCTYPE html>
<html lang="en">
<head>
<?php
  include('fredyNav.php');
  include('../php/conexion.php');
  include('../php/admin.php');
  include_once('../API/api_mt_include2.php');
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
                /*
                /// AUTOR: Tech-Nico.com ///
                /// admin@tech-nico.com /////
                /// API: Firewall Address-list: agrego una direccion IP a un address-list
                /// Fecha: 26/08/2015 
                */
                //////// configura tus datos
                $ServerList = $servidor['ip']; //ip_de_tu_API
                $Username = $servidor['user']; //usuario_API
                $Pass = $servidor['pass']; //contraseña_API
                $Port = $servidor['port']; //puerto_API

                /// VARIABLES DE FORMULARIO
                $address= $cortes['ip'];  // direccion que cargaremos en el address-list
                $list=    "MOROSOS";  // nombre de la lista donde cargaremos la direccion 
                if( $address !="" && $list!=""  ){
                    $API = new routeros_api();
                    $API->debug = false;
                    if ($API->connect($ServerList, $Username, $Pass, $Port)) {
                       $API->write("/ip/firewall/address-list/getall",false);
                       $API->write('?address='.$address,false);
                       $API->write('?list='.$list,true);       
                       $READ = $API->read(false);
                       $ARRAY = $API->parse_response($READ); // busco si ya existe
                        if(count($ARRAY)>0){ 
                            echo "Error: Cliente " . $cortes['nombre'] ." con la direccion: ".$cortes['ip']." ya se encuentra en la lista.<br>";
                        }else{ // si no existe lo creo
                            $API->write("/ip/firewall/address-list/add",false);
                            $API->write('=address='.$address,false);   // IP
                            $API->write('=list='.$list,false);       // lista
                            $READ = $API->read(false);
                            $ARRAY = $API->parse_response($READ);
                            echo "Se agrego el cliente ".$cortes['nombre']." con la direccion " . $address ." a la lista: ".$list."<br>";
                        }
                        $API->disconnect();
                    }
                }
            }
        ?>
        <br><br>
    </div>
    <br><br><br>
</body>
</main>
</html>