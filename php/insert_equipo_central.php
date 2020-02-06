<?php 
include('../php/conexion.php');
include('is_logged.php');

$id_user = $_SESSION['user_id'];
$Nombre = $conn->real_escape_string($_POST['valorNombre']);
$Marca = $conn->real_escape_string($_POST['valorMarca']);
$Modelo = $conn->real_escape_string($_POST['valorModelo']);
$IP = $conn->real_escape_string($_POST['valorIP']);
$Instalacion = $conn->real_escape_string($_POST['valorInstalacion']);
$Descripcion = $conn->real_escape_string($_POST['valorDescripcion']);
$Modificacion = $conn->real_escape_string($_POST['valorModificacion']);
$IdCentral = $conn->real_escape_string($_POST['valorIdCentral']);

if(mysqli_num_rows(mysqli_query($conn, "SELECT * FROM equipos WHERE nombre='$Nombre' AND marca='$Marca' AND modelo='$Modelo' AND ip='$IP' AND fecha_instalacion='$Instalacion' AND descripcion='$Descripcion' AND id_central='$IdCentral'"))>0){
	echo '<script >M.toast({html:"Y se encuentra registrado un equipo con los mismos valores.", classes: "rounded"})</script>';
}else{
	$sql = "INSERT INTO equipos (nombre, marca, modelo, ip, fecha_instalacion, modificacion, descripcion, status, usuario, id_central) VALUES('$Nombre', '$Marca', '$Modelo', '$IP', '$Instalacion', '$Modificacion', '$Descripcion', 'Activo', '$id_user', '$IdCentral')";
	if (mysqli_query($conn, $sql)) {
		echo '<script >M.toast({html:"El equipo se dio de alta satisfactoriamente.", classes: "rounded"})</script>';
	}else{
		echo '<script >M.toast({html:"Ocurrio un error.", classes: "rounded"})</script>';
	}
}
?>
<table class="bordered highlight responsive-table">
          <thead>
            <tr>
              <th>#</th>
              <th>Dispositivo</th>
              <th>Marca</th>
              <th>Modelo</th>
              <th>IP</th>
              <th>Descripción</th>
              <th>Fecha</th>
              <th>Modificacion</th>
              <th>Estatus</th>
              <th>Razon</th>
              <th>Registró</th>
              <!--<th>Imprimir</th>-->
              <th>Editar</th>
            </tr>
          </thead>
          <tbody>
          <?php
          $sql_equipos = "SELECT * FROM equipos WHERE id_central = '$IdCentral' ORDER BY id DESC";
          $resultado = mysqli_query($conn, $sql_equipos);
          $aux = mysqli_num_rows($resultado);
          if($aux>0){
          while($Equipo = mysqli_fetch_array($resultado)){
            $id_user = $Equipo['usuario'];
            $user = mysqli_fetch_array(mysqli_query($conn, "SELECT user_name FROM users WHERE user_id = '$id_user'"));
          ?>
            <tr>
              <td><b><?php echo $Equipo['id'];?></b></td>
              <td><?php echo $Equipo['nombre'];?></td>
              <td><?php echo $Equipo['marca'];?></td>
              <td><?php echo $Equipo['modelo'];?></td>
              <td><?php echo $Equipo['ip'];?></td>
              <td><?php echo $Equipo['descripcion'];?></td>
              <td><?php echo $Equipo['fecha_instalacion'];?></td>
              <td><?php echo $Equipo['modificacion'];?></td>
              <td><?php echo $Equipo['status'];?></td>
              <td><?php echo $Equipo['razon'];?></td>
              <td><?php echo $user['user_name'];?></td>
              <!--<td><a onclick="imprimir(<?php echo $pagos['id'];?>);" class="btn btn-floating pink waves-effect waves-light"><i class="material-icons">print</i></a></td>-->
               <td><br><form action="editar_equipo.php" method="post"><input type="hidden" name="id" value="<?php echo $Equipo['id'];?>"><button type="submit" class="btn-floating btn-tiny waves-effect waves-light pink"><i class="material-icons">edit</i></button></form></td>
            </tr>
            <?php
            $aux--;
            }//Fin while
            }else{
            echo "<center><b><h5 class = 'red-text'>Esta central aún no ha registrado equipos</h5 ></b></center>";
          }
          ?> 
          </tbody>
        </table>