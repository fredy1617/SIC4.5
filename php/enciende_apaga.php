<?php
session_start();
include_once('../API/api_mt_include2.php');
include('../php/conexion.php');
date_default_timezone_set('America/Mexico_City');
$Fecha_hoy = date('Y-m-d');

$Orden = $conn->real_escape_string($_POST['valorOrden']);
$Cliente = $conn->real_escape_string($_POST['valorCliente']);
//Buscamos en el firewall de l mikrotik
$id = $_SESSION['user_id'];
$area = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM users WHERE user_id=$id"));

if($area['area']=="Cobrador" || $area['area']=="Taller"){
  echo "<script>M.toast({html: 'Un *Cobrador* o alguien de *Taller* no pueden activar o desctivar Internet.', classes: 'rounded'});</script>";
}else{
$cliente = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM clientes WHERE id_cliente = $Cliente"));
  $id_comunidad = $cliente['lugar'];
  $comunidad = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM comunidades WHERE id_comunidad = $id_comunidad"));
  $id_server = $comunidad['servidor'];
  $info_Servier =  mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM servidores WHERE id_servidor = $id_server"));
	$servidor = $info_Servier['ip'];//Tomar el servidor(ip) de la comunidad que pertenece el cliente
	$Usuario = $info_Servier['user'];//Tomar el servidor(nombre) de la comunidad que pertenece el cliente
	$passs = $info_Servier['pass'];//Tomar el servidor(pass) de la comunidad que pertenece el cliente
	$Puerto = $info_Servier['port'];//Tomar el servidor(port) de la comunidad que pertenece el cliente
  
    //////// configura tus datos
    $ServerList = $servidor ; //ip_de_tu_API
    $Username = $Usuario; //usuario_API
    $Pass = $passs; //contraseña_API
    $Port = $Puerto; //puerto_API
    $Descripcion = 'Reporte generado automáticamente por un error al Encender/Apagar internet';

    /// VARIABLES DE FORMULARIO
    $address= $cliente['ip']; // direccion que borraremos en el address-list
    $list=    "MOROSOS";  // nombre de la lista  que borraremos 

if ($Orden == "Encender") {
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
                echo "<script >M.toast({html: 'El internet fue Encencido/Reactivado.(".$ID.")', classes: 'rounded'})</script>";
            }else{ // si no existe lo creo
                $sql = "INSERT INTO reportes (id_cliente, descripcion, fecha) VALUES ('$Cliente', 'SE USO BOTON DE ENCENDIDO Y NO SE ACTIVO EL INTERNET ERROR DE API.', '$Fecha_hoy')";
                if(mysqli_query($conn, $sql)){
                  echo "<script >M.toast({html: 'El reporte se dió de alta satisfcatoriamente.', classes: 'rounded'})</script>";
                }
  	            echo "<script >M.toast({html: 'OCURRIO UN ERROR', classes: 'rounded'})</script>";
  	        }
        }else{ // si no existe lo creo
            echo "<script >M.toast({html: 'Este cliente ya tiene el internet Encencido/Reactivado', classes: 'rounded'})</script>";
        }
        $API->disconnect();
      }else{
          echo "<script >M.toast({html: 'No se ha podido hacer conexión al mikrotik Cliente: '.$Cliente, classes: 'rounded'})</script>";
          $sql = "INSERT INTO reportes (id_cliente, descripcion, fecha) VALUES ($Cliente, '$Descripcion', '$Fecha_hoy')";
          if(mysqli_query($conn, $sql)){
          	echo "<script >M.toast({html: 'El reporte se dió de alta satisfcatoriamente.', classes: 'rounded'})</script>";
          }else{
          	echo "<script >M.toast({html: 'Ha ocurrido un error al generar el reporte automatico.', classes: 'rounded'})</script>";
          }
        }
    }else{
      echo "<script >M.toast({html: 'Hubo conflicto con la IP o el Firewall del servidor.', classes: 'rounded'})</script>";
      $sql2 = "INSERT INTO reportes (id_cliente, descripcion, fecha) VALUES ($Cliente, '$Descripcion', '$Fecha_hoy')";
        if(mysqli_query($conn, $sql2)){
          echo "<script >M.toast({html: 'El reporte se dió de alta satisfcatoriamente.', classes: 'rounded'})</script>";
        }else{
        	echo "<script >M.toast({html: 'Ha ocurrido un error al generar el reporte automatico.', classes: 'rounded'})</script>"; 
        }
    }
//fin de busqueda en el firewall del mikrotik 

}elseif ($Orden == "Apagar") {
    $Nombre = $area['firstname'];
    $comment = "CORTADO POR MOROSO, cortado por: ".$Nombre. ' Cliente: '.$cliente['id_cliente'];
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
                            echo "<script >M.toast({html: 'Este cliente ya tiene el internet Apagado/Cortado', classes: 'rounded'})</script>";
                        }else{ // si no existe lo creo
                            $API->write("/ip/firewall/address-list/add",false);
                            $API->write('=address='.$address,false);   // IP
                            $API->write('=list='.$list,false);       // lista
                            $API->write('=comment='.$comment,true);  // comentario
                            $READ = $API->read(false);
                            $ARRAY = $API->parse_response($READ);

                    		    echo "<script >M.toast({html: 'El internet fue Apagado/Cortado', classes: 'rounded'})</script>";
                        }
                        $API->disconnect();
                    }else{
                    	echo "<script >M.toast({html: 'No se ha podido hacer conexión al mikrotik Cliente: '.$Cliente, classes: 'rounded'})</script>";
                    	$sql2 = "INSERT INTO reportes (id_cliente, descripcion, fecha) VALUES ($Cliente, 'Hubo un error al conectar al mikrotik', '$Fecha_hoy')";
				        if(mysqli_query($conn, $sql2)){
				          echo  "<script >M.toast({html: 'El reporte se dió de alta satisfcatoriamente.', classes: 'rounded'})</script>";
				        }else{
				            echo "<script >M.toast({html: 'Ha ocurrido un error al generar el reporte automatico.', classes: 'rounded'})</script>";
				              }
                    }
                }else{
      				  echo "<script >M.toast({html: 'Hubo conflicto con la IP o el Firewall del servidor.', classes: 'rounded'})</script>";

				      $sql2 = "INSERT INTO reportes (id_cliente, descripcion, fecha) VALUES ($Cliente, '$Descripcion', '$Fecha_hoy')";
				        if(mysqli_query($conn, $sql2)){
				          echo  "<script >M.toast({html: 'El reporte se dió de alta satisfcatoriamente.', classes: 'rounded'})</script>";
        }else{
            echo "<script >M.toast({html: 'Ha ocurrido un error al generar el reporte automatico.', classes: 'rounded'})</script>";
        }
    }
//fin de busqueda en el firewall del mikrotik 
}
}
mysqli_close($conn);
?>