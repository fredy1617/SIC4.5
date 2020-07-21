<!DOCTYPE html>
<html lang="en">
<head>
<?php
  include('fredyNav.php');
  include ('../php/conexion.php');
$tecnico = $_SESSION['user_name'];
?>
<title>SIC | Alta Instalaciones</title>
<script>
function alta_instalacion_SM(bandera, contador) {
    textoTecnico = '<?php echo $tecnico;?>';
    textoApoyo = 0;
    for(var i=1;i<=bandera;i++){
      if(document.getElementById('tecnico'+i).checked==true){
        var textoApoyo = $("input#tecnico"+i).val();
      }
    } 
    textoTecnicos = textoTecnico+', '+textoApoyo;
    if (textoApoyo == 0) {
      textoTecnicos = textoTecnico
    }
    var textoIP = $("input#ip").val();    
    var textoObservacion = $("textarea#observacion").val();
    var textoIdCliente = $("input#id_cliente").val();
    var textoLiquidar = $("input#liquidar").val();
    var textoDireccion = $("input#direccion").val();
    var textoReferencia = $("input#referencia").val();
    var textoCoordenada = $("input#coordenada").val();
    var textoExtencion = $("input#tel_servicio").val();

     var textoAntena = $("select#antena").val();
    var textoRouter = $("select#router").val();
    var textoCable = $("input#cable").val();
    var textoTubos = $("input#tubos").val();
    var textoOtros = $("input#mas").val();
    if(document.getElementById('credito').checked==true){
      textoTipo_cambio   = "Credito";
    }else{
      textoTipo_cambio = "Efectivo";
    }
    var textoExtras = '';
    for(var j=1;j<=contador;j++){
      if(document.getElementById('extra'+j).checked==true){
        var textoCheck = $("input#extra"+j).val();
        textoExtras = textoExtras+textoCheck+', ';
      }
    } 
    textoExtras = textoExtras.slice(0, -2);

    if(textoOtros.length > 4){
      if (textoExtras == '') {
        textoExtras = textoOtros;
      }else{
        textoExtras = textoExtras+', '+textoOtros;
      }
    }

    if (textoIP == "") {
      M.toast({html:"El campo IP se encuentra vacío.", classes: "rounded"});
    }else if(textoTecnicos == ''){
      M.toast({html:"Seleccione un técnico por favor.", classes: "rounded"});
    }else if(textoCable == '' || textoCable == 0){
      M.toast({html:"Ingrese la cantidad de metros de cable que utilizo.", classes: "rounded"});
    }else{
      $.post("../php/alta_instalacion_SM.php", {
          valorIP: textoIP,
          valorObservacion: textoObservacion,
          valorIdCliente: textoIdCliente,
          valorTecnicos: textoTecnicos,
          valorLiquidar : textoLiquidar,
          valorTipo_Cambio: textoTipo_cambio,
          valorDireccion: textoDireccion,
          valorReferencia: textoReferencia,
          valorCoordenada: textoCoordenada,
          valorExtencion: textoExtencion,
          valorAntena: textoAntena,
          valorRouter: textoRouter,
          valorCable: textoCable,
          valorTubos: textoTubos,
          valorExtras: textoExtras
        }, function(mensaje) {
            $("#resultado_cliente").html(mensaje);
        });
    }
};

