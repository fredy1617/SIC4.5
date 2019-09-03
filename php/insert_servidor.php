<?php 
include('../php/conexion.php');
date_default_timezone_set('America/Mexico_City');
$IP = $conn->real_escape_string($_POST['valorIP']);
$User = $conn->real_escape_string($_POST['valorUser']);
$Pass = $conn->real_escape_string($_POST['valorPass']);
$Port = $conn->real_escape_string($_POST['valorPort']);
$Nombre = $conn->real_escape_string($_POST['valorNombre']);

//Variable vacía (para evitar los E_NOTICE)
$mensaje = "";

$sql_servidor = "SELECT * FROM servidores WHERE ip='$IP'";
if(mysqli_num_rows(mysqli_query($conn, $sql_servidor))>0){
    $mensaje = '<script>M.toast({html :"Ya se encuentra un servidor con la misma dirección.", classes: "rounded"})</script>';
}else{
//o $consultaBusqueda sea igual a nombre + (espacio) + apellido
$sql = "INSERT INTO servidores (ip, nombre, user, pass, port) VALUES('$IP', '$Nombre', '$User', '$Pass', '$Port')";
if(mysqli_query($conn, $sql)){
	$mensaje = '<script>M.toast({html :"El servidor se registró satisfactoriamente.", classes: "rounded"})</script>';
}else{
	$mensaje = '<script>M.toast({html :"Ha ocurrido un error.", classes: "rounded"})</script>';	
}
}
?>
<h3>Servidores</h3>
<table class="bordered highlight">
    <thead>
        <tr>
            <th>No. Servidor</th>
            <th>Nombre</th>
            <th>IP</th>
            <th>Usuarios</th>
            <th>Contraseña</th>
            <th>Puerto</th>
        </tr>
    </thead>
    <tbody>
    <?php
    $sql_tmp = mysqli_query($conn,"SELECT * FROM servidores");
    $columnas = mysqli_num_rows($sql_tmp);
    if($columnas == 0){
        ?>
        <h5 class="center">No hay servidores</h5>
        <?php
    }else{
        while($tmp = mysqli_fetch_array($sql_tmp)){
    ?>
        <tr>
          <td><?php echo $tmp['id_servidor']; ?></td>
          <td><?php echo $tmp['nombre']; ?></td>
          <td><?php echo $tmp['ip']; ?></td>
          <td><?php echo $tmp['user']; ?></td>
          <td><?php echo $tmp['pass']; ?></td>
          <td><?php echo $tmp['port']; ?></td>
        </tr>
    <?php
        }
    }
    ?>
    </tbody>
</table>
<br><br><br>
<?php
echo $mensaje;
mysqli_close($conn);
?>