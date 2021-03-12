<?php
#DEFINIMOS UNA ZONA HORARIA
date_default_timezone_set('America/Mexico_City');
#INCLUIMOS LA CONEXION A LA BASE DE DATOS PARA PODER HACER CUALQUIER MODIFICACION, INSERCION O SELECCION
include('../php/conexion.php');
#TOMAMOS EL ARCHIVO DONDE ESTA LA INFOMACION DEL INICIO SE SECCION AL SISTEMA
include('is_logged.php');
#TOMAMOS EL ID DEL USUARION LOGEADO
$Registro = $_SESSION['user_id'];
#GENERAMOS UNA FECHA DEL DIA EN CURSO REFERENTE A LA ZONA HORARIA
$Fecha_hoy = date('Y-m-d');

#RECIBIMOS LOS VALORES QUE SE NOS ENVIA DESDE EL FORMULARIO DEL A VISTA STOCK (PARA INSERTAR)
$IdTecnico = $conn->real_escape_string($_POST['valorIdTecnico']);
$Tipo = $conn->real_escape_string($_POST['valorTipo']);#TIPO DE MATERIAL ANTENA, ROUTER, TUBOS, ETC.
$Nombre = $conn->real_escape_string($_POST['valorNombre']);#NOMBRE MIMOSA, TP-LINK, ETC.
$Serie = $conn->real_escape_string($_POST['valorSerie']);#NUMERO DE SERIE EN CASO DE SER ANTENA O ROUTER BIENEN EN LA CAJA
$Cantidad = $conn->real_escape_string($_POST['valorCantidad']);#CANTIDAD A REGISTRAR DEL MATERIAL
$Regreso = $conn->real_escape_string($_POST['valorRegreso']);
$Ruta = $conn->real_escape_string($_POST['valorRuta']);#EN QUE RUTA PIDIO DICHO MATERIAL PARA MEJOR CONTROL


