<!DOCTYPE html>
<html>
<head>
	<title>SIC | Ruta</title>

<?php
include('fredyNav.php');
include('../php/conexion.php');
$comunidades = mysqli_query($conn, 'SELECT * FROM comunidades');
date_default_timezone_set('America/Mexico_City');
$Hoy = date('Y-m-d');
?>
<script>
    function borrar_inst(IdCliente){
        $.post("../php/borrar_inst.php", {
            tipo : "comunidad",
            valorIdCliente : IdCliente,
        }, function(mensaje){
            $("#borrar_inst").html(mensaje);
        });
    };
    function borrar_rep(IdReporte){
        $.post("../php/borrar_rep.php",{
            tipo : "comunidad",
            valorIdReporte : IdReporte,
        }, function(mensaje){
            $("#reporte_borrar").html(mensaje);
        });
    };
    function add_ruta(id_comunidad) {
        M.toast({html:"Insertando comunidada a ruta...", classes: "rounded"});
        $.post("../php/add_rutaCom.php", {valorIdComunidad: id_comunidad}, function(mensaje){
            $("#Res_add").html(mensaje);
        });
    };
    function modal(){
       $(document).ready(function(){
          $('#rutamodal').modal();
          $('#rutamodal').modal('open'); 
       });
     };
</script>
</head>
<body>
	<div class="container">
        <div id="Res_add"></div>
	   <div class="row" >
            <h3 class="hide-on-med-and-down">Ruta por Comunidad</h3>
            <h5 class="hide-on-large-only">Ruta por Comunidad</h5>
        </div>
        <!-- MUESTRA TODO LO QUE HAY POR CADA COMUNIDAD--->
        <ul class="collapsible">
        	<?php
        	while ($comunidad = mysqli_fetch_array($comunidades)) {
        		$nombre = $comunidad['nombre'];
        		$id_comunidad = $comunidad['id_comunidad'];
        		
        		$reportes = mysqli_query($conn, "SELECT * FROM clientes INNER JOIN reportes ON clientes.id_cliente = reportes.id_cliente WHERE ((reportes.fecha_visita = '$Hoy'  AND reportes.atender_visita = 0) OR (reportes.fecha_visita < '$Hoy' AND reportes.atender_visita = 0 AND reportes.visita = 1) OR reportes.atendido != 1 OR reportes.atendido IS NULL) AND clientes.lugar = '$id_comunidad'  ORDER BY reportes.fecha");

                $reportesEsp = mysqli_query($conn, "SELECT * FROM especiales INNER JOIN reportes ON especiales.id_cliente = reportes.id_cliente WHERE ((reportes.fecha_visita = '$Hoy'  AND reportes.atender_visita = 0) OR (reportes.fecha_visita < '$Hoy' AND reportes.atender_visita = 0 AND reportes.visita = 1) OR reportes.atendido != 1 OR reportes.atendido IS NULL) AND especiales.lugar = '$id_comunidad'");
        		$instalaciones = mysqli_query($conn, "SELECT * FROM clientes WHERE instalacion IS NULL AND lugar = '$id_comunidad' ORDER BY id_cliente ASC");

                $SiRep = mysqli_num_rows($reportes);
        		$SiInst = mysqli_num_rows($instalaciones);
                $SiEsp = mysqli_num_rows($reportesEsp);

        		if ($SiRep > 0 OR $SiInst > 0 OR $SiEsp) {
                    #INICIA LI
                    ?>
                <li>
                    <div class="row hide-on-large-only">
                    <!-- Barras desplegables MOVIL--->
                    <div class="collapsible-header col s12 m7 l7">
                       <h6 ><?php echo $nombre; ?><i class="material-icons right">arrow_drop_down</i></h6> 
                    </div>
                    <!-- BOTON MOVIL--->                    
                    <a onclick = "add_ruta(<?php echo $id_comunidad; ?>);" class = "hide-on-large-only btn waves-effect waves-light pink right col s12 m5 l5">+ Ruta</a>
                    </div>

                    <!-- BOTON PC--->
                    <a onclick = "add_ruta(<?php echo $id_comunidad; ?>);" class = "hide-on-med-and-down btn waves-effect waves-light pink right col s12 m5 l5">Agregar A Ruta</a>
                    <!-- Barras desplegables PC--->
                    <div class="collapsible-header col s12 m7 l7 hide-on-med-and-down">
                       <h5><?php echo $nombre; ?><i class="material-icons right">arrow_drop_down</i></h5>  
                    </div>

                    <div class="collapsible-body"> 
                    <?php
        			if ($SiRep > 0) {
        				echo '<h6><b class = "indigo-text">Reportes Pendientes</b></h6>';
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
	        					</tr>
        					</thead>
        					<tbody>
        				<?php	
        				while ($Reporte = mysqli_fetch_array($reportes)) { 
        					$id_user=$Reporte['registro'];
					        if ($id_user == 0) {
					          $Usuario = "Sistema";
					        }else{
					          $users =  mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM users WHERE user_id=$id_user"));
					          $Usuario = $users['firstname'];
					      } 
      					  $id_cliente = $Reporte['id_cliente'];      	
        				  $sql = mysqli_query($conn, "SELECT * FROM clientes WHERE id_cliente=$id_cliente");
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
        					</tr>
        					<?php
        				}
        				?>
        					</tbody>
        				</table>
        				<?php

        			}
                    if ($SiEsp > 0) {
                        echo '<h6><b class = "indigo-text">Reportes Especiales</b></h6>';
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
                                </tr>
                            </thead>
                            <tbody>
                        <?php   
                        while ($Reporte = mysqli_fetch_array($reportesEsp)) { 
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
                            </tr>
                            <?php
                        }
                        ?>
                            </tbody>
                        </table>
                        <?php

                    }
                    if ($SiInst > 0) {
        				echo '<h6><b class = "indigo-text">Instalaciones Pendientes</b></h6>';
                        ?>
                        <table class="border highlight responsive-table">
                            <thead>
                                <tr>
                                    <th>No. Cliente</th>
                                    <th>Nombre</th>
                                    <th>Servicio</th>
                                    <th>Telefono</th>
                                    <th>Registró</th>
                                </tr>
                            </thead>
                            <tbody>
                        <?php

        				while ($Instalacion = mysqli_fetch_array($instalaciones)) {
                            $id_cliente = $Instalacion['id_cliente'];
                            $Servicio = mysqli_fetch_array(mysqli_query($conn, "SELECT servicio FROM clientes WHERE id_cliente = $id_cliente"));	
                            ?>
                            <tr>
                                <td><?php echo $id_cliente; ?></td>
                                <td><?php echo $Instalacion['nombre']; ?></td>
                                <td><?php echo $Servicio['servicio']; ?></td>
                                <td><?php echo $Instalacion['telefono']; ?></td>
                                <td><?php echo $Instalacion['registro']; ?></td>
                            </tr>
                            <?php
        				}
                        ?>
                            </tbody>
                        </table>
                            <?php
        			}
        		#FIN LI
                ?>
                </div>
            </li>
                <?php
                }
        	}
            echo '<br><br>' ;   

        	?>
        </ul>
        <!-- MUESTRA INSTALACIONES DE RUTA--->
        <div class="row">
            <div id="borrar_inst"></div>
          <h5 class="hide-on-large-only">Ruta Instalaciones</h5>
          <h3 class="hide-on-med-and-down">Ruta Instalaciones</h3>
          <table class="bordered highlight responsive-table">
              <thead>
                  <tr>
                      <th>No.Cliente</th>
                      <th>Nombre</th>
                      <th>Servicio</th>
                      <th>Telefono</th>
                      <th>Lugar</th>
                      <th>Direccion</th>
                      <th>Borrar</th>
                  </tr>
              </thead>
              <tbody>
              <?php
              $sql_tmp = mysqli_query($conn, "SELECT * FROM tmp_pendientes WHERE ruta_inst = 0");
              $columnas = mysqli_num_rows($sql_tmp);
              if ($columnas == 0) {
                echo '<h5 class = "center">No hay instalaciones en ruta</h5>';
              }else{
                while ($tmp = mysqli_fetch_array($sql_tmp)) {
                    $id_comunidad = $tmp['lugar'];
                    $sql_comunidad1 = mysqli_fetch_array(mysqli_query($conn, "SELECT nombre FROM comunidades WHERE id_comunidad = $id_comunidad"));
                    $id_cliente = $tmp['id_cliente'];
                    $serv = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM clientes WHERE id_cliente = $id_cliente"));
                ?>
                <tr>
                    <td><?php echo $tmp['id_cliente']; ?></td>
                    <td><?php echo $tmp['nombre']; ?></td>
                    <td><?php echo $serv['servicio']; ?></td>
                    <td><?php echo $tmp['telefono']; ?></td>
                    <td><?php echo $sql_comunidad1['nombre']; ?></td>
                    <td><?php echo $tmp['direccion']; ?></td>
                    <td><a onclick="borrar_inst(<?php echo $tmp['id_cliente']; ?>);" class="btn btn-floating red darken-1 waves-effect waves-light"><i class="material-icons">delete</i></a></td>
                </tr>
                <?php
                }
              }
              ?>
              </tbody>
          </table> 
        </div>
        <!-- MUESTRA REPORTES DE RUTA--->
        <div class="row">
            <div id="reporte_borrar"></div>
          <h3 class="hide-on-med-and-down">Ruta Reportes</h3>
          <h5 class="hide-on-large-only">Ruta Reportes</h5>
          <table>
              <thead>
                  <tr>
                      <th>No. Reporte</th>
                      <th>Cliente</th>
                      <th>Comunidad</th>
                      <th>Descripción</th>
                      <th>Fecha</th>
                      <th>Borrar</th>
                  </tr>
              </thead>
              <tbody>
              <?php
              $sql_tmp = mysqli_query($conn, "SELECT * FROM tmp_reportes WHERE ruta = 0");
              $columnas = mysqli_num_rows($sql_tmp);
              if ($columnas == 0) {
                  echo "<h5 class = 'center'>No hay reportes en ruta</h5>";
              }else{
                while ($tmp = mysqli_fetch_array($sql_tmp)) {
                    $id_reporte = $tmp['id_reporte'];
                    $sql_reporte = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM reportes WHERE id_reporte = $id_reporte"));
                    $id_cliente = $sql_reporte['id_cliente'];
                    $ver = mysqli_query($conn, "SELECT * FROM clientes WHERE id_cliente = $id_cliente");
                    if (mysqli_num_rows($ver) == 0) {
                        $ver = mysqli_query($conn, "SELECT * FROM especiales WHERE id_cliente = $id_cliente");
                    }
                    $sql_nombre = mysqli_fetch_array($ver);
                    $id_comunidad = $sql_nombre['lugar'];
                    $comunidad = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM comunidades WHERE id_comunidad = $id_comunidad"));
                ?>
                <tr>
                    <td><?php echo $sql_reporte['id_reporte']; ?></td>
                    <td><?php echo $sql_nombre['nombre']; ?></td>
                    <td><?php echo $comunidad['nombre']; ?></td>
                    <td><?php echo $sql_reporte['descripcion']; ?></td>
                    <td><?php echo $sql_reporte['fecha']; ?></td>
                    <td><a onclick="borrar_rep(<?php echo $sql_reporte['id_reporte']; ?>);" class="btn btn-floating red darken-1 waves-effect waves-light"><i class="material-icons">delete</i></a></td>
                </tr>
                <?php
                }
              }
              ?>
              </tbody>
          </table>
        </div>
        <br>
        <a onclick="modal()" class="btn waves-light waves-effect right pink">Imprimir</a><br><br><br>

	</div>
</body>
</html>