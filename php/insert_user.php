<?php
//include('is_logged.php');//Archivo verifica que el usario que intenta acceder a la URL esta logueado
// Checamos la versión de PHP que esta usando el servidor
if (version_compare(PHP_VERSION, '5.3.7', '<')) {
    exit("Sorry, Simple PHP Login does not run on a PHP version smaller than 5.3.7 !");
} else if (version_compare(PHP_VERSION, '5.5.0', '<')) {
    // Si estamos usando una versión de PHP superior entonces usamos la API para encriptar la contrasela con el archivo: password_api_compatibility_library.php
    include_once("password_compatibility_library.php");
}
include("../php/conexion.php");//Contiene las variables de configuración para conectar a la base de datos
			    $caracteres_malos = array("<", ">", "\"", "'", "/", "<", ">", "'", "/",";","?", "php", "echo","$","{","}","=");
                $caracteres_buenos = array("", "", "", "", "", "", "", "", "","","", "","","", "","","");
			
				// Eliminamos cualquier tipo de código HTML o JavaScript
                $valorFirstName = $conn->real_escape_string($_POST["valorNombre"]);
				$valorLastName = $conn->real_escape_string($_POST["valorApellidos"]);
				$valorUserName = $conn->real_escape_string($_POST["valorUsuario"]);
                $valorUserEmail = $conn->real_escape_string($_POST["valorEmail"]);
				$valorUserPassword = $conn->real_escape_string($_POST['valorContra']);
                $valorUserRol = $conn->real_escape_string($_POST['valorRol']);
                //ELIMINAR CODIGO PHP
                $valorFirstName = str_replace($caracteres_malos, $caracteres_buenos, $valorFirstName);
                $valorLastName = str_replace($caracteres_malos, $caracteres_buenos, $valorLastName);
                $valorUserName = str_replace($caracteres_malos, $caracteres_buenos, $valorUserName);
                $valorUserEmail = str_replace($caracteres_malos, $caracteres_buenos, $valorUserEmail);           
                $valorUserPassword = str_replace($caracteres_malos, $caracteres_buenos, $valorUserPassword);
                $valorUserRol = str_replace($caracteres_malos, $caracteres_buenos, $valorUserRol);

				$date_added=date("Y-m-d H:i:s");
                // Se encripta el la contraseña del usuario con la función password_hash(), y retorna una cadena de 60 caracteres
				$valorUserPassword_hash = password_hash($valorUserPassword, PASSWORD_DEFAULT);
					
                // Comprobamos si el usuario o el correo ya existe
                $sql = "SELECT * FROM users WHERE user_name = '" . $valorUserName . "' OR user_email = '" . $valorUserEmail . "';";
                $query_check_user_name = mysqli_query($conn,$sql);
				$query_check_user=mysqli_num_rows($query_check_user_name);
                if ($query_check_user == 1) {
                    echo '<script>M.toast({html:"Este usuario o correo ya existe en la base de datos.", classes: "rounded"})</script>';
                } else {
					// Escribimos el nuevo usuario en la base de datos
                    $sql = "INSERT INTO users (firstname, lastname, user_name, user_password_hash, user_email, date_added, area)
                            VALUES('".$valorFirstName."','".$valorLastName."','" . $valorUserName . "', '" . $valorUserPassword_hash . "', '" . $valorUserEmail . "','".$date_added."','".$valorUserRol."');";
                    $query_new_user_insert = mysqli_query($conn,$sql);

                    // Si el usuario fue añadido con éxito
                    if ($query_new_user_insert) {
                        $url ="../views/usuarios.php";
                        $time_out = 3;
                        echo '<script>M.toast({html:"Usuario añadido correctamente.", classes: "rounded"})</script>';
                        header("refresh: $time_out; url=$url");
                    } else {
                        $url ="../views/usuarios.php";
                        $time_out = 3;
                        echo '<script>M.toast({html:"Hubo un error, intentelo mas tarde.", classes: "rounded"})</script>';
                        header("refresh: $time_out; url=$url");
                    }
                }
?>
    <h3>Usuarios</h3>
    <table class="bordered highlight responsive-table">
        <thead>
            <tr>
                <th>Nombre(s)</th>
                <th>Apellidos</th>
                <th>Usuario</th>
                <th>E-mail</th>
                <th>Rol</th>
                <th>Eliminar</th>
            </tr>
        </thead>
        <tbody>
        <?php
        include('../php/conexion.php');
        $sql_tmp = mysqli_query($conn,"SELECT * FROM users");
        $columnas = mysqli_num_rows($sql_tmp);
        if($columnas == 0){
            ?>
            <h5 class="center">No hay comunidades</h5>
            <?php
        }else{
            while($tmp = mysqli_fetch_array($sql_tmp)){
        ?>
            <tr>
              <td><?php echo $tmp['firstname']; ?></td>
              <td><?php echo $tmp['lastname']; ?></td>
              <td><?php echo $tmp['user_name']; ?></td>
              <td><?php echo $tmp['user_email']; ?></td>
              <td><?php echo $tmp['area'];?></td>
              <td><a onclick="eliminar(<?php echo $tmp['user_id'];?>);" class="btn-floating btn-tiny waves-effect waves-light red darken-3"><i class="material-icons">delete</i></a></td>
            </tr>
        <?php
            }
        }
        mysqli_close($conn);
        ?>
        </tbody>
    </table><br><br><br><br>