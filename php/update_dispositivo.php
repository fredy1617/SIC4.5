<?php
include ('conexion.php');
include ('is_logged.php');
date_default_timezone_set('America/Mexico_City');
$Link = $conn->real_escape_string($_POST['valorLink']);
$Obsevaciones = $conn->real_escape_string($_POST['valorObservaciones']);
$ManoObra = $conn->real_escape_string($_POST['valorManoObra']);
$IdDispositivo = $conn->real_escape_string($_POST['valorIdDispositivo']);
$Estatus = $conn->real_escape_string($_POST['valorEstatus']);
$Refacciones = $conn->real_escape_string($_POST['valorRefacciones']);
$Extras = $conn->real_escape_string($_POST['valorExtras']);
$Contra = $conn->real_escape_string($_POST['valorContra']);
$FechaHoy = date('Y-m-d');
$Hora = date('H:i:s');

$Tecnico = $_SESSION['user_id'];

$xRefa = explode(",", $Refacciones);
$num = count($xRefa)-1;
$T_Refa = 0;
if ($num>0) {
	for ($i=0; $i < $num; $i++) { 
		$separa = explode("-", $xRefa[$i]);
		$desc = $separa[0];
		$dinero = $separa[1];
		if(mysqli_num_rows(mysqli_query($conn, "SELECT * FROM refacciones WHERE descripcion = '$desc' AND cantidad = '$dinero' AND fecha = '$FechaHoy' AND id_dispositivo = '$IdDispositivo' "))<=0){
			mysqli_query($conn, "INSERT INTO refacciones (descripcion, cantidad, fecha, id_dispositivo) VALUES('$desc', '$dinero', '$FechaHoy', '$IdDispositivo')");
		}
	}
}
$sql = mysqli_query($conn, "SELECT cantidad FROM refacciones WHERE id_dispositivo = '$IdDispositivo' ");
if (mysqli_num_rows($sql)>0) {
	while ($refas = mysqli_fetch_array($sql)) {
		$T_Refa += $refas['cantidad'];
	}
}
#REGISTRAMOS LA ACTIVIDAD DE SI ES COTIZADO EN PROCESO O SI SE MANDO A LISTOS CON FECHA Y HORA SI YA SE REGISTRO ANTES LA ACTIVIDAD SOLO SE ACTUALIZA FECHA Y HORA
if(mysqli_num_rows(mysqli_query($conn, "SELECT * FROM actividades_taller WHERE dispositivo = '$IdDispositivo' AND accion = '$Estatus'"))<=0){
    mysqli_query($conn, "INSERT INTO actividades_taller (dispositivo, accion, fecha, hora, tecnico) VALUES('$IdDispositivo', '$Estatus', '$FechaHoy', '$Hora', '$Tecnico')");
}else{
    mysqli_query($conn, "UPDATE actividades_taller SET fecha = '$FechaHoy', hora = '$Hora', tecnico = '$Tecnico' WHERE dispositivo = '$IdDispositivo' AND accion = '$Estatus'");
}

