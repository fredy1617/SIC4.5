<?php
#INCLUIMOS EL ARCHIVO CON LOS DATOS Y CONEXXION A LA BASE DE DATOS
include ("../php/conexion.php");
#DEFINIMOS UNA ZONA HORARIA
date_default_timezone_set('America/Mexico_City');
#GENERAMOS UNA FECHA DEL DIA EN CURSO REFERENTE A LA ZONA HORARIA
$Hoy = date('Y-m-d');

#RECIBIMOS EL LA VARIABLE texto CON EL METODO POST DEL DOCUMENTO ruta_comunidad.php PARA HACER LA BUSQUEDA DE LA COMUNIDAD
$Texto = $conn->real_escape_string($_POST['texto']);
#VERIFICAMOS SI LA VARIABLE RECIBIDA CONTIENE ALGUN TEXTO
if ($Texto != "") {
	#SI RECIBIMOS ALGUN TEXTO BUSCAMOS ALGUNA COMUNIDAD CON ESE NOMBRE SIMILAR
	$sql = "SELECT * FROM comunidades WHERE nombre LIKE '$Texto%' OR nombre LIKE '%$Texto'";
}else{
	#SI EL TEXTO ESTA VACIO SELECCIONAMOS TODAS LAS COMUNIDADES
	$sql = "SELECT * FROM comunidades";
	echo '<script>M.toast({html:"all.", classes: "rounded"})</script>';
}

