<?php 
include('../php/conexion.php');
date_default_timezone_set('America/Mexico_City');
$id_Reporte = $conn->real_escape_string($_POST['valorIdReporte']);
$id_ruta = $conn->real_escape_string($_POST['valorRuta']);
$mensaje = '';

$sql_buscar = mysqli_query($conn, "SELECT * FROM tmp_reportes WHERE id_reporte = '$id_Reporte'");

if(mysqli_num_rows($sql_buscar)>0){
	echo '<script>M.toast({html:"Ya se encuentra este reporte en ruta.", classes: "rounded"})</script>';
}else{
	if(mysqli_query($conn, "INSERT INTO tmp_reportes (id_reporte, ruta) VALUES ('$id_Reporte', '$id_ruta')")){
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
            <th>Lugar</th>
            <th>Fecha</th>
            <th>Hora</th>
            <th>Estatus</th>
        </tr>
    </thead>
    <tbody>
    <?php 
        $sql_tmp = mysqli_query($conn,"SELECT * FROM tmp_reportes WHERE ruta = $id_ruta");
        $columnas = mysqli_num_rows($sql_tmp);
        if($columnas == 0){
            ?>
            <h5 class="center">No hay reportes en ruta</h5>
            <?php
        }else{
            while($tmp = mysqli_fetch_array($sql_tmp)){
                $id_reporte = $tmp['id_reporte'];
                $sql_reporte = mysqli_fetch_array(mysqli_query($conn,"SELECT * FROM reportes WHERE id_reporte = '$id_reporte'"));
                $atendido =$sql_reporte['atendido'];
                $estatus = '<span class="new badge red" data-badge-caption="Pendiente"></span>';
                if ($atendido == 1) {
                    $estatus = '<span class="new badge green" data-badge-caption="Terminado"></span>';
                }

                $id_cliente = $sql_reporte['id_cliente'];
                $sql_nombre = mysqli_fetch_array(mysqli_query($conn,"SELECT * FROM clientes WHERE id_cliente = '$id_cliente'"));
                $id_comunidad = $sql_nombre['lugar'];
                $comunidad = mysqli_fetch_array(mysqli_query($conn, "SELECT nombre FROM comunidades WHERE id_comunidad=$id_comunidad"))
    ?>
        <tr>
            <td><?php echo $sql_reporte['id_reporte']; ?></td>
            <td><?php echo $sql_nombre['nombre']; ?></td>
            <td><?php echo $sql_reporte['descripcion']; ?></td>
            <td><?php echo $comunidad['nombre'];?></td>
            <td><?php echo $sql_reporte['fecha']; ?></td>
            <td><?php echo $sql_reporte['hora_atendido']; ?></td>
            <td><?php echo $estatus; ?></td>
                    </tr>
            <?php
            }
            }
            mysqli_close($conn);
            ?>
    </tbody>
</table>
