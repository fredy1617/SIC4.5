<?php
include("is_logged.php");
include('../php/conexion.php');
date_default_timezone_set('America/Mexico_City');

$user_id = $_SESSION['user_id'];
$Estatus = $conn->real_escape_string($_POST['valorEstatus']);
$EstatusI = $conn->real_escape_string($_POST['valorEstatusI']);
$id = $conn->real_escape_string($_POST['valorIdOrden']);

$FechaAtendido = date('Y-m-d');
$Hora = date('H:i:s');

if ($EstatusI == 'PorConfirmar') {
	$sql = "UPDATE orden_servicios SET estatus = '$Estatus' WHERE id = '$id'";
}elseif ($EstatusI == 'Revisar') {

	$Trabajo = $conn->real_escape_string($_POST['valorTrabajo']);
	$Material = $conn->real_escape_string($_POST['valorMaterial']);
	$Tecnicos = $conn->real_escape_string($_POST['valorTecnicos']);
	$sql = "UPDATE orden_servicios SET trabajo = '$Trabajo', material = '$Material',  estatus = '$Estatus', tecnicos_r = '$Tecnicos', fecha_r = '$FechaAtendido', hora_r = '$Hora' WHERE id = '$id'";
}elseif ($EstatusI == 'Cotizar') {

	$Precio = $conn->real_escape_string($_POST['valorPrecio']);
	$Solucion = $conn->real_escape_string($_POST['valorSolucion']);
	$sql = "UPDATE orden_servicios SET precio = $Precio, solucion = '$Solucion', estatus = '$Estatus' WHERE id = '$id'";
	if ($Estatus == 'Cotizado') {
		mysqli_query($conn, "UPDATE orden_servicios SET cotizo = $user_id WHERE id = '$id'");
	}

}elseif ($EstatusI == 'Cotizado' or $EstatusI == 'Autorizado(Pedir)') {

	$Solucion = $conn->real_escape_string($_POST['valorSolucion']);
	$sql = "UPDATE orden_servicios SET solucion = '$Solucion', estatus = '$Estatus' WHERE id = '$id'";
	if ($Estatus == 'Autorizado(Pedir)') {
		mysqli_query($conn, "UPDATE orden_servicios SET confirmo = $user_id WHERE id = '$id'");
	}elseif ($Estatus == 'Ejecutar') {
		mysqli_query($conn, "UPDATE orden_servicios SET compro = $user_id WHERE id = '$id'");
	}

}elseif ($EstatusI == 'Ejecutar') {

	$Solucion = $conn->real_escape_string($_POST['valorSolucion']);
	$sql = "UPDATE orden_servicios SET solucion = '$Solucion', estatus = '$Estatus' WHERE id = '$id'";

	if ($Estatus == 'Facturar') {
		#ACTUALIZAR LOS TECNICOS FECHA Y HORA DE SOLUCION 
		$Tecnicos = $conn->real_escape_string($_POST['valorTecnicos']);
		$sql = "UPDATE orden_servicios SET  solucion = '$Solucion',  estatus = '$Estatus', tecnicos_s = '$Tecnicos', fecha_s = '$FechaAtendido', hora_s = '$Hora' WHERE id = '$id'";
		
		#CREAR TICKET
		?>
	    <script>
	    id = <?php echo $id; ?>;
	    var a = document.createElement("a");
	        a.target = "_blank";
	        a.href = "../php/ticket_orden.php?Id="+id;
	        a.click();
	    </script>
	    <?php  
	}
}
if(mysqli_query($conn, $sql)){
	echo '<script>M.toast({html:"Orden actualizada correctamente..", classes: "rounded"})</script>';
}else{
	echo '<script>M.toast({html:"Ocurrio un error!", classes: "rounded"})</script>';
}

?>
<script>
    function rec(){
      setTimeout("location.href='ordenes_servicio.php'",1000);
    }
    rec();
  </script>
        