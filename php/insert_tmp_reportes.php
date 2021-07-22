<?php 
include('../php/conexion.php');
include('is_logged.php');
date_default_timezone_set('America/Mexico_City');
$Hora = date('H:i:s');
$id_user = $_SESSION['user_id'];
$id_Reporte = $conn->real_escape_string($_POST['valorIdReporte']);

$sql_buscar = mysqli_query($conn, "SELECT * FROM tmp_reportes WHERE id_reporte = '$id_Reporte'");
$EnCampo = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM reportes WHERE id_reporte = '$id_Reporte'"));
if ($id_Reporte >= 100000) {
    $EnCampo['campo']= 1;
}
if ($EnCampo['campo']==0 ) {
    echo '<script>M.toast({html:"No se puede agregar a ruta porque no selecciono < En campo >.", classes: "rounded"})</script>';
}else{
    if(mysqli_num_rows($sql_buscar)>0){
    	echo '<script>M.toast({html:"Ya se encuentra este reporte en ruta.", classes: "rounded"})</script>';
    }else{
    	if(mysqli_query($conn, "INSERT INTO tmp_reportes (id_reporte, usuario, hora) VALUES ('$id_Reporte', '$id_user', '$Hora')")){
            echo '<script>M.toast({html:"Reporte agregado correctamente a la ruta.", classes: "rounded"})</script>';
        }	
    }
}
?>
<table class="bordered highlight responsive-table">
    <thead>
        <tr>
            <th>Reporte No.</th>
            <th>Cliente</th>
            <th>Descripción</th>
            <th>Fecha</th>
            <th>Borrar</th>
        </tr>
    </thead>
    <tbody>
    <?php 
    $sql_tmp = mysqli_query($conn,"SELECT * FROM tmp_reportes WHERE ruta = 0 AND usuario = $id_user");
    $columnas = mysqli_num_rows($sql_tmp);
    if($columnas == 0){
        ?>
        <h5 class="center">No hay reportes en ruta</h5>
        <?php
    }else{
        while($tmp = mysqli_fetch_array($sql_tmp)){
            $id_reporte = $tmp['id_reporte'];
            if ((mysqli_num_rows(mysqli_query($conn, "SELECT * FROM reportes WHERE id_reporte = $id_reporte"))) == 0){
                $sql = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM orden_servicios WHERE id = $id_reporte")); 
                $id = $sql['id'];
                $Descripcion = ($sql['trabajo'] == '')? $sql['solicitud']: $sql['trabajo'];  
            }else{
                $sql = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM reportes WHERE id_reporte = $id_reporte")); 
                $id = $sql['id_reporte'];
                $Descripcion = $sql['descripcion'];
            }
            $id_cliente = $sql['id_cliente'];
            $ver = mysqli_query($conn, "SELECT * FROM clientes WHERE id_cliente = $id_cliente");
            if (mysqli_num_rows($ver) == 0) {
                $ver = mysqli_query($conn, "SELECT * FROM especiales WHERE id_cliente = $id_cliente");
            }
            $sql_nombre = mysqli_fetch_array($ver);
            $id_comunidad = $sql_nombre['lugar'];
            $comunidad = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM comunidades WHERE id_comunidad = $id_comunidad"));
                ?>
        <tr>
            <td><?php echo $id; ?></td>
            <td><?php echo $sql_nombre['nombre']; ?></td>
            <td><?php echo $comunidad['nombre']; ?></td>
            <td><?php echo $Descripcion; ?></td>
            <td><?php echo $sql['fecha']; ?></td>
            <td><a onclick="borrar_rep(<?php echo $id; ?>);" class="btn btn-floating red darken-1 waves-effect waves-light"><i class="material-icons">delete</i></a></td>
        </tr>
<?php
            }
        }
mysqli_close($conn);
?>
    </tbody>
</table>
