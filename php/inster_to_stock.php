<?php
date_default_timezone_set('America/Mexico_City');
include('../php/conexion.php');
include('is_logged.php');

$Registro = $_SESSION['user_id'];
$Fecha_hoy = date('Y-m-d');

$IdTecnico = $conn->real_escape_string($_POST['valorIdTecnico']);
$Tipo = $conn->real_escape_string($_POST['valorTipo']);
$Nombre = $conn->real_escape_string($_POST['valorNombre']);
$Serie = $conn->real_escape_string($_POST['valorSerie']);
$Cantidad = $conn->real_escape_string($_POST['valorCantidad']);
$Regreso = $conn->real_escape_string($_POST['valorRegreso']);

if ($Regreso == 'Si') {
  if (mysqli_query($conn, "UPDATE stock_tecnicos SET uso = 300, fecha_salida = '$Fecha_hoy', disponible = 1  WHERE uso < 300 AND tecnico = $IdTecnico AND disponible = 0 AND tipo = 'Bobina'")){
    echo '<script>M.toast({html:"Se actualizo la Bobina...", classes: "rounded"})</script>';
  }else{
    echo '<script>M.toast({html:"Ocurrio un error al actualizar la Bobina...", classes: "rounded"})</script>';
  }
}

$sql = "INSERT INTO stock_tecnicos (nombre, serie, tipo, cantidad, fecha_alta, registro, tecnico) VALUES('$Nombre', '$Serie', '$Tipo', '$Cantidad', '$Fecha_hoy', '$Registro', '$IdTecnico')";
if(mysqli_query($conn, $sql)){
  echo '<script>M.toast({html:"El material se dió de alta satisfcatoriamente.", classes: "rounded"})</script>';
}else{
  echo '<script>M.toast({html:"Ha ocurrido un error al insertar.", classes: "rounded"})</script>';
}