$sql = "UPDATE dispositivos SET link = '$Link', observaciones = '$Obsevaciones', tecnico = '$Tecnico', estatus = '$Estatus', precio = 0, mano_obra = '$ManoObra', t_refacciones = '$T_Refa', extras = '$Extras', contra = '$Contra' WHERE id_dispositivo = '$IdDispositivo'";
if (mysqli_query($conn, $sql)) {
	echo '<script>M.toast({html:"Se ha actualizado correctamente el folio.", classes: "rounded"})</script>';
}else{
	echo '<script>M.toast({html:"Ha ocurrido un error...", classes: "rounded"})</script>';
}
if ($Estatus == 'Listo' OR $Estatus == 'Listo (No Reparado)') {
  ?>
    <script>
      var a = document.createElement("a");
      a.href = "../views/listos.php";
      a.click();   
    </script>
    <?php
}
?>
	<div id="refrescar">
    <div class="row">
        <h3 class="hide-on-med-and-down">Atender Dispositivo:</h3>
        <h5 class="hide-on-large-only">Atender Dispositivo:</h5>
      </div>
    <?php        
        $datos = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM dispositivos WHERE id_dispositivo = $IdDispositivo"));
    ?>
      <div class="row">
      <ul class="collection">
            <li class="collection-item avatar">
              <div class="hide-on-large-only"><br><br></div>
              <img src="../img/cliente.png" alt="" class="circle">
              <span class="title"><b>Folio: </b><?php echo $datos['id_dispositivo'];?></span>
              <p><b>Nombre: </b><?php echo $datos['nombre'];?><br>
                <b>Telefono: </b><?php echo $datos['telefono'];?><br>
                 <b>Dispositivo: </b><?php echo $datos['tipo'];?> <?php echo $datos['marca'];?><br>
                 <b>Modelo: </b><?php echo $datos['modelo'];?><br>
                  <?php if ($datos['extras'] == NULL) {
                    $ext = 'Color '.$datos['color'].' con '.$datos['cables'];
                 }else{
                    $ext = $datos['extras'];
                 }
                 ?>  
                 <div class="col s12">
                  <b class="col s4 m2 l2">Extras: </b>
                  <div class="col s12 m6 l6">
                    <input type="text" id="extra" name="extra" value="<?php echo $ext;?>">
                  </div>
                 </div>   
                 <div class="col s12">
                  <b class="col s4 m2 l2">Contraseña: </b>
                  <div class="col s12 m6 l6">
                    <input type="text" id="contra" name="contra" value="<?php echo $datos['contra'];?>">
                  </div>
                 </div>   
                 <?php 
                 if ($datos['precio']==0) {
                  $Tot = $datos['mano_obra']+$datos['t_refacciones'];
                 }else{
                  $Tot = $datos['precio'];
                 }
                  $sql = mysqli_query($conn, "SELECT * FROM pagos WHERE id_cliente = '$IdDispositivo' AND descripcion = 'Anticipo' AND tipo = 'Dispositivo'");
                  $Total_anti = 0;
                  if (mysqli_num_rows($sql)>0) {
                    
                    while ($anticipo = mysqli_fetch_array($sql)) {

                      $Total_anti += $anticipo['cantidad'];
                    }
                  }
                 $resto = $Tot-$Total_anti;
                
                 ?>
                 <b>Total: </b><?php echo "$".$Tot;?><br>
                 <b>Anticipo: </b><?php echo "$".$Total_anti;?><br>
                 <b>Resta: </b><?php echo "$".$resto;?><br>

                 <hr>
                 <b>Falla: </b><?php echo $datos['falla'];?>
              </p>
              <br>
              <a href="#!" class="secondary-content"><span class="new badge green" data-badge-caption="<?php echo 'FECHA DE ENTRADA: '.$datos['fecha'];?>"></span></a>
            </li>
      </ul>
      </div>
    <div class="row"> 
        <div class="row col s12">
          <div class="col s12 m3 l3">
          <div id="resp_anticipo"></div>
          <h3 class="hide-on-med-and-down">Abonar:</h3>
          <h5 class="hide-on-large-only">Abonar:</h5>
          </div>
          <form class="col s12 m9 l9">        
              <div class="row col s9 m5 l5">
              <div class="input-field">
                  <i class="material-icons prefix">payment</i>
                  <input id="monto" type="number" class="validate" data-length="6" value="0" required>
                  <label for="monto">Cantidad:</label>
              </div>
                </div>
                <div class="col s3 m4 l4">
              <p><br>
                  <input type="checkbox" id="banco"/>
                  <label for="banco">Banco</label>
                </p>
            </div><br>
            <a onclick="insert_anticipo();" class="waves-effect waves-light btn pink  col s8 m3 l3"><i class="material-icons right">send</i>Registrar Anticipo</a>
          </form>         
        </div>
        <div class="row">
            <div id="borrar_pagos">
              <table>
                <thead>
                  <th>#</th>
                  <th>Descripcion</th>
                  <th>Fecha</th>
                  <th>Cantidad</th>
                  <th>Borrar</th>
                </thead>
                <tbody>                 
                  <?php
                  $sql = mysqli_query($conn, "SELECT * FROM pagos WHERE id_cliente = '$IdDispositivo' AND descripcion = 'Anticipo' AND tipo = 'Dispositivo'");
                  $Total = 0;
                  if (mysqli_num_rows($sql)>0) {
                    $aux= 0;
                    
                    while ($anticipo = mysqli_fetch_array($sql)) {
                      $aux++;
                      $Total += $anticipo['cantidad'];
                      ?>
                    <tr>
                      <td><?php echo $aux; ?></td>
                      <td><?php echo $anticipo['descripcion']; ?></td>
                      <td><?php echo $anticipo['fecha']; ?></td>
                      <td>$<?php echo $anticipo['cantidad']; ?></td>
                      <td><a onclick="borrar(<?php echo $anticipo['id_pago']; ?>);" class="btn btn-floating red darken-1 waves-effect waves-light"><i class="material-icons">delete</i></a></td>
                    </tr>
                      <?php
                    }
                  }
                  ?>
                  <tr>
                    <td></td>
                    <td></td>
                    <td><b>TOTAL:</b></td>
                    <td>$<?php echo $Total; ?></td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
      <form class="col s12" action="../php/folioSalida.php" target="_blank" method="POST"><br>
        <div class="input-field col s12 m6 l6">
              <i class="material-icons prefix">insert_link</i>
              <textarea id="link" class="materialize-textarea validate" data-length="150" ><?php echo $datos['link'];?></textarea>
              <label for="link">Link del Articulo:</label>
            </div> 
            <div class="input-field col s12 m6 l6">
              <i class="material-icons prefix">comment</i>
              <textarea id="observaciones" class="materialize-textarea validate" data-length="150" ><?php echo $datos['observaciones'];?></textarea>
              <label for="observaciones">Observaciones:</label>
            </div> 
            <div class="row">
            <h4>Refacciones:</h4>
            <div id="refa_borrar">
              <table>
                <thead>
                  <th>#</th>
                  <th>Refacción</th>
                  <th>Precio</th>
                  <th>Borrar</th>
                </thead>
                <tbody>                 
                  <?php
                  $sql = mysqli_query($conn, "SELECT * FROM refacciones WHERE id_dispositivo = '$IdDispositivo' ");
                  $Total = 0;
                  if (mysqli_num_rows($sql)>0) {
                    $aux= 0;
                    
                    while ($refas = mysqli_fetch_array($sql)) {
                      $aux++;
                      $Total += $refas['cantidad'];
                      ?>
                    <tr>
                      <td><?php echo $aux; ?></td>
                      <td><?php echo $refas['descripcion']; ?></td>
                      <td>$<?php echo $refas['cantidad']; ?></td>
                      <td><a onclick="borrar_refa(<?php echo $refas['id_refaccion']; ?>);" class="btn btn-floating red darken-1 waves-effect waves-light"><i class="material-icons">delete</i></a></td>
                    </tr>
                      <?php
                    }
                  }else{
                    echo "<h4 class = 'center'>No hay refacciones</h4>";
                  }
                  ?>
                  <tr>
                    <td></td>
                    <td><b>SUBTOTAL:</b></td>
                    <td>$<?php echo $Total; ?></td>
                  </tr>
                </tbody>
              </table>
            </div>
            <div class="button">
             <button type="button" id="add_Desc" class="waves-effect waves-light btn pink right"><i class="material-icons right">add</i>Agregar</button>
            </div>
          </div><br> 
          <div class="row">
            <div class="col s12 m4 l4">
            <label><i class="material-icons">assignment_late</i> Estatus:</label>
              <div class="input-field">
                <select id="estatus" class="browser-default">
                  <option value="<?php echo $datos['estatus'];?>" selected><?php echo $datos['estatus'];?></option>
                  <option value="Cotizado">Cotizado</option>
                  <option value="En Proceso">En Proceso</option>
                  <option value="Listo">Listo</option>
                  <option value="Listo (No Reparado)">Listo (No Reparado)</option>
                </select>
              </div>
            </div>
            <div class="input-field col s12 m3 l3"><br>
                <i class="material-icons prefix">monetization_on</i>
                <input id="mano" type="number" class="validate" data-length="6" value="<?php  if($datos['precio'] > 0){ echo $datos['precio']; }else{ echo $datos['mano_obra'];}?>" required>
                <label for="mano">Mano de Obra:</label>
              </div>
              <div class="input-field col s12 m3 l3"><br>
                <h5><b>TOTAL: $<?php if($datos['precio'] > 0){ echo $datos['precio']; }else{ echo $datos['mano_obra']+$datos['t_refacciones'];}?></b></h5>
              </div>
              <div>
               <br><br>
               <a onclick="salida();" class="waves-effect waves-light btn pink right tooltipped" data-position="bottom" data-tooltip="Solo guardar"><i class="material-icons right">save</i>Guardar</a>
              </div>
           </div>   
      </form>     
    </div>
  </div>