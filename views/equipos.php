<html>
<head>
  <title>SIC | Equipos Central</title>
</head>
<?php 
include('fredyNav.php');
include('../php/conexion.php');
date_default_timezone_set('America/Mexico_City');
$Fecha_Hoy = date('Y-m-d');
if (isset($_GET['id']) == false) {
  ?>
  <script>    
    function atras() {
      M.toast({html: "Regresando a centrales.", classes: "rounded"})
      setTimeout("location.href='centrales.php'", 800);
    };
    atras();
  </script>
  <?php
}else{
$id_central = $_GET['id'];
?>
<script>   
function insert_equipo() {  
    var textoNombre = $("input#nombre").val();
    var textoMarca = $("input#marca").val();
    var textoModelo = $("input#modelo").val();
    var textoIP = $("input#ip").val();
    var textoDescripcion = $("textarea#descripcion").val();
    var textoInstalacion = $("input#instalacion").val();
    var textoModificacion = $("input#modificacion").val();
    var textoIdCentral = $("input#id_central").val();

    if (textoNombre == "") {
      M.toast({html: 'Ingrese un nombre para el equipo.', classes: 'rounded'});
    }else if (textoMarca == "") {
        M.toast({html: 'Ingrese una marca para el equipo.', classes: 'rounded'});
    }else if (textoModelo == "") {
        M.toast({html: 'Ingrese un modelo para el equipo.', classes: 'rounded'});
    }else if (textoDescripcion == "") {
        M.toast({html: 'Ingrese una descripcion para el equipo.', classes: 'rounded'});
    }else if (textoInstalacion == "") {
        M.toast({html: 'Ingrese una fecha de intalacion del equipo.', classes: 'rounded'});
    }else {
        $.post("../php/insert_equipo_central.php" , { 
            valorNombre: textoNombre,
            valorMarca: textoMarca,
            valorModelo: textoModelo,
            valorIP: textoIP,
            valorInstalacion: textoInstalacion,
            valorDescripcion: textoDescripcion,
            valorModificacion: textoModificacion,
            valorIdCentral: textoIdCentral
          }, function(mensaje) {
              $("#mostrar_equipos").html(mensaje);
          });  
    }    
};
</script>
<body>
<?php
$sql = "SELECT * FROM centrales WHERE id=$id_central";
$datos = mysqli_fetch_array(mysqli_query($conn, $sql));
//Sacamos la Comunidad
$id_comunidad = $datos['comunidad'];
$comunidad = mysqli_fetch_array(mysqli_query($conn, "SELECT nombre FROM comunidades WHERE id_comunidad='$id_comunidad'"));
?>
<div class="container">
  <h3 class="hide-on-med-and-down">Registrar equipos de la central:</h3>
  <h5 class="hide-on-large-only">Registrar equipos de la central:</h5>
  <ul class="collection">
    <li class="collection-item avatar">
      <img src="../img/cliente.png" alt="" class="circle">
      <span class="title"><b>No. Central: </b><?php echo $datos['id'];?></span>
      <p><b>Encargado: </b><?php echo $datos['nombre'];?><br>
         <b>Telefono: </b><?php echo $datos['telefono'];?><br>
         <b>Comunidad: </b><?php echo $comunidad['nombre'];?><br>
         <b>Dirección: </b><?php echo $datos['direccion'];?><br>
         <b>Coordenada: </b><?php echo $datos['coordenadas'];?><br>
         <b>Fecha Vencimineto de Renta: </b><?php echo $datos['vencimiento_renta'];?><br>
         <b>Descripcion General: </b><?php echo $datos['descripcion_gral'];?><br>
      </p>
    </li>
  </ul> 
<div id="imprimir"></div><br>
<div class="row">
  <div class="col s12">
    <div class="row">
      <form method="post" action="../views/central.php"><input name="id_central" type="hidden" value="<?php echo $id_central; ?>"><button type="submit" class="waves-effect waves-light btn pink right waves-effect waves-light pink"><i class="material-icons right">visibility</i>VER PAGOS</button></form>
        
<!-- ----------------------------  PAGOS   ---------------------------------------->
      <div class="col s12 m12 l12">
        <h4 class="pink-text "><< Nuevo equipo: >></h4>
        <form class="row" name="formMensualidad"><br>
          <div class="input-field col s12 m6 l6">
            <i class="material-icons prefix">phonelink</i>
            <input id="nombre" type="text" class="validate" data-length="6" required>
            <label for="nombre">Nombre :</label>
          </div>
          <div class="input-field col s12 m6 l6">
            <i class="material-icons prefix">local_offer</i>
            <input id="marca" type="text" class="validate" data-length="6" required>
            <label for="marca">Marca :</label>
          </div><div class="input-field col s12 m6 l6">
            <i class="material-icons prefix">confirmation_number</i>
            <input id="modelo" type="text" class="validate" data-length="6" required>
            <label for="modelo">Modelo :</label>
          </div>
          <div class="input-field col s12 m6 l6">
            <i class="material-icons prefix">settings_ethernet</i>
            <input id="ip" type="text" class="validate" data-length="6" required>
            <label for="ip">IP :</label>
          </div>
          <div class="input-field col s12 m6 l6">
            <i class="material-icons prefix">comment</i>
            <textarea id="descripcion" class="
           materialize-textarea validate" data-length="250" required ></textarea>
            <label for="descripcion">Descripción: (ej: Puerto 1 conectada a la antena 3, La torre tiene paneles solares de 25v con regulador, etc.)</label>
          </div>
          <div class="col s6 l3 m3">
                <label for="instalacion">Fecha Instalación:</label>
                <input id="instalacion" type="date" >    
          </div>
          <div class="col s6 l3 m3">
                <label for="modificacion">Ultima Modificación:</label>
                <input id="modificacion" type="date" >    
          </div>
          <input id="id_central" value="<?php echo htmlentities($datos['id']);?>" type="hidden">
        </form>
        <a onclick="insert_equipo();" class="waves-effect waves-light btn pink right "><i class="material-icons right">send</i>Registrar equipo</a><br>
        <h4>Equipos: </h4>
       <div id="mostrar_equipos">
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
          $sql_equipos = "SELECT * FROM equipos WHERE id_central = ".$datos['id']." ORDER BY id DESC";
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
       </div>
      </div>

    </div>
  </div>
</div>

</div><!----------------CONTAINER----------------->
</body>
<?php } ?>
</html>