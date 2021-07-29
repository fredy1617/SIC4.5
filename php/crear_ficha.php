<?php
#INCLUIMOS EL ARCHIVO QUE CONTIENE LO NESESARIO PARA PODER HACER CONEXION Y MODIFICACIONES O PETICIONES CON MIKROTIK
include_once('../API/api_mt_include2.php');
#MANDAMOS LLAMAR LA SESSION QUE ES DONDE TENEMOS LA INFORMACION DEL USUARIO LOGEADO
session_start();
#INCLUIMOS EL ARCHIVO DONDE TENEMOS LA CONEXION A LA BASE DE DATOS
include('../php/conexion.php');
#CREAMOS UNA FUNCION LA CUAL CREARA  NUMERO Y LETRAS MAYUSCULAS ALEATORIAS SEGUN LA LONGITUD QUE SE LE INDIQUE
function generarRandomStringNum($length) { 
  return substr(str_shuffle("123456789ABCDEFGHIJKLMNPQRSTUVWXYZ"), 0, $length); 
}
#CREAMOS UNA FUNCION LA CUAL CREA NUMEROS ALEATORIOS SEGUN A LA LONGITUD QUE SE LE INDIQUE
function generarRandomNum($length) { 
  return substr(str_shuffle("0123456789"), 0, $length); 
}
$Usuario = "SIC".generarRandomStringNum(4);// GENERAMOS UN USUARIO DE LA FICHA EL CUAL INICIARA CON LAS INICIALES "SIC" SEGUIDO DE 4 CARACTERES YA SEAN NUMEROS O LESTRAS MAYUSCULAS (SIC7GR6) 
$Pass = generarRandomNum(6);//GENERAMOS LA CONTRASEÑA DE LA FICHA LA CUAL TENDRA UNA LONGITUD DE 6 DIJITROS NUMERICOS CREADOS ALEATORIAMENTE
$perfil=$conn->real_escape_string($_POST['valorNombre']);// SE RECIVE EL PERFIL SELECCIONADO EN EL FORMULARIO fichas.php QUE ES EL QUE DEFINE EL TIEMPO DE DURACION DE LA FICHA

date_default_timezone_set('America/Mexico_City');
$Hora = date('H:i:s');
$Fecha_hoy = date('Y-m-d');//CREAMOS LA FECHA DEL DIA EN TURNO (HOY)
$id_user = $_SESSION['user_id'];//ASIGNAMOS A UNA BARIABLE EL ID DEL USUARIO LOGUEADO