?>
    <div class="row">
        <h3 class="hide-on-med-and-down">Stock:</h3>
        <h5 class="hide-on-large-only">Stock:</h5>
      </div>
    <?php   
      $datos = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM users WHERE user_id = $IdTecnico"));
      $Antenas = mysqli_fetch_array(mysqli_query($conn, "SELECT SUM(cantidad) AS total FROM stock_tecnicos WHERE disponible = 0 AND tecnico = $IdTecnico  AND tipo = 'Antena'"));
      $Routers = mysqli_fetch_array(mysqli_query($conn, "SELECT SUM(cantidad) AS total FROM stock_tecnicos WHERE disponible = 0 AND tecnico = $IdTecnico  AND tipo = 'Router'"));
      $bobina = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM stock_tecnicos WHERE disponible = 0 AND tecnico = $IdTecnico  AND tipo = 'Bobina'"));
      $CantidadB = $bobina['cantidad']-$bobina['uso'] ;
      $totalC = mysqli_fetch_array(mysqli_query($conn, "SELECT SUM(cantidad) AS total FROM stock_tecnicos WHERE disponible = 0 AND tecnico = $IdTecnico  AND tipo = 'Tubo(s)'"));
      $totalU = mysqli_fetch_array(mysqli_query($conn, "SELECT SUM(uso) AS total FROM stock_tecnicos WHERE disponible = 0 AND tecnico = $IdTecnico  AND tipo = 'Tubo(s)'"));
      $Tubos = $totalC['total']-$totalU['total'];
    ?>
      <div class="row">
      <ul class="collection">
            <li class="collection-item avatar">
              <div class="hide-on-large-only"><br><br></div>
              <img src="../img/cliente.png" alt="" class="circle">
              <span class="title"><b>Id: </b><?php echo $IdTecnico;?></span>
              <p><b>Nombre: </b><?php echo $datos['firstname'].' '.$datos['lastname'];?><br>
                <b>Antenas: <?php echo $Antenas['total'];?></b><br>                  
                <b>Routers: <?php echo $Routers['total'];?></b><br>                  
                <b>Metros de bobina: <?php echo $CantidadB;?></b><br>                  
                <b>Tubos: <?php echo $Tubos;?></b><br>
                <hr>
                <a href="history_stock.php?id=<?php echo $IdTecnico;?>" class="waves-effect waves-light btn pink right "><i class="material-icons right">visibility</i>Historial</a><br>
              </p>
              <br>
            </li>
      </ul>
      <div class="row">
        <div class="col s2"></div>
        <div class="row col s8">
        <table class="bordered highlight responsive-table">
          <thead>
            <th>#</th>
            <th>Tipo</th>
            <th>Nombre</th>
            <th>Serie</th>
          </thead>
          <tbody>
          <?php
          $tab = mysqli_query($conn, "SELECT * FROM stock_tecnicos WHERE tipo IN ('Antena', 'Router') AND disponible = 0 AND tecnico = $IdTecnico");
          while($unidad = mysqli_fetch_array($tab)){
            ?>
            <tr>
              <td><?php echo $unidad['id']; ?></td>
              <td><?php echo $unidad['tipo']?></td>
              <td><?php echo $unidad['nombre']; ?></td>
              <td><?php echo $unidad['serie']; ?></td>
            </tr>
          <?php
          }
          ?> 
          </tbody>
        </table>
        </div>
    </div>
      <form class="col s12">
        <div class="input-field row col s12 m3 l3">
          <i class="col s1"> <br></i>
          <select id="tipo" class="browser-default col s11" required onchange="javascript:showContent()">
            <option value="0" selected>Tipo: </option>
            <option value="Antena">Antena</option>
            <option value="Router">Router</option>
            <option value="Bobina">Bobina Nueva</option>
            <option value="Tubo(s)">Tubo(s)</option>
          </select>
        </div>
        <div class="input-field row col s12 m7 l7" id="content" style="display: none;">
          <!--CONTENIDO PARA ANTENA-->
          <div class="input-field col s12 m6 l6">
            <i class="col s1"> <br></i>
            <select id="nombreA" class="browser-default col s11" required>
              <option value="0" selected>Nombre: </option>
              <option value="LiteBeam M5">LiteBeam M5</option>
              <option value="NanoBeam M2">NanoBeam M2</option>
              <option value="NanoBeam M5">NanoBeam M5</option>
              <option value="LiteBeam AC">LiteBeam AC</option>
            </select>
          </div> 
          <div class="input-field col s12 m5 l5">
            <input id="serieA" type="text" class="validate" data-length="100" required>
            <label for="serieA">Serie:</label>
          </div>        
        </div>
        <div class="input-field row col s12 m7 l7" id="content2" style="display: none;">
          <!--CONTENCIDO PARA ROUTER-->
          <div class="input-field col s12 m6 l6">
            <i class="col s1"> <br></i>
            <select id="nombreR" class="browser-default col s11" required>
              <option value="0" selected>Nombre: </option>
              <option value="Tp-Link">Tp-Link</option>
              <option value="TELMEX">TELMEX</option>
            </select>
          </div> 
          <div class="input-field col s12 m5 l5">
            <input id="serieR" type="text" class="validate" data-length="100" required>
            <label for="serieR">Serie:</label>
          </div>         
        </div>
        <div class="input-field row col s12 m7 l7" id="content3" style="display: none;">
          <!--CONTENIDO PARA BOBINA-->
          <div class="col s12 m6 l6">
          <p><br>
            <input type="checkbox" id="regreso"/>
            <label for="regreso">Regreso Bobina Anterior</label>
          </p>
        </div>        
        </div>
        <div class="input-field row col s12 m7 l7" id="content4" style="display: none;">
          <!--CONTENIDO PARA TUBOS-->
          <div class="input-field col s12 m6 l6">
            <input id="cantidad" type="text" class="validate" data-length="100" required>
            <label for="cantidad">Cantidad:</label>
          </div>        
        </div>
        <div class="row">
            <input id="tecnico" value="<?php echo htmlentities($IdTecnico);?>" type="hidden">
            <a onclick="update_stock();" class="waves-effect waves-light btn pink right"><i class="material-icons right">add</i>Agregar</a> <br>
      </div>  
    </form>     
