<?php
include('../php/conexion.php');
$valorId = $conn->real_escape_string($_POST["valorId"]);
include('../php/superAdmin.php');

$sql_delete = "DELETE FROM users WHERE user_id=$valorId";

if(mysqli_query($conn, $sql_delete)){
    echo '<script>M.toast({html:"Usuario eliminado.", classes: "rounded"})</script>';
?>
    <h3>Usuarios</h3>
    <table class="bordered highlight">
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
    </table>
    <br><br><br><br>
    <?php
}else{
    echo '<script>M.toast({html:"Hubo un error, intentelo mas tarde.", classes: "rounded"})</script>';
}
?>