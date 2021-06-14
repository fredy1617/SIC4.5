	<!--Import material-icons.css-->
    <link href="css/material-icons.css" rel="stylesheet">
    <!--Import materialize.css-->
    <link type="text/css" rel="stylesheet" href="css/materialize.min.css"  media="screen,projection"/>
    <!--Let browser know website is optimized for mobile-->
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <link rel="icon" href="../img/logo.jpg" type="image/x-icon" />
<?php
include('../php/conexion.php');
// Checamos la version de PHP
if (version_compare(PHP_VERSION, '5.3.7', '<')) {
    exit("Lo sentimos, este sistema esta creado para correr en PHP 5.3.7 o superior!");
} else if (version_compare(PHP_VERSION, '5.5.0', '<')) {
    // incrustamos la libreria (API) para las contraseñas
    include_once("../php/password_compatibility_library.php");
}
include_once("../php/Login.php");
// Creamos un objeto de inicio de sesión. Cuando se crea este objeto, se va a hacer todas las cosas de conexión/desconexión automática
// Por lo que ésta sola línea se encarga de todo el proceso de inicio de sesión.
$login = new Login();

// Preguntamos si estamos accediendo
if ($login->isUserLoggedIn() == true){
    //Si se ha iniciado la sesión, entonces nos dirigimos a la página...
   header("location: ../views/home.php");

} else {
    // Y si no se ha iniciado sesión, entonces...
    ?>
<!DOCTYPE html>

<html lang="es">
<head>
  <!-- FONDO DE PANTALLA DE LOGIN -->  
  <img id="video_background"  src="../img/home.jpg"><br><br><br><br>

  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0, user-scalable=no"/>
  <title>SIC | Iniciar Sesión</title>
	<!-- Latest compiled and minified CSS -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
  <!-- CSS  -->
   <link href="../css/login.css" type="text/css" rel="stylesheet" media="screen,projection"/>
</head>
<body>
 <div class="container">
        <div class="card card-container">
            <img id="profile-img" class="profile-img-card" src="../img/logo.jpg"/>
            <p id="profile-name" class="profile-name-card"></p>
            <form method="post" accept-charset="utf-8" action="login.php" name="loginform" autocomplete="off" role="form" class="form-signin">
			<?php
				// Un error potencial
				if (isset($login)) {
					if ($login->errors) {
						?>
						<div class="alert alert-danger alert-dismissible" role="alert">
						    <strong>Error!</strong> 
						
						<?php 
						foreach ($login->errors as $error) {
							echo $error;
						}
						?>
						</div>
						<?php
					}
					if ($login->messages) {
						?>
						<div class="alert alert-success alert-dismissible" role="alert">
						    <strong>Aviso!</strong>
						<?php
						foreach ($login->messages as $message) {
							echo $message;
						}
						?>
						</div> 
						<?php 
					}
				}
				?>
				<br>
                <span id="reauth-email" class="reauth-email"></span>
                <input class="form-control" placeholder="Usuario" name="user_name" type="text" value="" autofocus="" required><br>
                <input class="form-control" placeholder="Contraseña" name="user_password" type="password" value="" autocomplete="off" required><br><br>
                <button type="submit" class="btn waves-effect waves-light pink" name="login" id="submit">Iniciar Sesión</button>
            </form>            
        </div><!-- /card-contenerdor -->
    </div><!-- /contenedor -->
  </body>
</html>
<?php
}
?>