$consulta =mysqli_query($conn, $sql);
$filas = mysqli_num_rows($consulta);
#VERIFICAMOS SI EL CONTADOR DE SQL DE LAS COMUNIDADES ES 0
if ($filas == 0) {
	#SI SE ENCUENTRA EN 0 MOSTRAR MENSAJE
	echo '<script>M.toast({html:"No se encontraron comunidades.", classes: "rounded"})</script>';
}else{
	#SI EL CONTADOR ES MAYOR A 0 INICIAMOS NUESTRA TABLA DE COMUINADES
	echo '<ul class="collapsible">'; // INICIA UL CONTENIDO COMUNIDADES
	#RECORREMOS LAS COMUIDADES UNA POR UNA
      while ($comunidad = mysqli_fetch_array($consulta)) {
        $nombre = $comunidad['nombre'];//NOMBRE DE LA COMUNIDAD EN TURNO
        $id_comunidad = $comunidad['id_comunidad']; //ID DE LA COMUNIDAD EN TURNO
        #BUSCANOS TODAS LAS INSTALCIONES PENDIENTES PERTENECIENTES A LA COMUNIDAD EN TURNO
        $instalaciones = mysqli_query($conn, "SELECT * FROM clientes WHERE instalacion IS NULL AND lugar = '$id_comunidad' ORDER BY id_cliente ASC");
        #SELECCIONAMOS TODOS LOS REPORTES PENDIENTES QUE ESTEN EN CAMPO = 1 Y QUE PERTENEZCAN A LA COMUNIDAD EN TURNO  
        $reportes = mysqli_query($conn, "SELECT * FROM clientes INNER JOIN reportes ON clientes.id_cliente = reportes.id_cliente WHERE ((reportes.fecha_visita = '$Hoy'  AND reportes.atender_visita = 0) OR (reportes.fecha_visita < '$Hoy' AND reportes.atender_visita = 0 AND reportes.visita = 1) OR reportes.atendido != 1 OR reportes.atendido IS NULL) AND reportes.campo = 1 AND clientes.lugar = '$id_comunidad'  ORDER BY reportes.fecha");
        #SELECCIONAMOS TODAS LAS ORDENES PENDIENTES QUE PERTEBEZCAN A LA COMUNIDAD EN TURNO Y PERTENEZCAN A REDES dpto = 1
        $Ordenes = mysqli_query($conn, "SELECT * FROM orden_servicios INNER JOIN especiales ON orden_servicios.id_cliente = especiales.id_cliente WHERE orden_servicios.estatus IN ('Revisar', 'Realizar')  AND orden_servicios.dpto = 1  AND especiales.lugar = '$id_comunidad'");
        #BUSCAMOS TODOS LOS MANTENIMIENTOS PENDIENTES PERTENECIENTES A LA COMUNIDAD EN TURNO
        $Mantenimiento = mysqli_query($conn, "SELECT * FROM especiales INNER JOIN reportes ON especiales.id_cliente = reportes.id_cliente WHERE ((reportes.fecha_visita = '$Hoy'  AND reportes.atender_visita = 0) OR (reportes.fecha_visita < '$Hoy' AND reportes.atender_visita = 0 AND reportes.visita = 1) OR reportes.atendido != 1 OR reportes.atendido IS NULL) AND especiales.lugar = '$id_comunidad' AND especiales.id_cliente > 10000 AND reportes.descripcion LIKE 'Mantenimiento:%'");
        
        #CONTABILIZAMOS EL TOTAL DE TODAS LAS SELECCIONES QUE HIZIMOS
        $SiInst = mysqli_num_rows($instalaciones);  $SiRep = mysqli_num_rows($reportes);  $SiOrdenes = mysqli_num_rows($Ordenes); $SiMant = mysqli_num_rows($Mantenimiento);  
      	#VERIFICAMOS QUE TENGAMOS ALMENOS ALGUN PENDIENTE PARA PODER MOSTRAR LA COMUNIDAD Y SU(S) PENDIENTE(S)
        if ($SiInst >0 OR $SiRep>0 OR $SiOrdenes>0 OR $SiMant > 0) {
          #INICIA LI RECUADRO Y DESPLEGABLE CON LA INFORMACION DE CADA COMUIDAD
          ?>
          <li>
          	<!-- CABEZERA HEADER BOTON SOLO MOSTRAR PARA PC--->
            <a onclick = "add_ruta(<?php echo $id_comunidad; ?>);" class = "hide-on-med-and-down btn waves-effect waves-light pink right col s12 m5 l5">Agregar A Ruta</a>
            <!-- CABEZERA HEADER Barras desplegables SOLO MOSTRAR PARA PC MUESTRA EL NOMBRE DE LA COMUNIDAD--->
	        <div class="collapsible-header col s12 m7 l7 hide-on-med-and-down">
            	<!--  MUESTRA EL NOMBRE DE LA COMUNIDAD--->
	            <h5><?php echo $nombre; ?> <i class="material-icons right">arrow_drop_down</i></h5>
	            <b class="blue-text"> -Instalaciones(<?php echo $SiInst; ?>); -Reportes(<?php echo $SiRep; ?>); -Oredenes(<?php echo $SiOrdenes; ?>); -Mantenimientos(<?php echo $SiMant; ?>)</b>
	        </div>
	        <!--CABEZERA HEADER BOTON SOLO MOSTRAR PARA MOVIL--->                    
            <a onclick = "add_ruta(<?php echo $id_comunidad; ?>);" class = "hide-on-large-only btn waves-effect waves-light pink right col s12 m5 l5">+ Ruta</a>
            <!--CABEZERA HEADER  Barras desplegables SOLO MOSTRAR PARA MOVIL--->
	        <div class="collapsible-header col s12 m7 l7 hide-on-large-only">
            	<!--  MUESTRA EL NOMBRE DE LA COMUNIDAD--->
	            <h6><?php echo $nombre; ?><i class="material-icons right">arrow_drop_down</i></h6> 
	        </div>
	        <!-- INICIAMOS EL CONTENIDO DE LA COMUNIDAD (PENDIENTES) -->		
            <div class="collapsible-body"> 
              	<?php
              	#VERIFICAMOS SI HAY INSTALACIONES PENDIENTES
              	if ($SiInst > 0) {
              		#SI HAY INSTALACIONES PENDIENTES MOSTRAMOS TITULO Y CREAMOS TABLA CON LAS INSTALACIONES
	              echo '<h6><b class = "indigo-text">Instalaciones Pendientes</b></h6>';
	              ?>
	              <table class="border highlight responsive-table">
	                <thead>
	                  <tr>
	                    <th>No. Cliente</th>
	                    <th>Nombre</th>
	                    <th>Servicio</th>
	                    <th>Telefono</th>
	                    <th>Fecha Registro</th>
	                    <th>Registró</th>
	                  </tr>
	                </thead>
	                <tbody>
	                <?php
	                #RECORREMOS UNA POR UNA LAS INSTALACIONES
	                while ($Instalacion = mysqli_fetch_array($instalaciones)) {
	                  ?>
	                  <tr>
	                    <td><?php echo $Instalacion['id_cliente']; ?></td>
	                    <td><?php echo $Instalacion['nombre']; ?></td>
	                    <td><?php echo $Instalacion['servicio']; ?></td>
	                    <td><?php echo $Instalacion['telefono']; ?></td>
	                    <td><?php echo $Instalacion['fecha_registro']; ?></td>
	                    <td><?php echo $Instalacion['registro']; ?></td>
	                  </tr>
	                <?php } //FIN WHILE ?>
	                </tbody>
	              </table>
	            <?php
	            }//FIN IF INSTALACIONES
	            #VERIFICAMOS SI HAY REPORTES EN CAMPO PENDIENTES
	            if ($SiRep > 0) {
	            	#SI SE ENCUENTRAN REPORTES EN CAMPO PENDIENTES MOSTRAR TITULO Y CREAR TABLA CON LOS REPORTES
	                echo '<h6><b class = "indigo-text">Reportes Pendientes</b></h6>';
	                ?>
	                <table class="border highlight responsive-table">
	                  <thead>
	                    <tr>
	                      <th>No.Rep</th>
	                      <th>Cliente</th>
	                      <th>Descripcion</th>
	                      <th>Registró</th>
	                      <th>Fecha</th>
	                      <th>Diagnostico</th>
	                      <th>Revisó</th>
	                    </tr>
	                  </thead>
	                  <tbody>
		              <?php 
		              #RECORREMOS LOS REPORTES UNO POR UNO 
		              while ($Reporte = mysqli_fetch_array($reportes)) { 
		                  $id_user=$Reporte['registro'];
		                  if ($id_user == 0) {
		                    $Usuario = "Sistema";
		                  }else{
		                    $users =  mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM users WHERE user_id=$id_user"));
		                    $Usuario = $users['firstname'];
		                  } 
		                  $id_cliente = $Reporte['id_cliente'];    
		                  $cliente = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM clientes WHERE id_cliente=$id_cliente"));
		                  if ($Reporte['tecnico'] =='') {
		                    $tecnico[0] = 'Sin tecnico';
		                  }else{
		                    $id_tecnico = $Reporte['tecnico'];
		                    $tecnico = mysqli_fetch_array(mysqli_query($conn, "SELECT firstname FROM users WHERE user_id = $id_tecnico"));
		                  }
		                  ?>
		                  <tr>
		                    <td><?php echo $Reporte['id_reporte']; ?></td>
		                    <td><?php echo $cliente['nombre']; ?></td>
		                    <td><?php echo $Reporte['descripcion']; ?></td>
		                    <td><?php echo $Usuario; ?></td>
		                    <td><?php echo $Reporte['fecha']; ?></td>
		                    <td><?php echo $Reporte['falla']; ?></td>
		                    <td><?php echo $tecnico[0]; ?></td>
		                  </tr>
		              <?php }//FIN WHILE ?>
	                  </tbody>
	                </table>
	                <?php
	            }//FIN DE IF REPORTES
	            if ($SiOrdenes > 0) {
	                echo '<h6><b class = "indigo-text">Ordenes de Servicio Pendientes</b></h6>';
	                ?>
				    <table class="bordered  highlight responsive-table">
			          <thead>
			            <tr>
			                <th>#Orden</th>
			                <th>Cliente</th>
			                <th>Descripción</th>
			                <th>Fecha</th>
			                <th>Registró</th>
			                <th>Estatus</th>
			                <th>+Ruta</th>
			            </tr>
			          </thead>
			          <tbody>
			          <?php
			            while($resultados = mysqli_fetch_array($Ordenes)) {
			              $id_cliente = $resultados['id_cliente'];
			              $id_user=$resultados['registro'];

			              $users = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM users WHERE user_id=$id_user"));
			              $cliente = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM especiales WHERE id_cliente=$id_cliente"));
			              $color_e = 'blue darken-3';
			              if($resultados['estatus'] == 'Realizar') {
			                $color_e = 'green darken-3';
			              }
			              ?>
			                  <tr>
			                    <td><b><?php echo $resultados['id'] ?></b></td>
			                    <td><?php echo $cliente['nombre']; ?></td>
			                    <td><?php echo ($resultados['estatus'] == 'Revisar') ? $resultados['solicitud']:$resultados['trabajo']; ?></td>
			                    <td><?php echo $resultados['fecha']; ?></td>
			                    <td><?php echo $users['firstname']; ?></td>
			                    <td><span class="new badge <?php echo $color_e; ?>" data-badge-caption=""><b><?php echo $resultados['estatus']; ?></b></span></td>
			                    <td><a onclick="ruta( <?php echo $resultados['id']; ?>);" class="btn btn-floating pink waves-effect waves-light"><i class="material-icons">add</i></a></td>
			                  </tr>
			                <?php
			            }//FIN WHILE
			          ?>
			          </tbody>
			        </table>
	                <?php
	            }//FIN IF ORDENES
	            #VERIFICAMOS SI HAY MANTENIMIENTOS PENDIENTES
              	if ($SiMant > 0) {
                 	echo '<h6><b class = "indigo-text">Mantenimiento</b></h6>';
	                ?>
	                <table class="border highlight responsive-table">
	                  <thead>
	                    <tr>
	                      <th>No.Rep</th>
	                      <th>Cliente</th>
	                      <th>Descripcion</th>
	                      <th>Fecha</th>
	                      <th>Técnico</th>
	                      <th>Registró</th>
			              <th>+Ruta</th>
	                    </tr>
	                  </thead>
	                  <tbody>
	                  <?php   
	                  while ($Reporte = mysqli_fetch_array($Mantenimiento)) { 
	                    $id_user=$Reporte['registro'];
	                    if ($id_user == 0) {
	                      $Usuario = "Sistema";
	                    }else{
	                      $users =  mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM users WHERE user_id=$id_user"));
	                      $Usuario = $users['firstname'];
	                    } 
	                    $id_cliente = $Reporte['id_cliente'];         
	                    $sql = mysqli_query($conn, "SELECT * FROM clientes WHERE id_cliente=$id_cliente");
	                    $filas = mysqli_num_rows($sql);
	                    if ($filas == 0) {
	                      $sql = mysqli_query($conn, "SELECT * FROM especiales WHERE id_cliente=$id_cliente");
	                    }
	                    $cliente = mysqli_fetch_array($sql);
	                    if ($Reporte['tecnico'] =='') {
	                      $tecnico1[0] = '';
	                      $tecnico1[1] = 'Sin tecnico';
	                    }else{
	                      $id_tecnico = $Reporte['tecnico'];
	                      $tecnico1 = mysqli_fetch_array(mysqli_query($conn, "SELECT user_id, user_name FROM users WHERE user_id = $id_tecnico"));
	                    }
	                    ?>
	                    <tr>
	                      <td><?php echo $Reporte['id_reporte']; ?></td>
	                      <td><?php echo $cliente['nombre']; ?></td>
	                      <td><?php echo $Reporte['descripcion']; ?></td>
	                      <td><?php echo $Reporte['fecha']; ?></td>
	                      <td><?php echo $tecnico1[1]; ?></td>
	                      <td><?php echo $Usuario; ?></td>
	                      <td><a onclick="ruta(<?php echo $Reporte['id_reporte']; ?>);" class="btn btn-floating pink waves-effect waves-light"><i class="material-icons">add</i></a></td>
	                    </tr>
	                  <?php
	                    }
	                  ?>
	                  </tbody>
              		</table>
              	<?php } ?>
            </div>
          </li>
        <?php
        }
      }

      echo '<br><br>';   
    echo '</ul>'; // FIN DEL UL CIERRE DE CONTENIDO DE LAS COMUNIDADES
} //Fin else $filas

mysqli_close($conn);
?>
<script>
	document.addEventListener('DOMContentLoaded', function() {
    var elems = document.querySelectorAll('.collapsible');
    var instances = M.Collapsible.init(elems, options);
  });

  // Or with jQuery

  $(document).ready(function(){
    $('.collapsible').collapsible();
  });
</script>