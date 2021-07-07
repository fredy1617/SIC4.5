<?php
include('../php/conexion.php');
date_default_timezone_set('America/Mexico_City');
session_start();
$id_user = $_SESSION['user_id'];
$Fecha_hoy = date('Y-m-d');
$Hora = date('H:i:s');
$id = $conn->real_escape_string($_POST['valorIdOrden']);
$LiquidarS = $conn->real_escape_string($_POST['valorLiquidarS']);
$IdCliente = $conn->real_escape_string($_POST['valorIdCliente']);

if ($LiquidarS <= 0) {
 	$sql_orden = "UPDATE orden_servicios SET estatus = 'Facturado', fecha_f = '$Fecha_hoy' WHERE id = '$id'";
 	if(mysqli_query($conn, $sql_orden)){
		echo '<script>M.toast({html:"Orden actualizada correctamente..", classes: "rounded"})</script>';
	}else{
		echo '<script>M.toast({html:"Ocurrio un error!", classes: "rounded"})</script>';
	}
}else{
	$precio = mysqli_fetch_array(mysqli_query($conn, "SELECT precio FROM orden_servicios WHERE id = '$id'"));
	$liq = ($precio['precio'] == $LiquidarS)? 1:0; 
 	$sql_orden = "UPDATE orden_servicios SET estatus = 'Facturado', fecha_f = '$Fecha_hoy', liquidada = '$liq' WHERE id = '$id'";
 	if(mysqli_query($conn, $sql_orden)){
		echo '<script>M.toast({html:"Orden actualizada correctamente..", classes: "rounded"})</script>';
		$TipoE = $conn->real_escape_string($_POST['valorTipoE']);
		$Descripcion = 'Liquidacion de Orden '.$id;
		$sql_ver = mysqli_query($conn, "SELECT * FROM pagos WHERE id_cliente = $IdCliente AND descripcion = '$Descripcion' AND tipo = 'Orden Servicio'");
		if(mysqli_num_rows($sql_ver)>0){
		    echo '<script>M.toast({html:"Ya se encuentra un pago con los mismos datos.", classes: "rounded"})</script>';
		}else{
			$sql = "INSERT INTO pagos (id_cliente, descripcion, cantidad, fecha, hora, tipo, id_user, corte, corteP, tipo_cambio) VALUES ($IdCliente, '$Descripcion', '$LiquidarS', '$Fecha_hoy', '$Hora', 'Orden Servicio', $id_user, 0, 0, '$TipoE')";
			if (mysqli_query($conn, $sql)) {
				$ultimo =  mysqli_fetch_array(mysqli_query($conn, "SELECT MAX(id_pago) AS id FROM pagos WHERE id_cliente = $IdCliente"));            
    			$id_pago = $ultimo['id'];
				?>
			    <script>
			    id_pago = <?php echo $id_pago; ?>;
			    var a = document.createElement("a");
			        a.target = "_blank";
			        a.href = "../php/imprimir.php?IdPago="+id_pago;
			        a.click();
			    </script>
			    <?php
			}
		}
	}else{
		echo '<script>M.toast({html:"Ocurrio un error!", classes: "rounded"})</script>';
	}
}
?>
<script>
	function gas() {
      setTimeout("location.href='facturar_l.php'", 800);
    }
    gas();
</script>	   


        