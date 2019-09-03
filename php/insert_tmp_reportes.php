<?php 
include('../php/conexion.php');
$id_Reporte = $conn->real_escape_string($_POST['valorIdReporte']);
$mensaje = '';

$sql_buscar = mysqli_query($conn, "SELECT * FROM tmp_reportes WHERE id_reporte = '$id_Reporte'");

if(mysqli_num_rows($sql_buscar)>0){
	echo '<script>M.toast({html:"Ya se encuentra este reporte en ruta.", classes: "rounded"})</script>';
}else{
	if(mysqli_query($conn, "INSERT INTO tmp_reportes (id_reporte) VALUES ('$id_Reporte')")){
		echo '<script>M.toast({html:"Reporte agregado correctamente a la ruta.", classes: "rounded"})</script>';
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
    $sql_tmp = mysqli_query($conn,"SELECT * FROM tmp_reportes WHERE ruta =0");
    $columnas = mysqli_num_rows($sql_tmp);
    if($columnas == 0){
        ?>
        <h5 class="center">No hay reportes en ruta</h5>
        <?php
    }else{
        while($tmp = mysqli_fetch_array($sql_tmp)){
            $id_reporte = $tmp['id_reporte'];
            $sql_reporte = mysqli_fetch_array(mysqli_query($conn,"SELECT * FROM reportes WHERE id_reporte = '$id_reporte'"));

            $id_cliente = $sql_reporte['id_cliente'];
            $sql_nombre = mysqli_fetch_array(mysqli_query($conn,"SELECT * FROM clientes WHERE id_cliente = '$id_cliente'"));
                ?>
        <tr>
            <td><?php echo $sql_reporte['id_reporte']; ?></td>
            <td><?php echo $sql_nombre['nombre']; ?></td>
            <td><?php echo $sql_reporte['descripcion']; ?></td>
            <td><?php echo $sql_reporte['fecha']; ?></td>
            <td><a onclick="borrar_rep(<?php echo $sql_reporte['id_reporte']; ?>);" class="btn btn-floating red darken-1 waves-effect waves-light"><i class="material-icons">delete</i></a></td>
        </tr>
<?php
            }
        }
mysqli_close($conn);
?>
    </tbody>
</table>
