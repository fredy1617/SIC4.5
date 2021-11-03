<?php
#INCLUIMOS EL PHP DONDE VIENE LA INFORMACION DEL INICIO DE SESSION
include('../php/is_logged.php');
#INCLUIMOS EL ARCHIVO CON LOS DATOS Y CONEXXION A LA BASE DE DATOS
include('../php/conexion.php');
#GENERAMOS UNA FECHA DEL DIA EN CURSO REFERENTE A LA ZONA HORARIA
#TOMAMOS EL ID DEL USUARIO CON LA SESSION INICIADA
$id = $_SESSION['user_id'];
#TOMAMOS LA INFORMACION DEL USUARIO (PARA SABER A QUE AREA PERTENECE)
$area = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM users WHERE user_id=$id"));
$Hoy = date('Y-m-d');
$instalaciones = mysqli_fetch_array(mysqli_query($conn,"SELECT count(*) FROM clientes WHERE instalacion IS NULL"));
$reportes = mysqli_fetch_array(mysqli_query($conn,"SELECT count(*) FROM reportes WHERE ((fecha_visita = '$Hoy'  AND atender_visita = 0) OR (fecha_visita < '$Hoy' AND atender_visita = 0 AND visita = 1) OR atendido != 1 OR atendido IS NULL) AND id_cliente < 10000"));
$reportesEsp = mysqli_fetch_array(mysqli_query($conn,"SELECT count(*) FROM reportes WHERE ((fecha_visita = '$Hoy'  AND atender_visita = 0) OR (fecha_visita < '$Hoy' AND atender_visita = 0 AND visita = 1) OR atendido != 1 OR atendido IS NULL) AND id_cliente > 10000 AND descripcion LIKE 'Reporte Especial:%'"));
$Ordenes_Redes = mysqli_fetch_array(mysqli_query($conn,"SELECT count(*) FROM orden_servicios  WHERE  estatus IN ('PorConfirmar', 'Revisar', 'Ejecutar', 'Cotizar', 'Cotizado', 'Pedir', 'Autorizado')  AND dpto = 1"));
$Ordenes_Taller = mysqli_fetch_array(mysqli_query($conn,"SELECT count(*) FROM orden_servicios  WHERE  estatus IN ('PorConfirmar', 'Revisar', 'Ejecutar', 'Cotizar', 'Cotizado', 'Pedir', 'Autorizado')  AND dpto = 2"));
$Ordenes_Ventas = mysqli_fetch_array(mysqli_query($conn,"SELECT count(*) FROM orden_servicios  WHERE  estatus IN ('PorConfirmar', 'Revisar', 'Ejecutar', 'Cotizar', 'Cotizado', 'Pedir', 'Autorizado') AND dpto = 3"));
if ($area['area'] == 'Taller') { $Orden = $Ordenes_Taller['count(*)']; }elseif ($id == 49 OR $id == 10 OR $id == 56) { $Orden = $Ordenes_Taller['count(*)']+$Ordenes_Ventas['count(*)']+$Ordenes_Redes['count(*)']; }elseif ( $area['area'] == 'Redes' OR $id == 25 OR $id == 28) {  $Orden = $Ordenes_Redes['count(*)'];  }else{ $Orden = $Ordenes_Ventas['count(*)']; }
$Mantenimiento = mysqli_fetch_array(mysqli_query($conn,"SELECT count(*) FROM reportes WHERE ((fecha_visita = '$Hoy'  AND atender_visita = 0) OR (fecha_visita < '$Hoy' AND atender_visita = 0 AND visita = 1) OR atendido != 1 OR atendido IS NULL) AND id_cliente > 10000 AND descripcion LIKE 'Mantenimiento:%'"));
$tel = mysqli_fetch_array(mysqli_query($conn,"SELECT count(*) FROM pagos WHERE Cotejado =1"));
$pendientes = mysqli_fetch_array(mysqli_query($conn,"SELECT count(*)FROM dispositivos WHERE estatus IN ('Cotizado','En Proceso','Pendiente') AND fecha > '2019-01-01'"));
$listos = mysqli_fetch_array(mysqli_query($conn,"SELECT count(*)FROM dispositivos WHERE estatus IN ('Listo (En Taller)','Listo (No Reparado)', 'Listo') AND fecha > '2019-01-01'"));
$almacen = mysqli_fetch_array(mysqli_query($conn,"SELECT count(*)FROM dispositivos WHERE estatus = 'Almacen'"));
$rutas = mysqli_fetch_array(mysqli_query($conn,"SELECT count(*)FROM rutas WHERE estatus = 0"));
?>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<!--Import material-icons.css-->
      <link href="css/material-icons.css" rel="stylesheet">
      <!--Import materialize.css-->
      <link type="text/css" rel="stylesheet" href="css/materialize.min.css"  media="screen,projection"/>
      <!--Let browser know website is optimized for mobile-->
      <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
	  <link rel="shortcut icon" href="../img/logo.jpg" type="image/jpg" />
      <style rel="stylesheet">
		.dropdown-content{  overflow: visible;	}
	  </style>
	<div class="navbar-fixed">
	<nav class="indigo darken-4">
		<div class="nav-wrapper container">
			<a  class="brand-logo"><img  class="responsive-img" style="width: 60px; height: 56px;" src="../img/logo.jpg"></a>
			<a href="#" data-target="menu-responsive" class="sidenav-trigger">
				<i class="material-icons">menu</i>
			</a>
			<ul class="right hide-on-med-and-down">
				<li><a class='dropdown-button' data-target='dropdown1'><i class="material-icons left">phonelink_setup</i>Servicio Técnico <i class="material-icons right">arrow_drop_down</i></a></li>
				<ul id='dropdown1' class='dropdown-content'>
				    <li><a href="form_entradas.php" class="black-text"><i class="material-icons">add</i>Nuevo Dispositivo </a></li>
				    <li><a href="dispositivos.php" class="black-text"><i class="material-icons">phonelink</i>Disp. En General </a></li>
				    <li><a href="ver_almacen.php" class="black-text"><i class="material-icons">dashboard</i>Almacen <span class="new badge pink" data-badge-caption=""><?php echo $almacen['count(*)'];?></span> </a></li>
				    <li><a href="listos.php" class="black-text"><i class="material-icons">assignment_turned_in</i>Listos <span class="new badge pink" data-badge-caption=""><?php echo $listos['count(*)'];?></span> </a></li>
				    <li><a href="pendientes.php" class="black-text"><i class="material-icons">assignment_late</i>Pendientes <span class="new badge pink" data-badge-caption=""><?php echo $pendientes['count(*)'];?></span> </a></li>    
				    <li><a href="reporte_pagos_ST.php" class="black-text"><i class="material-icons">list</i>Reporte Pagos</a></li>
				    <li><a href="rep_refacciones.php" class="black-text"><i class="material-icons">list</i>Rep. Refacciones</a></li>    			 
				    <li><a href="bitacora_taller.php" class="black-text"><i class="material-icons">list</i>Trabajo Taller</a></li>    			 
				    <li><a href="vehiculos.php" class="black-text"><i class="material-icons">directions_car</i>Vehiculos</a></li>    			 
 				 </ul>
				<li><a class='dropdown-button' data-target='dropdown2'><i class="material-icons left">language</i>Redes<span class=" new badge pink" data-badge-caption=""><?php echo $instalaciones['count(*)']+$reportes['count(*)']+$reportesEsp['count(*)']+$Orden+$Mantenimiento['count(*)'];?></span><i class="material-icons right">arrow_drop_down</i></a></li>
				<ul id='dropdown2' class='dropdown-content'>
				    <li><a href="../views/form_instalacion.php" class="black-text"><i class="material-icons">add</i>Nueva Instalación</a></li>    
					<li><a href="form_mantenimiento.php" class="black-text"><i class="material-icons">add_circle_outline</i>Nuevo Mantimiento</a></li>
					<li><a href="form_orden.php" class="black-text"><i class="material-icons">add_circle</i>Nueva Orden</a></li>
					 <li><a href="clientes.php" class="black-text"><i class="material-icons">people</i>Clientes </a></li>
				    <li><a href="../views/instalaciones.php" class="black-text"><i class="material-icons">list</i>Instalaciones <span class=" new badge pink" data-badge-caption=""><?php echo $instalaciones['count(*)']?></span></a></li>
				    <li><a href="stock.php" class="black-text"> <i class="material-icons">assignment_ind</i>Stock </a></li>
				    <li><a href="reportes.php" class="black-text"><i class="material-icons">perm_scan_wifi</i>Reportes <span class="new badge pink" data-badge-caption=""><?php echo $reportes['count(*)'];?></span></a></li>			    
				    <li><a href="ordenes_servicio.php" class="black-text"><i class="material-icons">assignment</i>Orden Servicio<span class="new badge pink" data-badge-caption=""><?php echo $Orden; ?></span></a></li>			    
				    <li><a href="mantenimiento.php" class="black-text"><i class="material-icons">build</i>Mantenimiento <span class="new badge pink" data-badge-caption=""><?php echo $Mantenimiento['count(*)'];?></span></a></li>			    
				    <li><a href="tel.php" class="black-text"><i class="material-icons">phone</i>Teléfono <span class=" new badge pink" data-badge-caption=""><?php echo $tel['count(*)'];?></span></a></li>
				    <li><a href="menu_rutas.php" class="black-text"><i class="material-icons">near_me</i>Rutas<span class="new badge pink" data-badge-caption=""><?php echo $rutas['count(*)'];?></span></a></li>
				    <li><a class='dropdown-btn1 black-text' data-target='sub-dropdown4'><i class="material-icons left">assignment_ind</i> Facturacion <i class="material-icons right">chevron_right</i></a></li>
				    <ul id='sub-dropdown4' class='dropdown-content'>
				    	<li><a href="facturar_p.php" class="black-text"><i class="material-icons">assignment_late</i>Pendientes </a></li>   
				    	<li><a href="facturar_l.php" class="black-text"><i class="material-icons">assignment_turned_in</i>Listas </a></li>
				    </ul>	    
 				 </ul>
 				<li><a class='dropdown-button' data-target='dropdown5'><i class="material-icons left">add</i>Mas <i class="material-icons right">arrow_drop_down</i></a></li>
				<ul id='dropdown5' class='dropdown-content'>
					<li><a href="centrales_pings.php" class="black-text"><i class="material-icons">settings_input_antenna</i>Centrales Pings </a></li>   
				    <li><a href="paquetes.php" class="black-text"><i class="material-icons">import_export</i>Paquetes </a></li>   
				    <li><a href="comunidades.php" class="black-text"><i class="material-icons">business</i>Comunidades </a></li>
				    <li><a href="servidores.php" class="black-text"><i class="material-icons">router</i>Servidores </a></li>
				    <li><a href="centrales.php" class="black-text"><i class="material-icons">satellite</i>Centrales </a></li>
				    <li><a href="fichas.php" class="black-text"><i class="material-icons">tap_and_play</i>Fichas </a></li>
				    <li><a href="perfiles.php" class="black-text"><i class="material-icons">note</i>Perfiles </a></li>
				    <li><a href="pedidos.php" class="black-text"><i class="material-icons">assignment</i>Pedidos </a></li>
				    <li><a href="sistema_mikrotik.php" class="black-text"><i class="material-icons">router</i>Mikrotik </a></li>
				    <li><a href="form_actividad.php" class="black-text"><i class="material-icons">assignment_turned_in</i>Actividad </a></li>
				    <li><a href="../views/inventario.php" class="black-text"><i class="material-icons">list</i>Inventario </a></li>
				</ul>
				<li><a class='dropdown-button' data-target='dropdown3'><i class="material-icons left">account_circle</i>Admin <i class="material-icons right">arrow_drop_down</i></a></li>
				<ul id='dropdown3' class='dropdown-content'>
				    <li><a href="admin_clientes.php" class="black-text"> <i class="material-icons">search</i>Clientes </a></li>
				    <li><a class='dropdown-btn black-text' data-target='sub-dropdown'><i class="material-icons left">assignment</i>Reportes <i class="material-icons right">chevron_right</i></a></li>
				    <ul id='sub-dropdown' class='dropdown-content'>
				    	<li><a href="rep_pagos.php" class="black-text"><i class="material-icons">report</i>Reporte Pagos </a></li>
				    	<li><a href="reportes_atendidos.php" class="black-text"><i class="material-icons">done</i>Reportes Atendidos </a></li>
				    	<li><a href="rep_instalaciones.php" class="black-text"><i class="material-icons">format_list_numbered</i>Rep. Instalaciones</a></li>
				    	<li><a href="estadistica.php" class="black-text"><i class="material-icons">equalizer</i>Estadistica</a></li>
				    	<li><a href="reporte_deudas.php" class="black-text"><i class="material-icons">list</i>Reporte Deudas</a></li>    	
				    	<li><a href="reporte_material.php" class="black-text"><i class="material-icons">router</i>Reporte Material</a></li>    	
				    	<li><a href="rep_ruta.php" class="black-text"><i class="material-icons">location_on</i>Reporte Rutas</a></li>
				    	<li><a href="reporte_extras.php" class="black-text"><i class="material-icons">add_alarm</i>Reporte Extras</a></li>
				    	<li><a href="canceladas.php" class="black-text"><i class="material-icons">do_not_disturb</i>Canceladas</a></li>    	
				    	<li><a href="reporte_pedidos_entregados.php" class="black-text"><i class="material-icons">list</i>Pedidos Entregados</a></li>    	
				    </ul> 
				    <li><a href="cortes_parciales.php" class="black-text"><i class="material-icons">attach_money</i>Corte Parcial </a></li>		  
				    <li><a href="cortes_pagos.php" class="black-text"><i class="material-icons">local_atm</i>Cortes Pagos </a></li>		  
				    <li><a href="Estatus_contratos.php" class="black-text"><i class="material-icons">assignment</i>Contratos Vencidos</a></li>
				    <li><a href="usuarios.php" class="black-text"><i class="material-icons">people</i>Usuarios </a></li>
				    <li><a class='dropdown-btn black-text' data-target='sub-dropdown2'><i class="material-icons left">content_cut</i> CORTES <i class="material-icons right">chevron_right</i></a></li>
				    <ul id='sub-dropdown2' class='dropdown-content'>
				    	<li><a href="total_cortes.php" class="black-text"><i class="material-icons">money_off</i>Total Cortes</a></li>
				    	<li><a href="historial_cortes.php" class="black-text"><i class="material-icons">content_cut</i>Historial de Cortes </a></li>
				    	<li><a href="list_cortes_sin_confirmar.php" class="black-text"><i class="material-icons">done</i>Sin Confirmar </a></li>
				    	<li><a href="cobradores_list.php" class="black-text"><i class="material-icons">attach_money</i>Saldo Cobradores </a></li>  	
				    </ul>
				    <li><a href="reporte_x_fecha.php" class="black-text"><i class="material-icons">assignment_turned_in</i>Trabajos Realizados</a></li>
				    <li><a class='dropdown-btn black-text' data-target='sub-dropdown3'><i class="material-icons left">monetization_on</i>CAJA<i class="material-icons right">chevron_right</i></a></li>
				    <ul id='sub-dropdown3' class='dropdown-content'>
				   		<li><a href="en_cajas.php" class="black-text"><i class="material-icons">archive</i>En Cajas</a></li> 	
				   		<li><a href="caja_chica.php" class="black-text"><i class="material-icons">attach_money</i>Caja Chica</a></li> 	
				    </ul>
				    <li><a href="calendario.php" class="black-text"><i class="material-icons">date_range</i>Calendario</a></li>
				    <li><a href="CORTES_FULL.php" class="black-text"><i class="material-icons left">signal_wifi_off</i>Cortes Full</a></li>	
				    <li><a href="activos.php" class="black-text"><i class="material-icons left">import_export</i>Activos</a></li>	
 				 </ul>
 				 <li><a class='dropdown-button' data-target='dropdown4'><?php echo $_SESSION['user_name'];?> <i class="material-icons right">arrow_drop_down</i></a></li>
				<ul id='dropdown4' class='dropdown-content'>
				    <li><a href="../php/cerrar_sesion.php" class="black-text"><i class="material-icons">exit_to_app</i>Cerrar Sesión</a></li>
 				 </ul>
			</ul>
			<ul class="right hide-on-large-only hide-on-small-only">
				<li><a class='dropdown-button' data-target='dropdown10'><?php echo $_SESSION['user_name'];?> <i class="material-icons right">arrow_drop_down</i></a></li>
				<ul id='dropdown10' class='dropdown-content'>
				    <li><a href="../php/cerrar_sesion.php" class="black-text"><i class="material-icons">exit_to_app</i>Cerrar Sesión</a></li>
 				 </ul>
			</ul>
			<ul class="right hide-on-med-and-up">
		        <li><a class='dropdown-button' data-target='dropdown8'><i class="material-icons left">account_circle</i><b>></b></a></li>
				<ul id='dropdown8' class='dropdown-content'>
				   <li><a href="../php/cerrar_sesion.php" class="black-text"><i class="material-icons">exit_to_app</i>Cerrar Sesión</a></li>
 				</ul>
		    </ul>			
		</div>		
	</nav>
	</div>
	<ul class="sidenav indigo lighten-5" id="menu-responsive" style="width: 270px;">
				<h2>Menú</h2>
    			<li><div class="divider"></div></li><br>
				<li>
	    			<ul class="collapsible collapsible-accordion">
	    				<li>
	    				  <div class="collapsible-header"><i class="material-icons">phonelink_setup</i>Servicio Técnico <i class="material-icons right">arrow_drop_down</i></div>
		      				<div class="collapsible-body  indigo lighten-5">
		      				  <span>
		      					<ul>
		      					  <li><a href="form_entrada.php"><i class="material-icons">add</i>Nuevo Dispositivo</a></li>
			      				  <li><a href="dispositivos.php"><i class="material-icons">phonelink</i>Disp. En General</a></li>
				    			  <li><a href="ver_almacen.php"><i class="material-icons">dashboard</i>Almacen <span class="new badge pink" data-badge-caption=""><?php echo $almacen['count(*)'];?></span> </a></li>
			      				  <li><a href="listos.php"><i class="material-icons">assignment_turned_in</i>Listos <span class="new badge pink" data-badge-caption=""><?php echo $listos['count(*)'];?></span> </a></li>
						    	  <li><a href="pendientes.php"><i class="material-icons">assignment_late</i>Pendientes<span class="new badge pink" data-badge-caption=""><?php echo $pendientes['count(*)'];?></span></a></li>
						    	  <li><a href="reporte_pagos_ST.php"><i class="material-icons">list</i>Reporte Pagos</a></li>
						    	  <li><a href="rep_refacciones.php"><i class="material-icons">list</i>Reporte Refacciones</a></li>
				    			  <li><a href="bitacora_taller.php"><i class="material-icons">list</i>Trabajo Taller</a></li>  			 
				    			  <li><a href="vehiculos.php"><i class="material-icons">directions_car</i>Vehiculos</a></li>    			 
					    		</ul>
					          </span>
		      			  </div>    			
	    				</li>	    			
	    			</ul>	     				
	    		</li>
				<li>
	    			<ul class="collapsible collapsible-accordion">
	    				<li>
	    				  <div class="collapsible-header"><i class="material-icons">language</i>Redes <i class="material-icons right">arrow_drop_down</i></div>
		      				<div class="collapsible-body  indigo lighten-5">
		      				  <span>
		      					<ul>
		      					  <li><a href="../views/form_instalacion.php"><i class="material-icons">add</i>Nueva Instalación</a></li>
					 			  <li><a href="form_mantenimiento.php"><i class="material-icons">add_circle_outline</i>Nuevo Mantenimiento</a></li>
								  <li><a href="form_orden.php"><i class="material-icons">add_circle</i>Nueva Orden</a></li>
					 			  <li><a href="clientes.php"><i class="material-icons">people</i>Clientes </a></li>
				    			  <li><a href="stock.php" class="black-text"> <i class="material-icons">assignment_ind</i>Stock </a></li>
			      				  <li><a href="../views/instalaciones.php"><i class="material-icons">list</i>Instalaciones <span class="new badge pink" data-badge-caption=""><?php echo $instalaciones['count(*)'];?></span></a></li>
						    	  <li><a href="reportes.php"><i class="material-icons">perm_scan_wifi</i>Reportes <span class=" new badge pink" data-badge-caption=""><?php echo $reportes['count(*)'];?></span></a></li>
						    	  <li><a href="ordenes_servicio.php"><i class="material-icons">assignment</i>Orden Servicio<span class=" new badge pink" data-badge-caption=""><?php echo $Orden; ?></span></a></li>
						    	  <li><a href="mantenimiento.php"><i class="material-icons">build</i>Mantenimiento<span class="new badge pink" data-badge-caption=""><?php echo $Mantenimiento['count(*)'];?></span></a></li>
						    	  <li><a href="tel.php"><i class="material-icons">phone</i>Teléfono <span class=" new badge pink" data-badge-caption=""><?php echo $tel['count(*)'];?></span></a></li>
						    	  <li><a href="menu_rutas.php"><i class="material-icons">near_me</i>Rutas <span class=" new badge pink" data-badge-caption=""><?php echo $rutas['count(*)'];?></span></a></li>
				    			  <li><a href="form_actividad.php"><i class="material-icons">assignment_turned_in</i>Actividad </a></li>
				    			  <li><a href="../views/inventario.php"><i class="material-icons">list</i>Inventario </a></li>
						    	  <li><a href="facturar_p.php"><i class="material-icons">assignment_late</i>Fac. Pendientes </a></li>   
				    			  <li><a href="facturar_l.php"><i class="material-icons">assignment_turned_in</i>Fac. Listas </a></li>
						    	  <li><a href="paquetes.php"><i class="material-icons">import_export</i>Paquetes</a></li>
						    	  <li><a href="comunidades.php"><i class="material-icons">business</i>Comunidades</a></li>
						    	  <li><a href="servidores.php"><i class="material-icons">router</i>Servidores</a></li>
				    			  <li><a href="centrales.php"><i class="material-icons">satellite</i>Centrales </a></li>
				    			  <li><a href="fichas.php"><i class="material-icons">tap_and_play</i>Fichas </a></li>
				    			  <li><a href="perfiles.php"><i class="material-icons">note</i>Perfiles </a></li>
				    			  <li><a href="pedidos.php"><i class="material-icons">assignment</i>Pedidos </a></li>
				    			  <li><a href="sistema_mikrotik.php"><i class="material-icons">router</i>Mikrotik </a></li>
					    		</ul>
					          </span>
		      			  </div>    			
	    				</li>	    			
	    			</ul>	     				
	    		</li>
				<li>
					<ul class="collapsible collapsible-accordion">
	    				<li>
	    				  <div class="collapsible-header"><i class="material-icons">account_circle</i>Admin<i class="material-icons right">arrow_drop_down</i></div>
		      				<div class="collapsible-body  indigo lighten-5">
		      				  <span>
		      					<ul>
								    <li><a href="admin_clientes.php"><i class="material-icons">search</i>Clientes </a></li>
								    <li><a href="Estatus_contratos.php"><i class="material-icons">assignment</i>Contratos Vencidos</a></li>
								    <li><a href="cortes_pagos.php"><i class="material-icons">attach_money</i>Cortes Pagos </a></li>
				    				<li><a href="cortes_parciales.php"><i class="material-icons">attach_money</i>Corte Parcial </a></li>
								    <li><a href="usuarios.php"><i class="material-icons">people</i>Usuarios </a></li>
								    <li><a href="rep_pagos.php"><i class="material-icons">report</i>Reporrte Pagos </a></li>
								    <li><a href="total_cortes.php"><i class="material-icons">money_off</i>Total Cortes</a></li>
								    <li><a href="historial_cortes.php"><i class="material-icons">content_cut</i>Historial Cortes </a>
				    				<li><a href="list_cortes_sin_confirmar.php"><i class="material-icons">done</i>Sin Confirmar </a></li>
				    				<li><a href="cobradores_list.php"><i class="material-icons">attach_money</i>Saldo Cobradores </a></li>  	
								    <li><a href="rep_instalaciones.php"><i class="material-icons">format_list_numbered</i>Reporte Instalaciones</a></li>
				    				<li><a href="estadistica.php"><i class="material-icons">equalizer</i>Estadistica</a></li>
								    <li><a href="reportes_atendidos.php"><i class="material-icons">done</i>Reportes Atendidos</a></li>
				    				<li><a href="reporte_material.php"><i class="material-icons">router</i>Reporte Material</a></li>  	
				    				<li><a href="rep_ruta.php"><i class="material-icons">location_on</i>Reporte Rutas</a></li>    	
				    				<li><a href="reporte_extras.php"><i class="material-icons">add_alarm</i>Reporte Extras</a></li>
				    				<li><a href="canceladas.php"><i class="material-icons">do_not_disturb</i>Canceladas</a></li>
				    				<li><a href="reporte_pedidos_entregados.php"><i class="material-icons">list</i>Pedidos Entregados</a></li>   
								    <li><a href="reporte_x_fecha.php"><i class="material-icons">assignment_turned_in</i>Trabajo Realizado</a></li>
				    				<li><a href="calendario.php"><i class="material-icons">date_range</i>Calendario</a></li>
								    <li><a href="en_cajas.php"><i class="material-icons">archive</i>En Cajas</a></li>
								    <li><a href="caja_chica.php"><i class="material-icons">attach_money</i>Caja Chica</a></li>
								    <li><a href="reporte_deudas.php"><i class="material-icons">list</i>Reporte Deudas</a></li>
						    	 	<li><a href="CORTES_FULL.php"><i class="material-icons">signal_wifi_off</i>Cortes Full</a></li>
						    	 	<li><a href="Activos.php"><i class="material-icons">import_export</i>Activos</a></li>
				 				</ul>
					          </span>
		      			  </div>    			
	    				</li>	    			
	    			</ul>	     				
	    		</li>
	</ul>
	<?php 
	include('../views/modals.php');
	include('../php/scripts.php');
	?>
	<script src="js/jquery-3.1.1.js"></script>
	<!--JavaScript at end of body for optimized loading-->
    <script type="text/javascript" src="js/materialize.min.js"></script>
	<script>
    	$(document).ready(function() {	    
	 	$('.dropdown-button').dropdown({
	      	  inDuration: 500,
	          outDuration: 500, 
	          constrainWidth: false, // Does not change width of dropdown to that of the activator
	          coverTrigger: false, 
	    });
	    $('.dropdown-btn').dropdown({
	      	  inDuration: 500,
	          outDuration: 500,
	          hover: true,
	          constrainWidth: true, // Does not change width of dropdown to that of the activator
	          coverTrigger: false, 
	    });
	    $('.dropdown-btn1').dropdown({
	      	  inDuration: 500,
	          outDuration: 500,
	          alignment: 'left',
	          hover: true,
	          constrainWidth: true, // Does not change width of dropdown to that of the activator
	          coverTrigger: false, 
	    });
	    $('tooltipped').tooltip();
	    });
		document.addEventListener('DOMContentLoaded', function(){
			M.AutoInit();
		});
		document.addEventListener('DOMContentLoaded', function() {
		    var elems = document.querySelectorAll('.fixed-action-btn');
		    var instances = M.FloatingActionButton.init(elems, {
		      direction: 'left'
		    });
		});
	</script>