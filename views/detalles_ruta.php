<!DOCTYPE html>
<html>
<head>
	<title>SIC | Detalles Ruta</title>
</head>
<?php
include('fredyNav.php');
include('../php/conexion.php');
if (isset($_POST['id_ruta']) == false) {
  ?>
  <script>
    function atras(){
      M.toast({html: "Regresando...", classes: "rounded"})
      setTimeout("location.href = 'menu_rutas.php'", 1000);
    }
    atras();
  </script>
  <?php
}else{
$id_ruta = $_POST['id_ruta'];
$ruta = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM rutas WHERE id_ruta = $id_ruta"));
?>
<script>
  function reimprimir(id_ruta){
    var a = document.createElement("a");
        a.target = "_blank";
        a.href = "../php/imprimir_ruta.php?IdRuta="+id_ruta;
        a.click();
  }
  function terminar(ruta){ 
      $.post("../php/terminar_ruta.php", { 
          valorRuta: ruta,
        }, function(mensaje) {
            $("#Terminar").html(mensaje);         
        });
  };
  function ruta_pendientes(id_cliente,ruta) {
    if (id_cliente == "") {
      M.toast({html:"Ocurrio un error al seleccionar el cliente.", classes: "rounded"});
    }else{
      $.post("../php/agregar_inst_detalle.php", {
          valorIdCliente: id_cliente,
          valorRuta: ruta,
        }, function(mensaje) {
            $("#instalaciones_ruta").html(mensaje);
        }); 
    }
  };
  function ruta_reportes(id_reporte,ruta) {
    if (id_reporte == "") {
      M.toast({html:"Ocurrio un error al seleccionar el reporte.", classes: "rounded"});
    }else{
      $.post("../php/agregar_rep_detalle.php", {
          valorIdReporte: id_reporte,
          valorRuta: ruta,
        }, function(mensaje) {
            $("#resultado_ruta_reporte").html(mensaje);
        }); 
    }
  };
</script>
<body>
	<div class="container">
		<h3 class="center blue-text hide-on-med-and-down">Detalles de la Ruta No. <?php echo $id_ruta; ?></h3>
    <h5 class="center blue-text hide-on-large-only">Detalles de la Ruta No. <?php echo $id_ruta; ?></h5><br><br>
<?php 
    if ($ruta['estatus'] == 0) {
?>
<div class="row">
<!-- ----------------------------  TABs o MENU  ---------------------------------------->
	<div class="row">
	    <div class="col s12">
	    <ul id="tabs-swipe-demo" class="tabs">
	      <li class="tab col s6"><a class="active black-text" href="#test-swipe-1">Instalaciones</a></li>
	      <li class="tab col s6"><a class="black-text" href="#test-swipe-2">Reportes</a></li>
	    </ul>
</div>
<!-- ----------------------------  FORMULARIO 1 Tabs  ---------------------------------------->
<div  id="test-swipe-1" class="col s12">
	  <div class="row" >
		  <h3 class="hide-on-med-and-down">Instalaciones Pendientes</h3>
		  <h5 class="hide-on-large-only">Instalaciones Pendientes</h5>
		</div>
    <div class="row">
		  <table class="bordered highlight responsive-table">
		    <thead>
		      <tr>
		        <th>No. Cliente</th>
		        <th>Nombre</th>
            <th>Servicio</th>
		        <th>Telefono</th>
		        <th>Lugar</th>
		        <th>Registro</th>
		        <th>Agregar</th>
		      </tr>
		    </thead>
		    <tbody>
				<?php 
				$sql_pendientes = mysqli_query($conn,"SELECT * FROM clientes WHERE instalacion is NULL ORDER BY id_cliente ASC");
				$filas = mysqli_num_rows($sql_pendientes);
				        //Si no existe ninguna fila que sea igual a $consultaBusqueda, entonces mostramos el siguiente mensaje
				if ($filas <= 0) {
				    echo '<script type="text/javascript">M.toast({html:"No se encontraron reportes.", classes: "rounded"})</script>';
				} else {
				while($pendientes = mysqli_fetch_array($sql_pendientes)){
				$id_cliente = $pendientes['id_cliente'];
				$sql_chequeo = mysqli_query($conn, "SELECT * FROM tmp_pendientes WHERE id_cliente = $id_cliente");
				$numero_columnas = mysqli_num_rows($sql_chequeo);
				if($numero_columnas<=0){
				$id_comunidad = $pendientes['lugar'];
				$sql_comunidad = mysqli_fetch_array(mysqli_query($conn,"SELECT nombre FROM comunidades WHERE id_comunidad=$id_comunidad"));
				?>
  				<tr>
  				  <td><?php echo $id_cliente; ?></td>
            <td><?php echo $pendientes['nombre'];?></td>
  				  <td><?php echo $pendientes['servicio'];?></td>
  				  <td><?php echo $pendientes['telefono'];?></td>
  				  <td><?php echo $sql_comunidad['nombre'];?></td>
  				 	<td><?php echo $pendientes['registro'];?></td>				          
  				  <td><a onclick="ruta_pendientes(<?php echo $pendientes['id_cliente'];?>,<?php echo $id_ruta;?>);" class="btn btn-floating pink waves-effect waves-light"><i class="material-icons">add</i></a></td>
  			</tr>
			<?php
			}//Fin IF        
			}//Fin while $resultados
			} //Fin else $filas
			?>
		  </tbody>
		</table>
	  </div>
</div>
<!-- ----------------------------  FORMULARIO 2 Tabs  ---------------------------------------->
<div  id="test-swipe-2" class="col s12">
	       
	<div class="row">
    <h3 class="hide-on-med-and-down">Reportes Pendientes</h3>
    <h5 class="hide-on-large-only">Reportes Pendientes</h5>
  </div>
  <div class="row">
  <table class="bordered  highlight responsive-table">
    <thead>
      <tr>
          <th>Estatus</th>
          <th>No.Reporte </th>
          <th>Cliente</th>
          <th>Descripción</th>
          <th>Fecha</th>
          <th>Comunidad</th>
          <th>Técnico</th>
          <th>+Ruta</th>
      </tr>
    </thead>
    <tbody>
    <?php
    $sql = "SELECT * FROM reportes  WHERE (fecha_visita = '$Hoy'  AND atender_visita = 0) OR (fecha_visita < '$Hoy' AND atender_visita = 0 AND visita = 1) OR atendido != 1 OR atendido IS NULL  ORDER BY fecha";
    $consulta = mysqli_query($conn, $sql);
    //Obtiene la cantidad de filas que hay en la consulta
    $filas = mysqli_num_rows($consulta);
    //Si no existe ninguna fila que sea igual a $consultaBusqueda, entonces mostramos el siguiente mensaje
    if ($filas <= 0) {
      echo '<script>M.toast({html:"No se encontraron reportes.", classes: "rounded"})</script>';
    } else {
    //La variable $resultado contiene el array que se genera en la consulta, así que obtenemos los datos y los mostramos en un bucle
    while($resultados = mysqli_fetch_array($consulta)) {
      $id_reporte = $resultados['id_reporte'];
      $sql_buscar = mysqli_query($conn, "SELECT * FROM tmp_reportes WHERE id_reporte = '$id_reporte'");

      if(mysqli_num_rows($sql_buscar)<0 or mysqli_num_rows($sql_buscar)==NULL){
      $id_reporte = $resultados['id_reporte'];
      $id_cliente = $resultados['id_cliente'];
      $sql = mysqli_query($conn, "SELECT * FROM clientes WHERE id_cliente=$id_cliente");
      $filas = mysqli_num_rows($sql);
      if ($filas == 0) {
        $sql = mysqli_query($conn, "SELECT * FROM especiales WHERE id_cliente=$id_cliente");
      }
      $cliente = mysqli_fetch_array($sql);
      $id_comunidad = $cliente['lugar'];
      $comunidad = mysqli_fetch_array(mysqli_query($conn, "SELECT nombre FROM comunidades WHERE id_comunidad=$id_comunidad"));
      if($resultados['tecnico']==''){
        $tecnico1[0] = '';
        $tecnico1[1] = 'Sin tecnico';
      }else{
        $id_tecnico = $resultados['tecnico'];
        $tecnico1 = mysqli_fetch_array(mysqli_query($conn, "SELECT user_id, user_name FROM users WHERE user_id=$id_tecnico"));  
      }
      $Estatus2= 0;
      if ($resultados['fecha']<$Hoy) {
        $date1 = new DateTime($Hoy);
        $date2 = new DateTime($resultados['fecha']);
        //Le restamos a la fecha date1-date2
        $diff = $date1->diff($date2);
        $Estatus2= $diff->days;
      }
      $estatus=$Estatus2;
      if ($resultados['estatus']>$Estatus2) { $estatus = $resultados['estatus']; }
      $color = "green";
      if ($estatus== 1) { $color = "yellow darken-2";
      }elseif ($estatus == 2) { $color = "orange darken-4";
      }elseif ($estatus >= 3) { $color = "red accent-4"; }
      if ($resultados['visita']==1) {
        $color = "green";
        $estatus = 0;
        if ($resultados['fecha_visita']<$Hoy) {
          $color = "red accent-4";
          $estatus = "YA!";
          $Tecnico = $resultados['tecnico'];
          $nombreTecnico  = mysqli_fetch_array(mysqli_query($conn,"SELECT * FROM users WHERE user_id = '$Tecnico'"));
          $Nombre = $nombreTecnico['firstname'];
          
          mysqli_query($conn,"UPDATE reportes SET descripcion = 'RETRASO DE VISITA NO ATENDIO: ".$Nombre." VISTAR URGENTEMENTE!'  WHERE id_reporte = $id_reporte");
            $resultados = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM reportes WHERE id_reporte=$id_reporte"));  
        }
      }
      ?>
          <tr>
            <td><span class="new badge <?php echo $color;?>" data-badge-caption=""><?php echo $estatus;?></span></td>
            <td><b><?php echo $id_reporte;?></b></td>
            <td><a class="tooltipped" data-position="top" data-tooltip="<?php echo 'Telefono: '. $cliente['telefono']; echo '  Comunidad: '.$comunidad['nombre'];?>"><?php echo $cliente['nombre'];?></a></td>
            <td><?php echo $resultados['descripcion'];?></td>
            <td><?php echo $resultados['fecha'];?></td>
            <td><?php echo $comunidad['nombre'];?></td>
            <td><?php echo $tecnico1[1];?></td>                    
            <td><a onclick="ruta_reportes(<?php echo $id_reporte;?>, <?php echo $id_ruta;?>);" class="btn btn-floating pink waves-effect waves-light"><i class="material-icons">add</i></a></td>
          </tr>                
    <?php  
    }//Fin IF        
    }//Fin while $resultados
    } //Fin else $filas
    ?>
    </tbody>
  </table>
	</div>
</div>
</div>
<a class="waves-effect waves-light btn pink right" onclick="terminar(<?php echo $id_ruta; ?>);">Terminar<i class="material-icons right">done</i></a>
</div>
    <?php
    }//fin if
    ?>
    	<br><br>
    	<div id="Terminar"></div>
        <h4 class="hide-on-med-and-down"> Instalaciones</h4>
        <h5 class="hide-on-large-only"> Instalaciones</h5>
        <div id="instalaciones_ruta">
            <table class="bordered highlight responsive-table">
                <thead>
                    <tr>
                        <th>No. Cliente</th>
                        <th>Nombre</th>
                        <th>Servicio</th>
                        <th>Telefono</th>
                        <th>Lugar</th>
                        <th>Dirección</th>
                        <th>Fecha</th>
                        <th>Hora</th>
                        <th>Estatus</th>
                    </tr>
                </thead>
                <tbody>
                <?php 
                $sql_tmp = mysqli_query($conn,"SELECT * FROM tmp_pendientes WHERE ruta_inst =$id_ruta");
                $columnas = mysqli_num_rows($sql_tmp);
                if($columnas == 0){
                    ?>
                    <h5 class="center">No hay instalaciones en ruta</h5>
                    <?php
                }else{
                    while($tmp = mysqli_fetch_array($sql_tmp)){
                    	$id_cliente = $tmp['id_cliente'];
                    	$cliente = mysqli_fetch_array(mysqli_query($conn,"SELECT * FROM clientes WHERE id_cliente =$id_cliente"));
                    	$instalacion =$cliente['instalacion'];
                    	$estatus = '<span class="new badge red" data-badge-caption="Pendiente"></span>';
                    	if ($instalacion == 1) {
                    		$estatus = '<span class="new badge green" data-badge-caption="Terminado"></span>';
                    	}
                        $id_comunidad = $tmp['lugar'];
                        $sql_comunidad1 = mysqli_fetch_array(mysqli_query($conn,"SELECT nombre FROM comunidades WHERE id_comunidad=$id_comunidad"));
                ?>
                    <tr>
                      <td><?php echo $tmp['id_cliente']; ?></td>
                      <td><?php echo $tmp['nombre']; ?></td>
                      <td><?php echo $cliente['servicio']; ?></td> 
                      <td><?php echo $tmp['telefono']; ?></td>
                      <td><?php echo $sql_comunidad1['nombre']; ?></td>
                      <td><?php echo $tmp['direccion']; ?></td> 
                      <td><?php echo $cliente['fecha_instalacion']; ?></td> 
                      <td><?php echo $cliente['hora_alta']; ?></td> 
                      <td><?php echo $estatus; ?></td>
            
                    </tr>
                <?php
                    }
                }
                ?>
                </tbody>
            </table>
        </div><br>		
		  <h4 class="hide-on-med-and-down"> Reportes</h4>
      <h5 class="hide-on-large-only"> Reportes</h5>
		  <div id="resultado_ruta_reporte">
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
                        $sql = mysqli_query($conn, "SELECT * FROM clientes WHERE id_cliente=$id_cliente");
                       $filas = mysqli_num_rows($sql);
                       if ($filas == 0) {
                         $sql = mysqli_query($conn, "SELECT * FROM especiales WHERE id_cliente=$id_cliente");
                        }
                        $cliente = mysqli_fetch_array($sql);
                       $id_comunidad = $cliente['lugar'];
      			           	$comunidad = mysqli_fetch_array(mysqli_query($conn, "SELECT nombre FROM comunidades WHERE id_comunidad=$id_comunidad"));
                ?>
                    <tr>
                      <td><?php echo $sql_reporte['id_reporte']; ?></td>
                      <td><?php echo $cliente['nombre']; ?></td>
                      <td><?php echo $sql_reporte['descripcion']; ?></td>
                       <td><?php echo $comunidad['nombre'];?></td>
                      <td><?php echo $sql_reporte['fecha_solucion']; ?></td>
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
     	</div><br><br>
        <a onclick="reimprimir(<?php echo $id_ruta;?>)" class="btn waves-light waves-effect right pink">Imprimir</a>
      <br><br>
    </div>
</body>
<?php
}
?>
</html>