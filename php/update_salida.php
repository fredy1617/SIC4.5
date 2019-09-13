<?php
date_default_timezone_set('America/Mexico_City');
include('../php/conexion.php');
include('is_logged.php');

$filtrarObservaciones = $_POST['valorObservaciones'];

//Filtro anti-XSS
$caracteres_malos = array("<", ">", "\"", "'", "/", "<", ">", "'", "/");
$caracteres_buenos = array("& lt;", "& gt;", "& quot;", "& #x27;", "& #x2F;", "& #060;", "& #062;", "& #039;", "& #047;");

$Observaciones = str_replace($caracteres_malos, $caracteres_buenos, $filtrarObservaciones);
$ManoObra = $conn->real_escape_string($_POST['valorManoObra']);
$IdDispositivo = $conn->real_escape_string($_POST['valorIdDispositivo']);
$Link = $conn->real_escape_string($_POST['valorLink']);
$Estatus = $conn->real_escape_string($_POST['valorEstatus']);
$Tecnico = $_SESSION['user_id'];
$Refacciones = $_POST['valorRefacciones'];
$FechaSalida = date('Y-m-d');

$xRefa = explode(",", $Refacciones);
$num = count($xRefa)-1;
$T_Refa = 0;
if ($num>0) {
	for ($i=0; $i < $num; $i++) { 
		$separa = explode("-", $xRefa[$i]);
		$desc = $separa[0];
		$dinero = $separa[1];
		if(mysqli_num_rows(mysqli_query($conn, "SELECT * FROM refacciones WHERE descripcion = '$desc' AND cantidad = '$dinero' AND fecha = '$FechaSalida' AND id_dispositivo = '$IdDispositivo' "))<=0){
			mysqli_query($conn, "INSERT INTO refacciones (descripcion, cantidad, fecha, id_dispositivo) VALUES('$desc', '$dinero', '$FechaSalida', '$IdDispositivo')");
		}
	}
}
$sql = mysqli_query($conn, "SELECT cantidad FROM refacciones WHERE id_dispositivo = '$IdDispositivo' ");
if (mysqli_num_rows($sql)>0) {
	while ($refas = mysqli_fetch_array($sql)) {
		$T_Refa += $refas['cantidad'];
	}
}

$sql = "UPDATE dispositivos SET observaciones='$Observaciones', tecnico='$Tecnico', estatus='$Estatus', fecha_salida='$FechaSalida', link='$Link', precio = 0, mano_obra='$ManoObra', t_refacciones = '$T_Refa' WHERE id_dispositivo='$IdDispositivo'";
if(mysqli_query($conn, $sql)){
	echo '<script>M.toast({html:"Se ha actualizado correctamente el folio.", classes: "rounded"})</script>';
}else{
	echo  '<script>M.toast({html:"Ha ocurrido un error.", classes: "rounded"})</script>';
}

?>
<div >
    <div class="row">
      <h2 class="hide-on-med-and-down">Salida</h2>
      <h4 class="hide-on-large-only">Salida</h4>
    </div>
    
        <?php        
        $datos = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM dispositivos WHERE id_dispositivo = $IdDispositivo"));
        ?>
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
                  ?>
                  <b>Extras: </b>Color <?php echo $datos['color'];?>, con <?php echo $datos['cables'];?><br>
                 <?php 
                 }else{
                  ?>
                  <b>Extras: </b><?php echo $datos['extras'];?><br>
                  <?php
                 }
                 ?>                 
                 <b>Contraseña: </b><?php echo $datos['contra'];?><br>
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
                 <b>Resta: </b><?php echo "$".$resto;?>
                 <a href="#!" class="primary-content"><span class="new badge red" data-badge-caption="<?php echo 'FECHA DE SALIDA: '.$datos['fecha_salida'];?>"></span></a>
              <br><br>
              <a href="#!" class="secondary-content"><span class="new badge green" data-badge-caption="<?php echo 'FECHA DE ENTRADA: '.$datos['fecha'];?>"></span></a>
                 <hr>
                 <b>Falla: </b><?php echo $datos['falla'];?>
              </p>
              <br>              
            </li>
        </ul>
        <div class="row">
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
            </div>
            <div class="row">
              <div class="col s12 m3 l3">
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
              <div class="input-field col s12 m2 l2"><br>
                  <i class="material-icons prefix">monetization_on</i>
                  <input id="mano" type="number" class="validate" data-length="6" value="<?php  if($datos['precio'] > 0){ echo $datos['precio']; }else{ echo $datos['mano_obra'];}?>" required>
                  <label for="mano">Mano de Obra:</label>
                </div>
                <div class="input-field col s12 m3 l3"><br>
                  <h5><b>TOTAL: $<?php if($datos['precio'] > 0){ echo $datos['precio']; }else{ echo $datos['mano_obra']+$datos['t_refacciones'];}?></b></h5>
                </div>
                <div class="col s3 m2 l2">
                  <p><br>
                    <input type="checkbox" id="banco"/>
                    <label for="banco">Banco</label>
                  </p>
                </div><br>
                <div>
                 <input name="id_dispositivo" id="id_dispositivo" type="hidden" class="validate center" data-length="200" value="<?php echo $datos['id_dispositivo'];?>"><br>
                <a onclick="salida();" class="btn btn-floating pink  tooltipped" data-position="bottom" data-tooltip="GUARDAR"><i class="material-icons">save</i></a>
                <button onclick="salida();" type="submit"  class="btn btn-floating pink waves-effect waves-light tooltipped" data-position="bottom" data-tooltip="GUARDAR e IMPRIMIR"><i class="material-icons">print</i></button>
                
                <a onclick="sacar();" class="btn btn-floating pink waves-effect waves-light tooltipped" data-position="bottom" data-tooltip="SALIDA"><i class="material-icons">exit_to_app</i></a>
                </div>
                <div id="sacar"></div>
             </div>             
          </form>
        </div>
        <?php
        mysqli_close($conn);
        ?>
    <br><br>
  </div>