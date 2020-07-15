<?php
session_start();
include('../php/conexion.php');
date_default_timezone_set('America/Mexico_City');
$id_user = $_SESSION['user_id'];
$Fecha = date('Y-m-d');
$IdCentral = $conn->real_escape_string($_POST['valorIdCentral']);
$Tipo = $conn->real_escape_string($_POST['valorTipo']);
$Cantidad = $conn->real_escape_string($_POST['valorCantidad']);
$Descripcion = $conn->real_escape_string($_POST['valorDescripcion']);
$MES = $conn->real_escape_string($_POST['valorMes']);

if(mysqli_num_rows(mysqli_query($conn, "SELECT * FROM pagos_centrales WHERE descripcion='$Descripcion' AND tipo='$Tipo' AND id_central='$IdCentral'"))>0){
	echo '<script >M.toast({html:"Ya se encuentra un pago con los mismos datos registrados.", classes: "rounded"})</script>';
}else{
	$sql = "INSERT INTO pagos_centrales (cantidad, descripcion, tipo, usuario, fecha, id_central) VALUES('$Cantidad', '$Descripcion', '$Tipo', '$id_user', '$Fecha', '$IdCentral')";
	if(mysqli_query($conn, $sql)){
		echo '<script >M.toast({html:"El pago se dió de alta satisfactoriamente.", classes: "rounded"})</script>';
		$array =  array('ENERO' => '02','FEBRERO' => '03', 'MARZO' => '04','ABRIL' => '05', 'MAYO' => '06', 'JUNIO' => '07', 'JULIO' => '08', 'AGOSTO' => '09', 'SEPTIEMBRE' => '10', 'OCTUBRE' => '11', 'NOVIEMBRE' => '12',  'DICIEMBRE' => '01');
		$mes = $array[$MES];
		$central =mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM centrales WHERE id='$IdCentral'"));
		$fecha_vencimiento = $central['vencimiento_renta'];
		if ($fecha_vencimiento < '2018-01-01') {
			$fecha_vencimiento = $Fecha;
		}
		if ($Tipo == 'Anual') {
			$AÑO = strtotime('+1 year', strtotime($fecha_vencimiento));
			$año = date('Y', $AÑO);
  			$Vencimiento = date($año.'-'.$mes.'-01');
		}else{
			$AÑO = strtotime('+1 month', strtotime($fecha_vencimiento));
			$Vencimiento = date('Y-m-01', $AÑO);
		}
		mysqli_query($conn, "UPDATE centrales SET vencimiento_renta='$Vencimiento'WHERE id='$IdCentral'");
	}else{
		echo '<script >M.toast({html:"Ha ocurrido un error.", classes: "rounded"})</script>';	
	}
}
?>
<table class="bordered highlight responsive-table">
          <thead>
            <tr>
              <th>#</th>
              <th>Cantidad</th>
              <th>Tipo</th>
              <th>Descripción</th>
              <th>Usuario</th>
              <th>Fecha</th>
              <!--<th>Imprimir</th>-->
              <th>Borrar</th>
            </tr>
          </thead>
          <tbody>
          <?php
          $sql_pagos = "SELECT * FROM pagos_centrales WHERE tipo != 'Dispositivo' AND id_central = '$IdCentral' ORDER BY id DESC";
          $resultado_pagos = mysqli_query($conn, $sql_pagos);
          $aux = mysqli_num_rows($resultado_pagos);
          if($aux>0){
          while($pagos = mysqli_fetch_array($resultado_pagos)){
            $id_user = $pagos['usuario'];
            $user = mysqli_fetch_array(mysqli_query($conn, "SELECT user_name FROM users WHERE user_id = '$id_user'"));
          ?>
            <tr>
              <td><b><?php echo $aux;?></b></td>
              <td>$<?php echo $pagos['cantidad'];?></td>
              <td><?php echo $pagos['tipo'];?></td>
              <td><?php echo $pagos['descripcion'];?></td>
              <td><?php echo $user['user_name'];?></td>
              <td><?php echo $pagos['fecha'];?></td>
              <!--<td><a onclick="imprimir(<?php echo $pagos['id'];?>);" class="btn btn-floating pink waves-effect waves-light"><i class="material-icons">print</i></a></td>-->
              <td><a onclick="borrar(<?php echo $pagos['id'];?>);" class="btn btn-floating red darken-1 waves-effect waves-light"><i class="material-icons">delete</i></a></td>
            </tr>
            <?php
            $aux--;
            }//Fin while
            }else{
            echo "<center><b><h5 class = 'red-text'>Esta central aún no ha registrado pagos</h5 ></b></center>";
          }
          ?> 
          </tbody>
        </table>
<?php
mysqli_close($conn);
?>