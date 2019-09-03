<!DOCTYPE html>
<html>
<head>
	<title>SIC | Rutas</title>
<?php
include ('fredyNav.php');
include('../php/conexion.php');
include('../php/cobrador.php');
?>
</head>
<body>
	<div class="container">
		<div class="row">
			<h3 class="hide-on-med-and-down">Reporte de Rutas</h3>
  			<h5 class="hide-on-large-only">Reporte de Rutas</h5>
		<a class="waves-effect waves-green btn pink right" href="../views/rutas.php">Todas<i class="material-icons right">done</i></a>	
		</div>
		<?php
		for ($i=0; $i <= 1; $i++) {
		$inicia=$i*3; 

		$rutas = mysqli_query($conn, "SELECT * FROM rutas ORDER BY id_ruta DESC LIMIT $inicia,3 ");
		?>
		<div class="row">
		<?php
		date_default_timezone_set('America/Mexico_City');
		$Hoy = date('Y-m-d');
		$filas = mysqli_num_rows($rutas);
		if ($filas == 0) {
	       ?>
            <h5 class="center">No hay rutas</h5>
            <?php                    
	    } else {	

		while($ruta = mysqli_fetch_array($rutas)) {
		$id_ruta = $ruta['id_ruta'];
		$instalaciones = mysqli_fetch_array(mysqli_query($conn,"SELECT count(*) FROM tmp_pendientes WHERE ruta_inst = $id_ruta"));
		$reportes = mysqli_fetch_array(mysqli_query($conn,"SELECT count(*) FROM tmp_reportes WHERE ruta = $id_ruta"));
		$Total =$instalaciones['count(*)']+$reportes['count(*)'];
		$instalacion = mysqli_fetch_array(mysqli_query($conn,"SELECT count(*) FROM tmp_pendientes AS Pen JOIN clientes AS Cli ON Pen.id_cliente = Cli.id_cliente WHERE ruta_inst = $id_ruta and instalacion = 1"));
		$reporte = mysqli_fetch_array(mysqli_query($conn,"SELECT count(*) FROM tmp_reportes  AS TRep JOIN reportes AS Rep ON TRep.id_reporte = Rep.id_reporte WHERE ruta = $id_ruta and atendido = 1 OR atendido !=  NULL"));
		$Avance = $instalacion['count(*)']+$reporte['count(*)'];
		$Texto = $ruta['tecnicos'];
		$Tecnicos = explode(",", $Texto);
		$n=count($Tecnicos);
		$fecha_ruta=$ruta['fecha'];
		$mas_dias = strtotime('+1 day', strtotime($fecha_ruta));
		$dosdias = date('Y-m-d', $mas_dias);
		if ($Total == $Avance OR $Hoy > $dosdias) {
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
		}
		}
		$EstatusR = mysqli_fetch_array(mysqli_query($conn, "SELECT estatus FROM rutas WHERE id_ruta = $id_ruta"));
		if ($EstatusR ['estatus']==0) {
			$Estatus = '<h6 class="blue-text"><b>'."$Avance".' / '."$Total".'</b></h6>';
		}else{
			$Estatus = '<span class="new badge green" data-badge-caption="Terminado"></span>';
		}
		?>
		<div class="col s12 m4 l4">
		    <h5 class="header">Ruta No. <?php echo $id_ruta; ?></h5>
		    <div class="card horizontal">
		      <div class="card-image">
		        <img class="responsive-img" src="img/som.png">
		      </div>
		      <div class="card-stacked">
		        <div class="card-content">
		          <p>
		          	<b>Estatus:</b><br>
		          	<?php echo $Estatus; ?>
		          	<b>Tecnicos:</b>
		           	<ul>
		           		<?php foreach ($Tecnicos as $tecnico){?>
		          		<li class="red-text darken-2"><?php echo $tecnico; ?></li>
		          		<?php }?>
		          	</ul>
		          	<span class="new badge pink" data-badge-caption=""><?php echo $fecha_ruta; ?></span>
		          </p>
		        </div>
		        <div class="card-action">
		        	<form method="post" action="../views/detalles_ruta.php"><input id="id_ruta" name="id_ruta" type="hidden" value="<?php echo $ruta['id_ruta']; ?>"><button class="waves-effect waves-light btn-small white orange-text"><b>Detalles</b></button></form>
		        </div>
		      </div>
		    </div>
		</div>
		<?php
		}
		}
		?>
		</div>
		<?php
		} 
		?>	
	</div>
</body>
</html>