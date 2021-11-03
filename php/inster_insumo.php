<?php 
include('../php/conexion.php');
include('is_logged.php');
$id_user = $_SESSION['user_id'];
$Id = $conn->real_escape_string($_POST['valorId']);
$Fecha = $conn->real_escape_string($_POST['valorFecha']);
$Descripcion = $conn->real_escape_string($_POST['valorDescripcion']);

if (mysqli_num_rows(mysqli_query($conn, "SELECT * FROM insumos WHERE vehiculo = $Id AND descripcion = '$Descripcion' AND fecha ='$Fecha'"))>0) {
	echo "<script>M.toast({html: 'Ya se encuentra un extra con la misma informacion registrado.', classes: 'rounded'})</script>";
}else{
	$sql = "INSERT INTO insumos (vehiculo, descripcion, fecha, registro) VALUES($Id, '$Descripcion', '$Fecha', $id_user)";
	if(mysqli_query($conn, $sql)){
		echo '<script >M.toast({html:"El insumo se dió de alta satisfactoriamente.", classes: "rounded"})</script>';	
		#UNA VES INSERTADO EL INSUMO MODIFICAMOS LA FECHA SEGUN LA OPCION ELEGIDA
		if ($Descripcion == 'General') {
			mysqli_query($conn, "UPDATE unidades SET fecha_aceite_motor = '$Fecha', fecha_aceite_trans = '$Fecha', fecha_aceite_dif = '$Fecha', fecha_balatas_d = '$Fecha', fecha_balatas_t = '$Fecha', fecha_aceleracion = '$Fecha', fecha_insp_mmt = '$Fecha', fecha_insp_fc = '$Fecha', fecha_insp_refri = '$Fecha' WHERE id = '$Id'");
		}else if ($Descripcion == 'Aceite de motor') {
			mysqli_query($conn, "UPDATE unidades SET fecha_aceite_motor = '$Fecha' WHERE id = '$Id'");
		}else if ($Descripcion == 'Aceite de diferencial') {
			mysqli_query($conn, "UPDATE unidades SET fecha_aceite_dif = '$Fecha' WHERE id = '$Id'");
		}else if ($Descripcion == 'Balatas delanteras') {
			mysqli_query($conn, "UPDATE unidades SET fecha_balatas_d = '$Fecha' WHERE id = '$Id'");
		}else if ($Descripcion == 'Balatas traseras') {
			mysqli_query($conn, "UPDATE unidades SET fecha_balatas_t = '$Fecha' WHERE id = '$Id'");
		}else if ($Descripcion == 'Cuerpo de aceleracion') {
			mysqli_query($conn, "UPDATE unidades SET fecha_aceleracion = '$Fecha' WHERE id = '$Id'");
		}else if ($Descripcion == 'Inspeccion de de monturas de motor y transmision') {
			mysqli_query($conn, "UPDATE unidades SET fecha_insp_mmt = '$Fecha' WHERE id = '$Id'");
		}else if ($Descripcion == 'Inspeccion de flechas y crucetas') {
			mysqli_query($conn, "UPDATE unidades SET fecha_insp_fc = '$Fecha' WHERE id = '$Id'");
		}else if ($Descripcion == 'Inspeccion de refrigerante') {
			mysqli_query($conn, "UPDATE unidades SET fecha_insp_refri = '$Fecha' WHERE id = '$Id'");
		}else if ($Descripcion == 'Aceite de transmision y transfer') {
			mysqli_query($conn, "UPDATE unidades SET fecha_aceite_trans = '$Fecha' WHERE id = '$Id'");
		}
	}else{
		echo '<script >M.toast({html:"Ha ocurrido un error.", classes: "rounded"})</script>';	
	}
}
?>
	<div class="row" id="resultado_update_insumo">
        <h3>Historial</h3>
        <div class="col s2"></div>
        <div class="row col s8">
          <!--CREAMOS UNA TABLA QUE MUESTRA EL HISTORIAL DE LOS MANTENIMIENTOS O INSUMOS -->
          <table class="bordered highlight responsive-table">
            <thead>
              <th>#</th>
              <th>Descripcion</th>
              <th>Fecha</th>
              <th>Registro</th>
              <th>Borrar</th>
            </thead>
            <tbody>
            <?php
            #SELECCIONAMOS TODOS LOS ROUTERS Y ANTENAS DISPONIBLES EN STOCK
            $tab = mysqli_query($conn, "SELECT * FROM insumos WHERE vehiculo = '$Id'");
            #SI TIENE DISPONIBLES LA RECORREMOS UNA POR UNA Y MOSTRAMOS LA INFORMACION
            while($insumo = mysqli_fetch_array($tab)){
              ?>
              <tr>
                <td><?php echo $insumo['id']; ?></td>
                <td><?php echo $insumo['descripcion']?></td>
                <td><?php echo $insumo['fecha']; ?></td>
                <td><?php echo $insumo['registro']; ?></td>
                <td><a onclick="borrar(<?php echo $insumo['id'];?>);" class="btn btn-floating red darken-1 waves-effect waves-light"><i class="material-icons">delete</i></td>
              </tr>
            <?php
            }
            ?> 
            </tbody>
          </table>
        </div>
    </div>   