#VERIFICACMOS QUE NO HALLA UN FICHA CON LA MISMA INFORMACION
$fichas = mysqli_query($conn, "SELECT * FROM fichas WHERE usuario_ficha = '$Usuario' AND password = '$Pass'");
if (mysqli_num_rows($fichas)>0) {
  #SI LA INFORMACION SE REPITE MOSTRAR ALERTA
  echo '<script>M.toast({html:"Esta ficha esta repetida.", classes: "rounded"})</script>';
}else{
  #---------------------------------------------------------------------------------------------------
  #  CREAR UN SERVIDOR (HOSTPOT, 172.17.1.249, dude, dude, 2479)
  #---------------------------------------------------------------------------------------------------
  #SI NO SE REPITE LA INFORMACION SE HACE LA INSERCION DE LA FICHA EN LA BD
  $sql_Servidor = mysqli_query($conn, "SELECT * FROM servidores WHERE nombre = 'HOSTPOT'");
  #VERIFICA SI ENCUENTRA AL SERVIDOR "HOSTPOT"
  if (mysqli_num_rows($sql_Servidor)<=0) {
    echo '<script>M.toast({html:"Error no encontro el servidor con nombre *HOSTPOT*...", classes: "rounded"})</script>';
  }else{
    $sql = "INSERT INTO fichas(usuario_ficha, password, usuario, perfil, fecha) VALUES ('$Usuario', '$Pass', '$id_user', '$perfil', '$Fecha_hoy')";
    #VERIFICAMOS SI SE EJECUTA EL QUERY Y SE HACE LA INSERCION 
    if (mysqli_query($conn, $sql)) {
      //DAR DE ALTA EN MIKROTIK YA SE DIO DE ALTA EN EL SISTEMA
      //////// OBTENER LOS DATOS DEL SERVIDOR /////
      $Servidor = mysqli_fetch_array($sql_Servidor);
      $ServerList = $Servidor['ip'] ; //ip_de_tu_API 172.17.1.249
      $Username = $Servidor['user'] ; //usuario_API dude
      $Password = $Servidor['pass'] ; //contraseña_API dude
      $Port = $Servidor['port'] ; //puerto_API 2479

      /// SE HACE LA CONEXION A MIKROTIK
      $API = new routeros_api();
      $API->debug = false;

      if ($API->connect($ServerList, $Username, $Password, $Port)) {
        /* creacion de usuario */
        $API->comm("/tool/user-manager/user/add", array(
                  "customer"  => "admin",
                  "username"  => $Usuario,
                  "password"  => $Pass,
                  ));  
        /* asignacion de perfil */
        $API->comm("/tool/user-manager/user/create-and-activate-profile", array(
                  "customer"  => "admin",
                  "numbers"   => $Usuario,
                  "profile"   => $perfil,
                  ));
        $API->disconnect();
        #MOSTRAMOS AERTA DE FICHA CREADA YA QUE SE CREO EN LA BD Y EN MIKROTIK
        echo '<script>M.toast({html:"Se creo correctamente la ficha.", classes: "rounded"})</script>';
        #UNA VEZ CREADA LA FICHA EN LA BD Y EN MIKROTIK SE PORCEDE A CREAR EL PAGO CON EL PRECIO DE LA FICHA (PERFIL)
        #SELECCIONAMOS LA INFORMACION DE LA FICHA RECIEN CREADA
        $ficha = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM fichas WHERE usuario_ficha = '$Usuario' AND password = '$Pass'"));
        #CON EL NOMBRE DEL PERFIL SACAMOS TODA LA INFORMACION
        $Perfil = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM perfiles WHERE nombre = '$perfil'"));
        $descripcion ='No. '.$ficha['id_ficha'].' ; '.$Perfil['descripcion'];//CREAMOS LA DESCRIPCION CON EL NUMERO DE LA FICHA Y LA DESCRIPCION DEL PERFIL
        $Precio = $Perfil['costo'];// SACAMOS EL COSTO DE LA FICHA SEGUN SU PERFIL
        #VERIFICAMOS QUE SE REALICE LA INSERCION DEL PAGO
        if (mysqli_query($conn, "INSERT INTO pagos(id_cliente, descripcion, cantidad, fecha, hora, tipo, corte, corteP, id_user, tipo_cambio) VALUES (68, '$descripcion', '$Precio', '$Fecha_hoy', '$Hora', 'Ficha', 0, 0, '$id_user', 'Efectivo')")) {
          echo "<script >M.toast({html: 'Pago de la ficha registrado', classes: 'rounded'})</script>";
          #MOSTRAMOS LA FICHA GENERADA
          ?>
          <div class="row">
            <div class="col s1 m3 l3"></div>
            <div class="col s10 m6 l6">
              <div class="card blue-grey darken-1">
                <div class="card-content white-text">
                  <span class="card-title">Ficha No. <?php echo $ficha['id_ficha']; ?></span><br>
                  <p>
                    <b>USUARIO:</b> <?php echo $Usuario; ?><br>
                    <b>CONTRASEÑA:</b> <?php echo $Pass; ?><br>
                    <b>DESCRIPCION:</b> <?php echo $Perfil['descripcion']; ?><br><br>
                    <a class="waves-effect waves-ligth btn pink right" onclick="imprime(<?php echo $ficha['id_ficha']; ?>)">Imprimir<i class="material-icons right">print</i></a><br>
                  </p>
                </div>
              </div>
            </div>
          </div>
          <?php
        }
      }else{
        echo "<script >M.toast({html: 'No se ha podido hacer conexión al mikrotik', classes: 'rounded'})</script>";
      }
    }
  }
}
