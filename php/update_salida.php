<?php
date_default_timezone_set('America/Mexico_City');
include('../php/conexion.php');
include('is_logged.php');
include('../escpos/autoload.php'); //Nota: si renombraste la carpeta a algo diferente de "ticket" cambia el nombre en esta línea
use Mike42\Escpos\Printer;
use Mike42\Escpos\EscposImage;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
$Link = $_POST['valorLink'];
$filtrarObservaciones = $_POST['valorObservaciones'];
$filtrarPrecio = $_POST['valorManoObra'];
$filtrarIdDispositivo = $_POST['valorIdDispositivo'];
$filtrarTecnico = $_POST['valorTecnico'];
$filtrarEstatus = $_POST['valorEstatus'];
$Imprimir = $_POST['valorImprimir'];

//Filtro anti-XSS
$caracteres_malos = array("<", ">", "\"", "'", "/", "<", ">", "'", "/");
$caracteres_buenos = array("& lt;", "& gt;", "& quot;", "& #x27;", "& #x2F;", "& #060;", "& #062;", "& #039;", "& #047;");

$Observaciones = str_replace($caracteres_malos, $caracteres_buenos, $filtrarObservaciones);
$ManoObra = str_replace($caracteres_malos, $caracteres_buenos, $filtrarPrecio);
$IdDispositivo = str_replace($caracteres_malos, $caracteres_buenos, $filtrarIdDispositivo);
$Tecnico = str_replace($caracteres_malos, $caracteres_buenos, $filtrarTecnico);
$Estatus = str_replace($caracteres_malos, $caracteres_buenos, $filtrarEstatus);

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
<div>
    
    <div class="row">
      <h2 class="hide-on-med-and-down">Salida</h2>
      <h4 class="hide-on-large-only">Salida</h4>
    </div>
    
    	<?php       
        $datos = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM dispositivos WHERE id_dispositivo = '$IdDispositivo'"));
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
                 <b>Falla: </b><?php echo $datos['falla'];?><br>
              </p>
              <a href="#!" class="primary-content"><span class="new badge red" data-badge-caption="<?php echo 'FECHA DE SALIDA: '.$datos['fecha_salida'];?>"></span></a>
              <br><br>
              <a href="#!" class="secondary-content"><span class="new badge green" data-badge-caption="<?php echo 'FECHA DE ENTRADA: '.$datos['fecha'];?>"></span></a>
            </li>
        </ul>
        <?php
        $id_user = $datos['tecnico'];
        if($id_user==''){
          $sql_usr[0] = 'Sin tecnico';
        }else{
          $sql_usr = mysqli_fetch_array(mysqli_query($conn, "SELECT user_name FROM users WHERE user_id=$id_user"));  
        }        
        ?>
        <form class="col s12" action="../php/folioSalida.php" target="_blank" method="POST">
          <div class="row">
            <div class="input-field col s12">
              <i class="material-icons prefix">insert_link</i>
              <textarea id="link" class="materialize-textarea validate" data-length="150" ><?php echo $datos['link'];?></textarea>
              <label for="link">Link de Mercado Libre:</label>
            </div>
          </div>
          <div class="row">
            <div class="input-field col s12">
              <i class="material-icons prefix">comment</i>
              <textarea id="observaciones" class="materialize-textarea validate" data-length="150" ><?php echo $datos['observaciones'];?></textarea>
              <label for="observaciones">Observaciones:</label>
            </div>
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
                  if (mysqli_num_rows($sql)>0) {
                    $aux= 0;
                    while ($refas = mysqli_fetch_array($sql)) {
                      $aux++;
                      ?>
                    <tr>
                      <td><?php echo $aux; ?></td>
                      <td><?php echo $refas['descripcion']; ?></td>
                      <td>$<?php echo $refas['cantidad']; ?></td>
                      <td><a onclick="borrar_refa(<?php echo $refas['id_refaccion']; ?>);" class="btn btn-floating red darken-1 waves-effect waves-light"><i class="material-icons">delete</i></a></td>

                    </tr>
                      <?php
                    }
                  }
                  ?>
                </tbody>
              </table>
            </div>
            <div class="button">
             <button type="button" id="add_Desc" class="waves-effect waves-light btn pink right"><i class="material-icons right">add</i>Agregar</button>
            </div>
          </div>
          <div class="row">
            <div class="input-field col s12 m4 l4">
              <i class="material-icons prefix">monetization_on</i>
              <input id="mano" type="number" class="validate" data-length="6" value="<?php  if($datos['precio'] > 0){ echo $datos['precio']; }else{ echo $datos['mano_obra'];}?>" required>
              <label for="mano">Mano de Obra:</label>
            </div>
            <input id="id_dispositivo" type="hidden" class="validate" data-length="6" value="<?php echo $datos['id_dispositivo'];?>" required>
            
            <div class="input-field col l5 m5 s12">
              <select id="estatus" class="browser-default">
                <option value="<?php echo $datos['estatus'];?>" selected><?php echo $datos['estatus'];?></option>
                <option value="Listo (En Taller)">Listo (En Taller)</option>
              </select>
            </div>
             <input name="id_dispositivo" type="hidden" class="validate center" data-length="200" value="<?php echo $datos['id_dispositivo'];?>">
             <button onclick="salida(1);" type="submit" class="waves-effect waves-light btn pink right tooltipped" data-position="bottom" data-tooltip="Guardar e imprimir"><i class="material-icons right">print</i>Ticket</button><br><br>
             <a onclick="salida(0);" class="waves-effect waves-light btn pink right tooltipped" data-position="bottom" data-tooltip="Solo guardar"><i class="material-icons right">save</i>Guardar</a>
            </div>            
        </form>
       
    
    <?php
    mysqli_close($conn);
    ?>
    </div>
    <script>
  $(document).ready(function() {
    $("#add_Desc").click(function(){
        var contador = $("input[type='text']").length;

        $(this).before('<div class="row"><div class= "col s12 m6 l6"><div class="input-field"><i class="material-icons prefix">comment</i><input type="text" id="Desc_'+ contador +'" name="Desc[]"/><label for="Desc_'+ contador +'">Descripción No.'+ contador +' :</label></div></div><div class="col s10 m4 l4"><div class="input-field"><i class="material-icons prefix">attach_money</i><input type="number" id="precio'+ contador +'" name="precio[]"/><label for="precio'+ contador +'">Preco No.'+ contador +' :</label></div></div><input id="numero'+ contador +'" value="'+ contador +'" type="hidden"><button type="button" class="delete_Desc btn-floating btn-tiny waves-effect waves-light pink "><i class="material-icons prefix">delete</i></button></div>');
    });

    $(document).on('click', '.delete_Desc', function(){
        $(this).parent().remove();
    });
});
</script>
    <br><br><br>
