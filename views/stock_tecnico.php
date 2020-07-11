<!DOCTYPE html>
<html>
<head>
  <title>SIC | Stock</title>
</head>
<?php
include ('fredyNav.php');
include('../php/conexion.php');
include('../php/cobrador.php');
if (isset($_POST['id_tecnico']) == false) {
  ?>
  <script>    
    function atras() {
      M.toast({html: "Regresando a listado tecnicos...", classes: "rounded"})
      setTimeout("location.href='stock.php'", 1000);
    }
    atras();
  </script>
  <?php
}else{
  $id_tecnico = $_POST['id_tecnico'];
?>
<script>
  function showContent() {
    element = document.getElementById("content");
    element2 = document.getElementById("content2");
    element3 = document.getElementById("content3");
    element4 = document.getElementById("content4");
    var textoTipo = $("select#tipo").val();

    if (textoTipo == 'Antena') {
      element.style.display='block';
    }  else {
      element.style.display='none';
    }
    if (textoTipo == 'Router') {
      element2.style.display='block';
    }  else {
      element2.style.display='none';
    }
    if (textoTipo == 'Bobina') {
      element3.style.display='block';
    }  else {
      element3.style.display='none';
    }
    if (textoTipo == 'Tubo(s)') {
      element4.style.display='block';
    }  else {
      element4.style.display='none';
    }
        
  };
function update_stock() {
    var textoTipo = $("select#tipo").val();
    var textoIdTecnico = $("input#tecnico").val();

    if (textoTipo == 'Antena') {
      var textoNombre = $("select#nombreA").val();
      var textoSerie = $("input#serieA").val();
      textoCantidad = 1;
    }else if (textoTipo == 'Router') {
      var textoNombre = $("select#nombreR").val();
      var textoSerie = $("input#serieR").val();
      textoCantidad = 1;
    }else if (textoTipo == 'Bobina') {
      textoNombre = 'Bobina Nueva';
      textoSerie = '111111';
      textoCantidad = 300;
    }else if (textoTipo == 'Tubo(s)') {
      textoNombre = 'Tubos';
      textoSerie = '222222';
      var textoCantidad = $("input#cantidad").val();
    }

    if(document.getElementById('regreso').checked==true){
      textoRegreso = 'Si';
    }else{
      textoRegreso = 'No';
    }

    if(textoTipo == 0){
      M.toast({html:"Elige un tipo para agregar...", classes: "rounded"})
    }else if(textoNombre == 0){
      M.toast({html:"Elige un nombre...", classes: "rounded"})
    }else if(textoSerie == ""){
      M.toast({html:"Ingrese la serie...", classes: "rounded"})
    }else if(textoCantidad == "" || textoCantidad <= 0){
      M.toast({html:"Ingrese un valor correcto en cantidad", classes: "rounded"})
    }else{
      $.post("../php/inster_to_stock.php", {
          valorIdTecnico: textoIdTecnico,
          valorTipo: textoTipo,
          valorNombre: textoNombre,
          valorSerie: textoSerie,
          valorCantidad: textoCantidad,
          valorRegreso: textoRegreso
      }, function(mensaje) {
          $("#resultado_update_stock").html(mensaje);
      });
    }
};
</script>
<body>
  <div class="container" id="resultado_update_stock">
    <div class="row">
        <h3 class="hide-on-med-and-down">Stock:</h3>
        <h5 class="hide-on-large-only">Stock:</h5>
      </div>
    <?php   
      $datos = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM users WHERE user_id = $id_tecnico"));
      $Antenas = mysqli_fetch_array(mysqli_query($conn, "SELECT SUM(cantidad) AS total FROM stock_tecnicos WHERE disponible = 0 AND tecnico = $id_tecnico  AND tipo = 'Antena'"));
      $Routers = mysqli_fetch_array(mysqli_query($conn, "SELECT SUM(cantidad) AS total FROM stock_tecnicos WHERE disponible = 0 AND tecnico = $id_tecnico  AND tipo = 'Router'"));
      $bobina = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM stock_tecnicos WHERE disponible = 0 AND tecnico = $id_tecnico  AND tipo = 'Bobina'"));
      $CantidadB = $bobina['cantidad']-$bobina['uso'];
      $totalC = mysqli_fetch_array(mysqli_query($conn, "SELECT SUM(cantidad) AS total FROM stock_tecnicos WHERE disponible = 0 AND tecnico = $id_tecnico  AND tipo = 'Tubo(s)'"));
      $totalU = mysqli_fetch_array(mysqli_query($conn, "SELECT SUM(uso) AS total FROM stock_tecnicos WHERE disponible = 0 AND tecnico = $id_tecnico  AND tipo = 'Tubo(s)'"));
      $Tubos = $totalC['total']-$totalU['total'];
    ?>
      <div class="row">
      <ul class="collection">
            <li class="collection-item avatar">
              <div class="hide-on-large-only"><br><br></div>
              <img src="../img/cliente.png" alt="" class="circle">
              <span class="title"><b>Id: </b><?php echo $id_tecnico;?></span>
              <p><b>Nombre: </b><?php echo $datos['firstname'].' '.$datos['lastname'];?><br>
                <b>Antenas: <?php echo $Antenas['total'];?></b><br>                  
                <b>Routers: <?php echo $Routers['total'];?></b><br>                  
                <b>Metros de bobina: <?php echo $CantidadB;?></b><br>                  
                <b>Tubos: <?php echo $Tubos;?></b><br>
                <hr>
                <a href="history_stock.php?id=<?php echo $id_tecnico;?>" class="waves-effect waves-light btn pink right "><i class="material-icons right">visibility</i>Historial</a><br>
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
          $tab = mysqli_query($conn, "SELECT * FROM stock_tecnicos WHERE tipo IN ('Antena', 'Router') AND disponible = 0 AND tecnico = $id_tecnico");
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
        <div class="input-field row col s12 m7 l7" id="content2" style="display: none;">
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
            <input id="tecnico" value="<?php echo htmlentities($id_tecnico);?>" type="hidden">
            <a onclick="update_stock();" class="waves-effect waves-light btn pink right"><i class="material-icons right">add</i>Agregar</a> <br>
      </div>  
    </form>     
    </div>
</body>
<?php
}
mysqli_close($conn);
?>
</script>
</html>