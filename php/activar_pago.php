<?php
include ('conexion.php');
include_once('../API/api_mt_include2.php');
date_default_timezone_set('America/Mexico_City');
$IdCliente = $_GET['id'];
$Fecha_hoy = date('Y-m-d');

//Buscamos en el firewall de l mikrotik
    $cliente = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM clientes WHERE id_cliente = $IdCliente"));
    $id_comunidad = $cliente['lugar'];
    $comunidad = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM comunidades WHERE id_comunidad='$id_comunidad'"));
    $id_servidor = $comunidad['servidor'];
    $servidor = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM servidores WHERE id_servidor='$id_servidor'"));

    //////// configura tus datos
    $ServerList = $servidor['ip'] ; //ip_de_tu_API
    $Username = $servidor['user']; //usuario_API
    $Pass = $servidor['pass']; //contraseña_API
    $Port = $servidor['port']; //puerto_API
    $Descripcion = 'Reporte generado automáticamente por un error al generar pago (IP)';

    /// VARIABLES DE FORMULARIO
    $address= $cliente['ip']; // direccion que borraremos en el address-list
    $list=    "MOROSOS";  // nombre de la lista  que borraremos 
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
              #REMOVER DE LA LISTA
                $ID = $ARRAY[0]['.id'];
                $API->write('/ip/firewall/address-list/remove', false);
                $API->write('=.id='.$ID, true);
                $READ = $API->read(false);
                #VERIFICAR NUEVAMENTE SI YA NO ESTA EN LA LISTA
                $API->write("/ip/firewall/address-list/getall",false);
                $API->write('?address='.$address,false);
                $API->write('?list='.$list,true);       
                $READ = $API->read(false);
                $ARRAY = $API->parse_response($READ); // busco si ya existe
                if(count($ARRAY) == 0){
                    echo "<html><h3>SERVICIO REACTIVADO!!</h3></html>";
                }else{ // si no existe lo creo
                    $sql = "INSERT INTO reportes (id_cliente, descripcion, fecha) VALUES ('$IdCliente', 'PAGO MENSUALIDAD Y NO SE LE ACTIVO AUTOMATICAMENTE EL SERVICIO ERROR DE API.', '$Fecha_hoy')";
                    if(mysqli_query($conn, $sql)){
                      echo "<br> El reporte se dió de alta satisfcatoriamente.<br>";
                    }
                    echo "<html><font color = 'red'><h2>OCURRIO UN ERROR 410...(NO SE ACTIVO)</h2></font></html>";
                }     
            }else{ // si no existe lo creo
                echo "<html><h3>Cliente verificado y no fue cortado aún...</h3></html>";
            }
            $API->disconnect();
        }else{
          echo "<html><font color = 'red'><h2>OCURRIO UN ERROR 404... CONSULTAR CON PROGRAMACION ! CLIENTE: ".$IdCliente."</h2> </font></html>";
          $sql = "INSERT INTO reportes (id_cliente, descripcion, fecha) VALUES ('$IdCliente', 'Hubo un error con la conexion al mikrotik.', '$Fecha_hoy')";
          if(mysqli_query($conn, $sql)){
            echo "<br> El reporte se dió de alta satisfcatoriamente.<br>";
          }else{
           mysqli_query($conn, "INSERT INTO reportes (id_cliente, descripcion, fecha) VALUES '$IdCliente', 'Error a generar reporte auntomatico conexion mikrotik', '$Fecha_hoy'"); 
          }
        }
    }else{
      echo "<html><font color = 'red'><h3>OCURRIO UN ERROR 204... CONSULTAR CON PROGRAMACION ! CLIENTE: ".$IdCliente."</h3> </font></html>";
      $sql2 = "INSERT INTO reportes (id_cliente, descripcion, fecha) VALUES ('$IdCliente', '$Descripcion', '$Fecha_hoy')";
        if(mysqli_query($conn, $sql2)){
          echo "El reporte se dió de alta satisfcatoriamente.<br>";
        }else{
          mysqli_query($conn, "INSERT INTO reportes (id_cliente, descripcion, fecha) VALUES '$IdCliente', 'Error a generar reporte auntomatico error de IP', '$Fecha_hoy'"); 
        }
    }
//fin de busqueda en el firewall del mikrotik  

mysqli_close($conn);
?>