function alta_instalacion(bandera, contador) {
    textoTecnico = '<?php echo $tecnico;?>';
    textoApoyo = 0;
    for(var i=1;i<=bandera;i++){
      if(document.getElementById('tecnico'+i).checked==true){
        var textoApoyo = $("input#tecnico"+i).val();
      }
    } 
    textoTecnicos = textoTecnico+', '+textoApoyo;
    if (textoApoyo == 0) {
      textoTecnicos = textoTecnico
    }
    var textoIP = $("input#ip").val();    
    var textoObservacion = $("textarea#observacion").val();
    var textoIdCliente = $("input#id_cliente").val();
    var textoLiquidar = $("input#liquidar").val();
    var textoDireccion = $("input#direccion").val();
    var textoReferencia = $("input#referencia").val();
    var textoCoordenada = $("input#coordenada").val();
    var textoExtencion = $("input#tel_servicio").val();

    var textoAntena = $("select#antena").val();
    var textoRouter = $("select#router").val();
    var textoCable = $("input#cable").val();
    var textoTubos = $("input#tubos").val();
    var textoOtros = $("input#mas").val();
    if(document.getElementById('credito').checked==true){
      textoTipo_cambio   = "Credito";
    }else{
      textoTipo_cambio = "Efectivo";
    }
    var textoExtras = '';
    for(var j=1;j<=contador;j++){
      if(document.getElementById('extra'+j).checked==true){
        var textoCheck = $("input#extra"+j).val();
        textoExtras = textoExtras+textoCheck+', ';
      }
    } 
    textoExtras = textoExtras.slice(0, -2);

    if(textoOtros.length > 4){
      if (textoExtras == '') {
        textoExtras = textoOtros;
      }else{
        textoExtras = textoExtras+', '+textoOtros;
      }
    }

    if (textoIP == "") {
      M.toast({html:"El campo IP se encuentra vacío.", classes: "rounded"});
    }else if(textoTecnicos == ''){
      M.toast({html:"Seleccione un técnico por favor.", classes: "rounded"});
    }else if(textoAntena == 0){
      M.toast({html:"Seleccione una antena.", classes: "rounded"});
    }else if(textoCable == '' || textoCable == 0){
      M.toast({html:"Ingrese la cantidad de metros de cable que utilizo.", classes: "rounded"});
    }else if(textoRouter == 0){
      M.toast({html:"Seleccione un router.", classes: "rounded"});
    }else{
      $.post("../php/alta_instalacion.php", {
          valorIP: textoIP,
          valorObservacion: textoObservacion,
          valorIdCliente: textoIdCliente,
          valorTecnicos: textoTecnicos,
          valorLiquidar : textoLiquidar,
          valorTipo_Cambio: textoTipo_cambio,
          valorDireccion: textoDireccion,
          valorReferencia: textoReferencia,
          valorCoordenada: textoCoordenada,
          valorExtencion: textoExtencion,
          valorAntena: textoAntena,
          valorRouter: textoRouter,
          valorCable: textoCable,
          valorTubos: textoTubos,
          valorExtras: textoExtras
        }, function(mensaje) {
            $("#resultado_cliente").html(mensaje);
        }); 
    }
};
</script>
</head>
<?php
if (isset($_POST['id_cliente']) == false) {
  ?>
  <script>
    function atras(){
      M.toast({html: "Regresando a instalaciones pendientes", classes: "rounded"});
      setTimeout("location.href='instalaciones.php'",1000);
    }
    atras();
  </script>
  <?php
}else{
  ?>
<body>
<div class="container" id="resultado_cliente">
</div>
	<div class="container row">
    	<?php
        $id_cliente = $_POST['id_cliente'];
        $datos = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM clientes WHERE id_cliente=$id_cliente"));

        $id_comunidad = $datos['lugar'];
        $comunidad = mysqli_fetch_array(mysqli_query($conn, "SELECT nombre FROM comunidades WHERE id_comunidad=$id_comunidad"));

        $id_paquete = $datos['paquete'];
        $paquete = mysqli_fetch_array(mysqli_query($conn, "SELECT subida, bajada FROM paquetes WHERE id_paquete=$id_paquete"));
        ?>
      <div class="row" >
      <h3 class="hide-on-med-and-down">Dar de Alta a Cliente</h3>
      <h5 class="hide-on-large-only">Dar de Alta a Cliente</h5>
      </div>
        <ul class="collection">
            <li class="collection-item avatar">
              <img src="../img/cliente.png" alt="" class="circle">
              <span class="title"><b>No. Cliente: </b><?php echo $datos['id_cliente'];?></span>
              <p><b>Nombre(s): </b><?php echo $datos['nombre'];?><br>
                <b>Telefono: </b><?php echo $datos['telefono'];?><br>
                 <b>Comunidad: </b><?php echo $comunidad['nombre'];?><br>
                 <div class="col s12">
                    <b class="col s4 m2 l2">Direccion: </b>
                    <div class="col s12 m9 l9">
                      <input id="direccion" type="text" class="validate" value="<?php echo $datos['direccion'];?>">
                    </div>
                  </div>
                  <div class="col s12">
                    <b class="col s4 m2 l2">Referencia: </b>
                    <div class="col s12 m9 l9">
                      <input id="referencia" type="text" class="validate" value="<?php echo $datos['referencia'];?>">
                    </div>
                  </div>
                  <b>Paquete: </b> Subida: <?php echo $paquete['subida'];?> | Bajada: <?php echo $paquete['bajada'];?><br>
                 <b>Total: </b>$<?php echo $datos['total'];?><br>
                 <b>Dejó: </b>$<?php echo $datos['dejo'];?><br>
                 <b>Resta: </b>$<?php echo $datos['total']-$datos['dejo'];?><br>
                 <span class="new badge pink hide-on-med-and-up" data-badge-caption="INACTIVO"></span>               
              </p>
              <a href="#!" class="secondary-content hide-on-small-only"><span class="new badge pink" data-badge-caption="INACTIVO"></span></a>
            </li>
        </ul>
        <div class="row">
        <form class="col s12">
          <div class="row">
            <div class="col s12 m6 l6">
            <br>
            <div class="input-field">
              <i class="material-icons prefix">settings_ethernet</i>
              <input id="ip" type="text" class="validate" data-length="15" required>
              <label for="ip">IP:</label>
            </div>
            <div class="input-field">
              <i class="material-icons prefix">add_location</i>
              <input id="coordenada" type="text" class="validate" data-length="15" required>
              <label for="coordenada">Coordenada:</label>
            </div>
            <div class="input-field">
              <i class="material-icons prefix">comment</i>
              <textarea id="observacion" class="materialize-textarea validate" data-length="150" required></textarea>
              <label for="observacion">Observacion Tecnica:</label>
            </div>
            <h5 class="hide-on-med-and-down">Material:</h5>
            
            </div>
            <!-- AQUI SE ENCUENTRA LA DOBLE COLUMNA EN ESCRITORIO.-->
            <div class="col s12 m6 l6"><br>
            <div class="row">
            <div class="input-field col s8 m9 l9">
              <i class="material-icons prefix">local_atm</i>
              <input id="liquidar" type="number" class="validate" data-length="6" required value="<?php echo $datos['total']-$datos['dejo'];?>">
              <label for="liquidar">Liquidar:</label>
            </div>
            <div class="col s4 m3 l3">
              <p>
                <br>
                <input type="checkbox" id="credito"/>
                <label for="credito">Credito</label>
              </p>
            </div>
            </div>
            <input id="id_cliente" type="hidden" class="validate" data-length="200" value="<?php echo $datos['id_cliente'];?>" required>
            
            <div class="input-field">
              <i class="material-icons prefix">phone</i>
              <input id="tel_servicio" type="text" class="validate" data-length="15" required>
              <label for="tel_servicio">Telefono Servicio:</label>
            </div><br>
            <label>APOYO (solo seleccionar uno):</label>
                <p>
                  <?php
                  $bandera = 1; 
                  $sql_tecnico = mysqli_query($conn,"SELECT * FROM users WHERE area='Taller' OR area='Redes'  OR user_id = 49 OR user_id = 28 OR user_id = 25");
                  while($tecnico = mysqli_fetch_array($sql_tecnico)){
                    ?>
                    <div class="col s12 m6 l4">
                      <input type="checkbox" value="<?php echo $tecnico['user_name'];?>" id="tecnico<?php echo $bandera;?>"/>
                      <label for="tecnico<?php echo $bandera;?>"><?php echo $tecnico['user_name'];?></label>
                    </div>
                    <?php
                    $bandera++;
                  }$bandera--;
                  ?>
                </p>
              </div>
              </div>
              <div class="row">
          <?php 
            $contador = 1;
            $id_tecnico = $_SESSION['user_id'];?>
                <div class="col s12 m6 l6">
                  <div class="row">
                  <div class="input-field col s12 m6 l6">
                    <i class="material-icons col s2">satellite<br></i>
                      <select id="antena" class="browser-default col s10" required>
                        <option value="0" selected >Antena:</option>
                        <option value="N/A">Ninguna</option>
                        <?php
                            $sql = mysqli_query($conn,"SELECT * FROM stock_tecnicos WHERE tipo = 'Antena' AND disponible = 0 AND tecnico = $id_tecnico");
                            while($antena = mysqli_fetch_array($sql)){
                              ?>
                                <option value="<?php echo $antena['serie'];?>"><?php echo $antena['nombre'];?> (Serie: <?php echo $antena['serie'];?>)</option>
                              <?php
                            } 
                        ?>
                      </select>
                  </div>
                  <div class="input-field col s12 m6 l6">
                    <i class="material-icons col s2">router<br></i>
                      <select id="router" class="browser-default col s10" required>
                        <option value="0" selected >Router:</option>
                        <option value="N/A">Ninguno</option>
                        <?php
                            $sql = mysqli_query($conn,"SELECT * FROM stock_tecnicos WHERE tipo = 'Router' AND disponible = 0 AND tecnico = $id_tecnico");
                            while($router = mysqli_fetch_array($sql)){
                              ?>
                                <option value="<?php echo $router['serie'];?>"><?php echo $router['nombre'];?> (Serie: <?php echo $router['serie'];?>)</option>
                              <?php
                            } 
                        ?>
                      </select>
                  </div>
                  </div>
                  <div class="input-field">
                    <i class="material-icons prefix">pan_tool</i>
                    <input id="mano_obra" type="text" class="validate" data-length="15" required>
                    <label for="mano_obra">Mano de obra:</label>
                  </div>
                  <div class="input-field">
                    <i class="material-icons prefix">add</i>
                    <input id="mas" type="text" class="validate" data-length="15" required>
                    <label for="mas">Otros ¿Cuales? (ej: 3 Camaras, 1 Grabador, etc.):</label>
                  </div>
                </div>
                
                <!-- AQUI SE ENCUENTRA LA DOBLE COLUMNA EN ESCRITORIO.-->
                <div class="col s12 m6 l6">
                  <?php
                  $bobina = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM stock_tecnicos WHERE disponible = 0 AND tecnico = $id_tecnico  AND tipo = 'Bobina'"));
                  $CantidadB = $bobina['cantidad']-$bobina['uso'];
                  $totalC = mysqli_fetch_array(mysqli_query($conn, "SELECT SUM(cantidad) AS total FROM stock_tecnicos WHERE disponible = 0 AND tecnico = $id_tecnico  AND tipo = 'Tubo(s)'"));
                  $totalU = mysqli_fetch_array(mysqli_query($conn, "SELECT SUM(uso) AS total FROM stock_tecnicos WHERE disponible = 0 AND tecnico = $id_tecnico  AND tipo = 'Tubo(s)'"));
                  $Tubos = $totalC['total']-$totalU['total'];
                  ?>
                  <div class="input-field col s12 m6 l6">
                    <i class="material-icons prefix">settings_input_hdmi</i>
                    <input id="cable" type="number" class="validate" data-length="15" required>
                    <label for="cable">Cable Red (metros) <?php echo $CantidadB;?>:</label>
                  </div>
                  <div class="input-field col s12 m6 l6">
                    <i class="material-icons prefix">priority_high</i>
                    <input id="tubos" type="number" class="validate" data-length="15" value="0" required>
                    <label for="tubos">Tubos (piezas) <?php echo $Tubos;?>:</label>
                  </div>
                  <label>Extras:</label>
                  <p>
                    <?php 
                    $ven = array("Alambre","Taquetes","Conectores", "Tornillos","Grapas","Cinta","Clavos");
                      while ($contador < count($ven)) {
                      ?>
                      <div class="col s12 m6 l4">
                        <input type="checkbox" value="<?php echo $ven[$contador];?>" id="extra<?php echo $contador;?>"/>
                        <label for="extra<?php echo $contador;?>"><?php echo $ven[$contador];?></label>
                      </div>
                      <?php
                      $contador++;
                    }$contador--;
                    ?>
                  </p>
                </div>
        </div> 
        </form>
        </div>
        <div class="row">
          <a onclick="alta_instalacion(<?php echo $bandera;?>, <?php echo $contador;?>);" class="waves-effect waves-light btn pink right"><i class="material-icons right">send</i>ALTA SERVIDOR</a>
          <br><br>
          <a onclick="alta_instalacion_SM(<?php echo $bandera;?>, <?php echo $contador;?>);" class="waves-effect waves-light btn pink right"><i class="material-icons right">send</i> ALTA MANUAL. </a>          
        </div>
    </div>
    <?php
    mysqli_close($conn);
    ?>
</body>
<?php
}
?>
</html>