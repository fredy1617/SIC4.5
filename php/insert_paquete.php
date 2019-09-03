<?php 
include('../php/conexion.php');
date_default_timezone_set('America/Mexico_City');
$Subida = $conn->real_escape_string($_POST['valorSubida']);
$Bajada = $conn->real_escape_string($_POST['valorBajada']);
$Mensualidad = $conn->real_escape_string($_POST['valorMensualidad']);

//Variable vacía (para evitar los E_NOTICE)
$mensaje = "";
//o $consultaBusqueda sea igual a nombre + (espacio) + apellido
$sql = "INSERT INTO paquetes (subida, bajada, mensualidad) VALUES('$Subida', '$Bajada', '$Mensualidad')";
if(mysqli_query($conn, $sql)){
	$mensaje = '<script>M.toast({html:"El paquete se dió de alta satisfcatoriamente.", classes: "rounded"})</script>';
}else{
	$mensaje = '<script>M.toast({html:"Ha ocurrido un error.", classes: "rounded"})</script>';
}
?>
<table class="bordered highlight">
    <thead>
        <tr>
            <th>No. Paquete</th>
            <th>Bajada</th>
            <th>Subida</th>
            <th>Mensualidad</th>
        </tr>
    </thead>
    <tbody>
    <?php
    include('../php/conexion.php');
    $sql_tmp = mysqli_query($conn,"SELECT * FROM paquetes");
    $columnas = mysqli_num_rows($sql_tmp);
    if($columnas == 0){
        ?>
        <h5 class="center">No hay paquetes</h5>
        <?php
    }else{
        while($tmp = mysqli_fetch_array($sql_tmp)){
    ?>
        <tr>
          <td><?php echo $tmp['id_paquete']; ?></td>
          <td><?php echo $tmp['bajada']; ?></td>
          <td><?php echo $tmp['subida']; ?></td>
          <td>$<?php echo $tmp['mensualidad']; ?></td>
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