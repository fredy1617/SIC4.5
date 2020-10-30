<?php
#INCLUIMOS EL ARCHIVO EL CUAL HACE LA CONEXION DE LA BASE DE DATOS PARA ACCEDER A LA INFORMACION DEL SISTEMA
include('../php/conexion.php');
#INCLUIMOS EL PHP DONDE VIENE LA INFORMACION DEL INICIO DE SESSION
include('../php/is_logged.php');

#DEFINIMOS UNA ZONA HORARIA
date_default_timezone_set('America/Mexico_City');
#GENERAMOS UNA FECHA DEL DIA EN CURSO REFERENTE A LA ZONA HORARIA
$Fecha_Hoy = date('Y-m-d');

#RECIBIMOS EL LA VARIABLE valorSerie CON EL METODO POST QUE ES EL NUMERO DE SERIE DEL MATERIAL
$Serie = $conn->real_escape_string($_POST['valorSerie']);
#SELECCIONAMOS LA INFORMACION DEL MATERIAL SEGUN EL NUMERO DE SERIE RECIBIDO
$material=mysqli_fetch_array(mysqli_query($conn,"SELECT * FROM stock_tecnicos WHERE serie = '$Serie' AND disponible = 0"));
#ASIGNAMOS LA INFORMACION DEL MATERIAL EN VARIABLES RESPECTIVAMENTE
$nombre = $material['nombre'];
$tecnico = $material['tecnico'];
$ruta = $material['ruta'];
#RECIBIMOS EL LA VARIABLE valorMotivo CON EL METODO POST QUE ES EL MOTIVO QUE SE ESCRIBIO EN EL MODAL
$Motivo = $conn->real_escape_string($_POST['valorMotivo']);
#VERIFICAMOS QUE ESTE MATERIA NO HAYA SIDO AGREGADO YA A LA TABLA historial_stock
if(mysqli_num_rows(mysqli_query($conn, "SELECT * FROM historial_stock WHERE serie = '$Serie' AND material = '$nombre'"))>0){
	#SI YA FUE AGREGADA MOSTRAMOS EL MSJ DE ALERTA
	echo '<script>M.toast({html:"Ya se encuentra un material registrado con los mismos valores en borrados.", classes: "rounded"})</script>';
	#ELIMINAMOS EL MATERIAL DE LA TABLA stock_tecnicos
	if(mysqli_query($conn, "DELETE FROM stock_tecnicos WHERE serie = '$Serie' AND tecnico = '$tecnico'")){
	  #SI ES ELIMINADO MANDAR MSJ CON ALERTA
	  echo '<script >M.toast({html:"Material Borrado...", classes: "rounded"})</script>';
	}
}else{ 
	#SI NO ESTA EN LA TABLA, LO AGREGAMOS A historial_stock
	if(mysqli_query($conn, "INSERT INTO historial_stock(material, serie, tecnico, ruta, motivo, fecha) VALUES ('$nombre', '$Serie', '$tecnico', '$ruta', '$Motivo', '$Fecha_Hoy')")){
		echo '<script>M.toast({html:"Se agrego a historial_stock.", classes: "rounded"})</script>';
		# SI SE AGREGA ELIMINAMOS EL MATERIAL DE LA TABLA stock_tecnicos
		if(mysqli_query($conn, "DELETE FROM stock_tecnicos WHERE serie = '$Serie' AND tecnico = '$tecnico'")){
		  #SI ES ELIMINADO MANDAR MSJ CON ALERTA
		  echo '<script >M.toast({html:"Material Borrado...", classes: "rounded"})</script>';
		}
    }
} 
   # MOSTRAMOS LA TABLA CON LA INFORMACION DE LAS ANTEAS Y ROSUTERS YA CON LA QUE SE HAYA BORRADO  
?>
	<div class="row" id="Continuar">
        <div class="col s2"></div>
        <div class="row col s8">
        <table class="bordered highlight responsive-table">
          <thead>
            <th>#</th>
            <th>Tipo</th>
            <th>Nombre</th>
            <th>Serie</th>
            <th>Ruta</th>
            <?php if(in_array($_SESSION['user_id'], array(59, 66, 49))){ ?>
            <th>Borrar</th>
          	<?php } ?>
          </thead>
          <tbody>
          <?php
          $tab = mysqli_query($conn, "SELECT * FROM stock_tecnicos WHERE tipo IN ('Antena', 'Router') AND disponible = 0 AND tecnico = $tecnico");
          while($unidad = mysqli_fetch_array($tab)){
            ?>
            <tr>
              <td><?php echo $unidad['id']; ?></td>
              <td><?php echo $unidad['tipo']?></td>
              <td><?php echo $unidad['nombre']; ?></td>
              <td><?php echo $unidad['serie']; ?></td>
              <td><?php echo $unidad['ruta']; ?></td>
              <?php if(in_array($_SESSION['user_id'], array(59, 66, 49))){ ?>
              <td><a onclick="verificar_eliminar('<?php echo $unidad['serie'] ?>')"class="btn btn-floating red darken-1 waves-effect waves-light"><i class="material-icons">delete</i></a></td>
          	  <?php } ?>
            </tr>
          <?php
          }
          ?> 
          </tbody>
        </table>
        </div>
    </div>