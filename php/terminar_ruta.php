<?php 
include('../php/conexion.php');
$id_ruta = $conn->real_escape_string($_POST['valorRuta']);
if(mysqli_query($conn, "UPDATE rutas SET estatus = 1  WHERE id_ruta = $id_ruta")){
	$sql_tmp = mysqli_query($conn,"SELECT * FROM tmp_reportes WHERE ruta = $id_ruta");
	$columnas = mysqli_num_rows($sql_tmp);
    if($columnas > 0){
		 while($tmp = mysqli_fetch_array($sql_tmp)){
		 	$id_reporte = $tmp['id_reporte'];
            $sql_reporte = mysqli_fetch_array(mysqli_query($conn,"SELECT * FROM reportes WHERE id_reporte = $id_reporte"));
            $atendido =$sql_reporte['atendido'];
            if ($atendido != 1) {
                mysqli_query($conn, "DELETE FROM tmp_reportes  WHERE id_reporte = $id_reporte");
            }
        }
	}
	$sql_tmp = mysqli_query($conn,"SELECT * FROM tmp_pendientes WHERE ruta_inst = $id_ruta");
	$columnas = mysqli_num_rows($sql_tmp);
    if($columnas > 0){
		 while($tmp = mysqli_fetch_array($sql_tmp)){
		 	$id_cliente = $tmp['id_cliente'];
            $sql_reporte = mysqli_fetch_array(mysqli_query($conn,"SELECT * FROM clientes WHERE id_cliente = $id_cliente"));
            $instalacion =$sql_reporte['instalacion'];
            if ($instalacion != 1) {
                mysqli_query($conn, "DELETE FROM tmp_pendientes WHERE id_cliente = $id_cliente");
            }
        }
	}
	echo '<script>M.toast({html:"La ruta se actualizado correctamente.", classes: "rounded"})</script>';
}else{
	echo '<script>M.toast({html:"Ocurrio un error y no se actualizo.", classes: "rounded"})</script>';
}
//echo mysqli_error($conn);
mysqli_close($conn);
?>
<script>    
	var a = document.createElement("a");
	  a.href = "../views/menu_rutas.php";
	  a.click();
</script>