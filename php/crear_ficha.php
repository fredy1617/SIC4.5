<?php

include_once('../API/api_mt_include2.php');
	function generarRandomString($length) { 
    return substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length); 
  }

  $Usuario = "SIC".generarRandomString(3);
  $Pass = generarRandomString(5);

  session_start();
  include('../php/conexion.php');
  date_default_timezone_set('America/Mexico_City');
  $Fecha_hoy = date('Y-m-d');
  $id_user = $_SESSION['user_id'];

  $fichas = mysqli_query($conn, "SELECT * FROM fichas WHERE usuario_ficha = '$Usuario' AND password = '$Pass'");
  $fila = mysqli_num_rows($fichas);
  if ($fila>0) {
    echo '<script>M.toast({html:"Esta ficha esta repetida.", classes: "rounded"})</script>';
  }else{
    $sql = "INSERT INTO fichas(usuario_ficha, password, usuario, fecha) VALUES ('$Usuario', '$Pass', '$id_user', '$Fecha_hoy')";
    if (mysqli_query($conn, $sql)) {

      //DAR DE ALTA EN MIKROTIK YA SE DIO DE ALTA EN EL SISTEMA
       //////// configura tus datos
      $ServerList = '192.168.0.130' ; //ip_de_tu_API
      $Username = 'admin'; //usuario_API
      $Password = ''; //contraseña_API
      $Port = '2406'; //puerto_API

      /// VARIABLES DE FORMULARIO
      $profile=    "FICHA1HORA";  // nombre de la lista  que borraremos 
        $API = new routeros_api();
        $API->debug = false;
        if ($API->connect($ServerList, $Username, $Password, $Port)) {
          $API->write("/ip/hotspot/user/add",false);
                $API->write('=password='.$Pass,false);   // IP
                $API->write('=name='.$Usuario,false); 
                $API->write('=profile='.$profile,false);      
                $API->write('=limit-uptime= 1h',true);        // comentario
                $READ = $API->read(false);
                $ARRAY = $API->parse_response($READ);  
          $API->disconnect();

        }else{
          echo "<script >M.toast({html: 'No se ha podido hacer conexión al mikrotik', classes: 'rounded'})</script>";
        }


      echo '<script>M.toast({html:"Se creo correctamente la ficha.", classes: "rounded"})</script>';
      
      $ficha = mysqli_fetch_array(mysqli_query($conn, "SELECT MAX(id_ficha) AS id FROM fichas"));
      $id_ficha = $ficha['id'];
      $descripcion ='FICHA HOSTPOT No. '.$id_ficha;
      if (mysqli_query($conn, "INSERT INTO pagos(id_cliente, descripcion, cantidad, fecha, tipo, corte, id_user, tipo_cambio) VALUES (68, '$descripcion', 10, '$Fecha_hoy', 'Ficha', 0, '$id_user', 'Efectivo')")) {
      }
    }
  }
?>
 <div class="row">
 	<div class="col s1 m3 l3"></div>
    <div class="col s10 m6 l6">
      <div class="card blue-grey darken-1">
        <div class="card-content white-text">
          <span class="card-title">Ficha:</span><br>
          <p>
          	<b>USUARIO:</b> <?php echo $Usuario; ?><br>
          	<b>CONTRASEÑA:</b> <?php echo $Pass; ?><br><br>
            <a class="waves-effect waves-ligth btn pink right" onclick="imprime(<?php echo $id_ficha; ?>)">Imprimir<i class="material-icons right">print</i></a>
            <br>
          </p>
        </div>
      </div>
    </div>
  </div>