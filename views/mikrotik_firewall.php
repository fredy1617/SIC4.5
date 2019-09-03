<!DOCTYPE html>
<html lang="en">
<head>
<?php
  include('fredyNav.php');
  include('../php/conexion.php');
  include('../php/admin.php');
  include_once('../API/api_mt_include2.php');
?>
</style>
<title>SIC | Cortando...</title>
</head>
<main>
<body>
	<div class="container">
    	<h3>Cortando...</h3>
        <?php
        date_default_timezone_set('America/Mexico_City');
                /*
                /// AUTOR: Tech-Nico.com ///
                /// admin@tech-nico.com /////
                /// API: Firewall Address-list: agrego una direccion IP a un address-list
                /// Fecha: 26/08/2015 
                */
                //////// configura tus datos
                $ServerList = '177.241.241.219'; //ip_de_tu_API
                $Username = 'gabrielvre'; //usuario_API
                $Pass = 'sic1088'; //contraseña_API
                $Port = '8290'; //puerto_API

                /// VARIABLES DE FORMULARIO
                $address= '192.168.10.1';  // direccion que cargaremos en el address-list
                $list = "MOROSOS";  // nombre de la lista donde cargaremos la direccion 
                $comment = "CORTADO POR MOROSO"; // comentario
                if( $address !="" && $list!="" ){
                    $API = new routeros_api();
                    $API->debug = false;
                    if ($API->connect($ServerList, $Username, $Pass, $Port)) {
                       $API->write("/ip/firewall/address-list/getall",false);
                       $API->write('?address='.$address,false);
                       $API->write('?list='.$list,true);       
                       $READ = $API->read(false);
                       $ARRAY = $API->parse_response($READ); // busco si ya existe
                        if(count($ARRAY)>0){ 
                            echo "Error: Ya existe " . $list ." con la direccion: ".$address;
                        }else{ // si no existe lo creo
                            $API->write("/ip/firewall/address-list/add",false);
                            $API->write('=address='.$address,false);   // IP
                            $API->write('=list='.$list,false);       // lista
                            $API->write('=comment='.$comment,true);  // comentario
                            $READ = $API->read(false);
                            $ARRAY = $API->parse_response($READ);
                            echo "Se agrego la direccion " . $address ." a la lista: ".$list;
                        }
                        $API->disconnect();
                    }
                }
        ?>
        <br><br>
    </div>
    <br><br><br>
</body>
</main>
</html>