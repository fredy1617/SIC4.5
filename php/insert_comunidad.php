<?php 
include('../php/conexion.php');
date_default_timezone_set('America/Mexico_City');
$Nombre = $conn->real_escape_string($_POST['valorNombre']);
$Instalacion = $conn->real_escape_string($_POST['valorInstalacion']);
$Servidor = $conn->real_escape_string($_POST['valorServidor']);

//Variable vacía (para evitar los E_NOTICE)
$mensaje = "";

$sql_comunidad = "SELECT * FROM comunidades WHERE nombre='$Nombre'";
if(mysqli_num_rows(mysqli_query($conn, $sql_comunidad))>0){
    $mensaje = '<script>M.toast({html :"Ya se encuentra una comunidad con el mismo nombre.", classes: "rounded"})</script>';
}else{
//o $consultaBusqueda sea igual a nombre + (espacio) + apellido
$sql = "INSERT INTO comunidades (nombre, instalacion, servidor) VALUES('$Nombre', '$Instalacion', '$Servidor')";
if(mysqli_query($conn, $sql)){
	$mensaje = '<script>M.toast({html :"La comunidad se registró satisfactoriamente.", classes: "rounded"})</script>';
}else{
	$mensaje = '<script>M.toast({html :"Ha ocurrido un error.", classes: "rounded"})</script>';	
}
}
?>
<h3>Comunidades</h3>
<table class="bordered highlight">
    <thead>
        <tr>
            <th>No. Comunidad</th>
            <th>Nombre</th>
            <th>Servidor</th>
            <th>Costo de Instalación</th>
            <th>Editar</th>
        </tr>
    </thead>
    <tbody>

    <?php
    $sql_tmp = mysqli_query($conn,"SELECT * FROM comunidades");
    $columnas = mysqli_num_rows($sql_tmp);

    if($columnas == 0){
        ?>
        <h5 class="center">No hay comunidades</h5>
        <?php
    }else{
        while($tmp = mysqli_fetch_array($sql_tmp)){
            $serv_id = $tmp['servidor'];
            $serv = mysqli_fetch_array(mysqli_query($conn, "SELECT nombre FROM servidores WHERE id_servidor = '$serv_id'"));
    ?>
        <tr>
          <td><?php echo $tmp['id_comunidad']; ?></td>
          <td><?php echo $tmp['nombre']; ?></td>
          <td><?php echo $serv[0]; ?></td>
          <td>$<?php echo $tmp['instalacion']; ?>.00</td>
          <td><form method="post" action="../views/editar_comunidad.php"><input name="no_comunidad" type="hidden" value="<?php echo $tmp['id_comunidad']; ?>"><button type="submit" class="btn-floating btn-tiny waves-effect waves-light pink"><i class="material-icons">edit</i></button></form></td>
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