#COMPARAREMOS SI EL MATERIAL A INSRTAR ES UNA ANTENA O UN ROUTER Y SI LA SERIE YA SE ENCUENTRA REGISTRADA EN LA TABLA stock_tecnicos
if (mysqli_num_rows(mysqli_query($conn, "SELECT * FROM stock_tecnicos WHERE serie = '$Serie' AND (tipo = 'Router' OR tipo = 'Antena')"))>0) {
  #SI YA SE ENCUENTRA REGISTRARA UNA SERIE IGUAL EN UNA ANTENA O ROUTER REGRESARA UNA ALERTA Y NO SE HARA LA ISERCION
  echo "<script>M.toast({html: 'Ya se encuentra esta serie registrada en el stock.', classes: 'rounded'})</script>";
}else{
  #COMPARAMOS SI EL VALOR DE $Regreso == 'Si' QUIERE DECIR QUE REGRESO UN CARRETE Y AUTOMATICAMENTE LA BOBINA ANTERIOR SE PONE COMO USADA Y SE AGREGA UNA NIEVA CON 300 mts
  if ($Regreso == 'Si') {
    #MODIFICAMOS LA BOBINA ANTERIOR 
    if (mysqli_query($conn, "UPDATE stock_tecnicos SET uso = 300, fecha_salida = '$Fecha_hoy', disponible = 1  WHERE uso < 300 AND tecnico = $IdTecnico AND disponible = 0 AND tipo = 'Bobina'")){
      #SI SE MODIFICA SIN NINGUN ERROR MANDARA UNA ALERTA
      echo '<script>M.toast({html:"Se actualizo la Bobina...", classes: "rounded"})</script>';
    }else{
      #SI NO SE HACE LA MODIFICACION DE LA BOBINA ANTERIOR LANZARA UNA ALERTA DE ERROR
      echo '<script>M.toast({html:"Ocurrio un error al actualizar la Bobina...", classes: "rounded"})</script>';
    }
  }
  #CREAMOS EL SQL CON EL CUAL SE HARA LA INSERCION DEL MATERIAL
  $sql = "INSERT INTO stock_tecnicos (nombre, serie, tipo, cantidad, fecha_alta, registro, tecnico, ruta) VALUES('$Nombre', '$Serie', '$Tipo', '$Cantidad', '$Fecha_hoy', '$Registro', '$IdTecnico','$Ruta')";
  #AQUI COMPARAMOS Y COMPROBAMOS SI SE HACE LA INSERCION EN LA BASE DE DATOS
  if(mysqli_query($conn, $sql)){
    #SI SE INSERTO CORRECTAMENTE MANDARA UNA ALERTA DE EXITO!
    echo '<script>M.toast({html:"El material se dió de alta satisfcatoriamente.", classes: "rounded"})</script>';
  }else{
    #SI NO SE INSERTA EL MATERIAL EN LA BD MANDARA UNA ALERTA DE ERROR
    echo '<script>M.toast({html:"Ha ocurrido un error al insertar.", classes: "rounded"})</script>';
  }
}
?>
    <div class="row">
        <h3 class="hide-on-med-and-down">Stock:</h3>
        <h5 class="hide-on-large-only">Stock:</h5>
      </div>
    <?php   
      #HACEMOS LA SELECCION DE LA INFORMACION NESESARIA PARA MOSTRAR EN LA VISTA
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
      <!-- UL RECUADRO CON LA INFORMACION DEL STOCK POR TECNICO --> 
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
            <th>Ruta</th>
          </thead>
          <tbody>
          <?php
          $tab = mysqli_query($conn, "SELECT * FROM stock_tecnicos WHERE tipo IN ('Antena', 'Router') AND disponible = 0 AND tecnico = $IdTecnico AND ruta IS NOT NULL");
          while($unidad = mysqli_fetch_array($tab)){
            ?>
            <tr>
              <td><?php echo $unidad['id']; ?></td>
              <td><?php echo $unidad['tipo']?></td>
              <td><?php echo $unidad['nombre']; ?></td>
              <td><?php echo $unidad['serie']; ?></td>
              <td><?php echo $unidad['ruta']; ?></td>
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
        <div class="input-field row col s12 m6 l6" id="content" style="display: none;">
          <!--CONTENIDO PARA ANTENA-->
          <div class="input-field col s12 m6 l6">
            <i class="col s1"> <br></i>
            <select id="nombreA" class="browser-default col s11" required>
              <option value="0" selected>Nombre: </option>
              <option value="LiteBeam M5">LiteBeam M5</option>
              <option value="NanoBeam M2">NanoBeam M2</option>
              <option value="NanoBeam M5">NanoBeam M5</option>
              <option value="LiteBeam AC">LiteBeam AC</option>
              <option value="PowerBeam AC">PowerBeam AC</option>
              <option value="PowerBeam M5">PowerBeam M5</option>
              <option value="PowerBeam M2">PowerBeam M2</option>
              <option value="NanoStation AC">NanoStation AC</option>
              <option value="NanoStation M2">NanoStation M2</option>
              <option value="NanoStation M5">NanoStation M5</option>
              <option value="Rocket M2">Rocket M2</option>
              <option value="Rocket M5">Rocket M5</option>
              <option value="Rocket AC">Rocket AC</option>
              <option value="Rocket AC Prism">Rocket AC Prism</option>
              <option value="MIMOSA B5C">MIMOSA B5C</option>
              <option value="MIMOSA C5C">MIMOSA C5C</option>
              <option value="Cambium ePMP">Cambium ePMP</option>
              <option value="Cambium ePMP Force">Cambium ePMP Force</option>
            </select>
          </div> 
          <div class="input-field col s12 m5 l5">
            <input id="serieA" type="text" class="validate" data-length="100" required>
            <label for="serieA">Serie:</label>
          </div>        
        </div>
        <div class="input-field row col s12 m6 l6" id="content2" style="display: none;">
          <!--CONTENCIDO PARA ROUTER-->
          <div class="input-field col s12 m6 l6">
            <i class="col s1"> <br></i>
            <select id="nombreR" class="browser-default col s11" required>
              <option value="0" selected>Nombre: </option>
              <option value="Tp-Link">Tp-Link</option>
              <option value="TELMEX">TELMEX</option>
              <option value="Tenda">Tenda</option>
              <option value="Mercusys">Mercusys</option>
            </select>
          </div> 
          <div class="input-field col s12 m5 l5">
            <input id="serieR" type="text" class="validate" data-length="100" required>
            <label for="serieR">Serie:</label>
          </div>         
        </div>
        <div class="input-field row col s12 m6 l6" id="content3" style="display: none;">
          <!--CONTENIDO PARA BOBINA-->
          <div class="col s12 m6 l6">
          <p><br>
            <input type="checkbox" id="regreso"/>
            <label for="regreso">Regreso Bobina Anterior</label>
          </p>
        </div>        
        </div>
        <div class="input-field row col s12 m6 l6" id="content4" style="display: none;">
          <!--CONTENIDO PARA TUBOS-->
          <div class="input-field col s12 m6 l6">
            <input id="cantidad" type="number" class="validate" data-length="100" required>
            <label for="cantidad">Cantidad:</label>
          </div>        
        </div>
        <div class="input-field row col s12 m3 l3">
          <!--CONTENIDO ID RUTA-->
          <div class="input-field col s12">
            <input id="ruta" type="number" class="validate" data-length="100" required>
            <label for="ruta">Ruta:</label>
          </div>        
        </div>
        <div class="row">
            <input id="tecnico" value="<?php echo htmlentities($IdTecnico);?>" type="hidden">
            <a onclick="update_stock();" class="waves-effect waves-light btn pink right"><i class="material-icons right">add</i>Agregar</a> <br>
      </div>  
    </form>     
