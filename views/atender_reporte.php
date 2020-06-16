<html>
<head>
	<title>SIC | Atender Reporte</title>
</head>
<?php 
include('../views/fredyNav.php');
include('../php/conexion.php');
if (isset($_POST['id_reporte']) == false) {
  ?>
  <script>
    function atras(){
      M.toast({html: "Regresando a reportes pendientes", classes: "rounded"});
      setTimeout("location.href='reportes.php'",1000);
    }
    atras();
  </script>
  <?php
}else{
$tecnico = $_SESSION['user_id'];
date_default_timezone_set('America/Mexico_City');
$Fecha_Hoy = date('Y-m-d');
$id_reporte = $_POST['id_reporte'];
$resultado = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM reportes WHERE id_reporte = $id_reporte"));
$id_cliente = $resultado['id_cliente'];
?>

<script>
function encender(){
  if(document.getElementById('enciende').checked==true){
    textoOrden = "Encender";  
  }else{    
    textoOrden = "Apagar";
  }
  textoIdCliente = <?php echo $id_cliente; ?>;
  $.post("../php/enciende_apaga.php", { 
          valorOrden: textoOrden,
          valorCliente:textoIdCliente,
  }, function(mensaje) {
  $("#Orden").html(mensaje);
  }); 
}
function update_reporte(bandera, contador) {
    var textoNombre = $("input#nombres").val();
    var textoTelefono = $("input#telefono").val();
    var textoDireccion = $("input#direccion").val();
    var textoReferencia = $("input#referencia").val();
    var textoIdReporte = $("input#id_reporte").val();
    var textoFalla = $("textarea#falla").val();
    var textoSolucion = $("textarea#solucion").val();
    var textoAtendido = $("select#atendido").val();
    var textoFecha = $("input#fecha_visita").val();
    var textoIdCliente = $("input#id_cliente").val();

    entra = "Si";
    textoTecnico = <?php echo $tecnico;?>;
    textoApoyo = 0;
    for(var i=1;i<=bandera;i++){
      if(document.getElementById('tecnico'+i).checked==true){
        var textoApoyo = $("input#tecnico"+i).val();
      }
    }  
    Campo_var = <?php echo $resultado['campo'];?>;
    if (Campo_var == 1) {
        var textoAntena = $("input#antena").val();
        var textoRouter = $("input#router").val();
        var textoCable = $("input#cable").val();
        var textoTubos = $("input#tubos").val();
        var textoOtros = $("input#mas").val();
        var textoCosto = $("input#costo").val();
        var textoManoObra = $("input#mano_obra").val();

        if(document.getElementById('reemplazo').checked==true){
          textoTipo = 'Reemplazo';
        }else{ textoTipo = 'Nuevo'; } 

        var textoExtras = '';
        for(var j=1;j<=contador;j++){
          if(document.getElementById('extra'+j).checked==true){
            var textoCheck = $("input#extra"+j).val();
            textoExtras = textoExtras+textoCheck+', ';
          }
        } 
        textoExtras = textoExtras.slice(0, -2);

        if(document.getElementById('otros').checked==true){
          if (textoExtras == '') {
            textoExtras = textoOtros;
          }else{
            textoExtras = textoExtras+', '+textoOtros;
          }
        }
        if(document.getElementById('credito').checked==true){
          textoTipo_cambio   = "Credito";
        }else{
          textoTipo_cambio = "Efectivo";
        }
    }else{
        textoAntena = '';
        textoRouter = '';
        textoCable = '';
        textoTubos = '';
        textoBobina = '';
        textoExtras = '';
        textoTipo = '';
        textoCosto = '';
        textoManoObra = '';
        textoTipo_cambio = '';

    }

    if(document.getElementById('campo').checked==true){
      textoCampo = 1;
    }else{
      textoCampo = 0;
    }

    if(document.getElementById('visita').checked==true){
      if (textoFecha == "") {
        entra = "No";
        M.toast({html:"El campo Fecha no puede ir vacío.",classes: "rounded"}); }
      if (textoTecnico == "") {
        entra = "No";
        M.toast({html:"El campo no puede ir Sin Tecnico.",classes: "rounded"});}
    }
    if (textoFecha == "") {textoFecha = 0}

    if (textoFalla == "" ) {
      M.toast({html:"El campo Falla no puede ir vacío.",classes: "rounded"});
    }else{ if ( entra == "Si") {
      $.post("../php/update_reporte.php", {
          valorIdCliente: textoIdCliente,
          valorNombre: textoNombre,
          valorTelefono:textoTelefono,
          valorDierccion:textoDireccion,
          valorReferencia: textoReferencia,
          valorIdReporte: textoIdReporte,
          valorFalla: textoFalla,
          valorSolucion: textoSolucion,
          valorTecnico: textoTecnico,
          valorAtendido: textoAtendido,
          valorManoObra: textoManoObra,
          valorFecha: textoFecha,
          valorCampo: textoCampo,
          valorApoyo: textoApoyo,
          valorAntena: textoAntena,
          valorRouter: textoRouter,
          valorCable: textoCable,
          valorTubos: textoTubos,
          valorExtras: textoExtras,
          valorTipo: textoTipo,
          valorTipo_Cambio: textoTipo_cambio,
          valorCosto: textoCosto
        }, function(mensaje) {
            $("#resultado_update_reporte").html(mensaje);
        });
    }     
    }
};
</script>

<body>
<div class="container">
<?php
//Cliente, reporte y comunidad

$sql = mysqli_query($conn, "SELECT * FROM clientes WHERE id_cliente=$id_cliente");
$filas = mysqli_num_rows($sql);
$esp= "no";
if ($filas == 0) {
  $esp="si";
  $sql = mysqli_query($conn, "SELECT * FROM especiales WHERE id_cliente=$id_cliente");
}
$cliente = mysqli_fetch_array($sql);
$id_comunidad = $cliente['lugar'];
$comunidad = mysqli_fetch_array(mysqli_query($conn, "SELECT nombre FROM comunidades WHERE id_comunidad=$id_comunidad"));

if($resultado['tecnico']==''){
    $tecnico1[0] = '';
    $tecnico1[1] = 'Sin tecnico';
  }else{
    $id_tecnico = $resultado['tecnico'];
    $tecnico1 = mysqli_fetch_array(mysqli_query($conn, "SELECT user_id, user_name FROM users WHERE user_id=$id_tecnico"));  
  }
?>
  <h2 class="hide-on-med-and-down">Atender Reporte No.<?php echo $id_reporte;?></h2>
  <h5 class="hide-on-large-only">Atender Reporte No.<?php echo $id_reporte;?></h5>
  <div id="Orden"></div>
  <div id="resultado_update_reporte">
  </div>
   <div class="row">
   <ul class="collection">
            <li class="collection-item avatar">
              <img src="../img/cliente.png" alt="" class="circle">
              <span class="title"><b>No. Cliente: </b><?php echo $cliente['id_cliente'];?></span><br>
              <div class="col s12"><br>
                <b class="col s4 m2 l2">Nombre(s): </b>
                <div class="col s12 m9 l9">
                  <input type="text" id="nombres" name="nombres" value="<?php echo $cliente['nombre']; ?>">
                </div>
              </div>
              <div class="col s12">
                <b class="col s4 m2 l2">Telefono: </b>
                <div class="col s12 m9 l9">
                  <input type="text" id="telefono" name="telefono" value="<?php echo $cliente['telefono'];?>">
                </div>
              </div>
              <div class="col s12">
                <b class="col s4 m2 l2">Direccion: </b>
                <div class="col s12 m9 l9">
                  <input type="text" id="direccion" name="direccion" value="<?php echo $cliente['direccion'];?>">
                </div>
              </div>
              <div class="col s12">
                <b class="col s4 m2 l2">Referencia: </b>
                <div class="col s12 m9 l9">
                  <input type="text" id="referencia" name="referencia" value="<?php echo $cliente['referencia'];?>">
                </div>
              </div>

                 <b>Comunidad: </b><?php echo $comunidad['nombre'];?><br>
                 <b>IP: </b><a href="http://<?php echo $cliente['ip'];?>" target="_blank"><?php echo $cliente['ip'];?></a><br>
                  <!-- Switch -->
                 <?php 
                 if ($esp == "no") {                  
                   $estado="";
                   if ($cliente['fecha_corte']>$Fecha_Hoy) {
                     $estado = "checked";
                   } 
                   ?>
                   <b>Internet: </b> 
          
                    <div class="switch right">
                      <label>
                        Off
                        <input type="checkbox" <?php echo $estado; ?> onclick="encender();" id="enciende">
                        <span class="lever"></span>
                        On
                      </label>
                    </div>
                  <?php
                   }
                  ?>
                 <span class="new badge pink hide-on-med-and-up" data-badge-caption="<?php echo $resultado['fecha'];?>"></span>
                 <br><br><hr>
                 <b>Descripción: </b><?php echo $resultado['descripcion'];?><br>
              
              <a href="#!" class="secondary-content hide-on-small-only"><span class="new badge pink" data-badge-caption="<?php echo $resultado['fecha'];?>"></span></a>
            </li>
        </ul>
    <form class="col s12">
    <input id="id_reporte" type="hidden" class="validate" data-length="200" value="<?php echo $id_reporte;?>" required><br>
      <div class="row">
        <div class="col s12 m6 l6">
        <div class="input-field">
          <i class="material-icons prefix">close</i>
          <textarea id="falla" class="materialize-textarea validate" data-length="150" required><?php echo $resultado['falla'];?></textarea> 
          <label for="falla">Diagnostico:</label>
        </div>
        <div class="input-field">
          <i class="material-icons prefix">done</i>
          <textarea id="solucion"  class="materialize-textarea validate" data-length="150" required><?php echo $resultado['solucion']; ?></textarea>
          <label for="solucion">Solución: </label>
        </div>
        <label>APOYO (solo toma uno):</label>
                <p>
                  <?php
                  $bandera = 1; 
                  $sql_tecnico = mysqli_query($conn,"SELECT * FROM users WHERE area='Taller' OR area='Redes'  OR user_id = 49 OR user_id = 28 OR user_id = 25");
                  while($tecnico = mysqli_fetch_array($sql_tecnico)){
                    ?>
                    <div class="col s12 m6 l4">
                      <input type="checkbox" value="<?php echo $tecnico['user_id'];?>" id="tecnico<?php echo $bandera;?>"/>
                      <label for="tecnico<?php echo $bandera;?>"><?php echo $bandera;?>.-<?php echo $tecnico['firstname'];?></label>
                    </div>
                    <?php
                    $bandera++;
                  }$bandera--;
                  ?>
                </p>
      </div>
      <!-- AQUI SE ENCUENTRA LA DOBLE COLUMNA EN ESCRITORIO.-->
      <div class="col s12 m6 l6">
        <div class="hide-on-med-and-down">
         <br><br>
        </div>
        <div class="col s2 m2 l2"><br></div>
          <div class=" col s12 m4 l4"><br>
          ¿Visitar? 
          <div class="switch">
            <label>
              No
              <input type="checkbox"  id="visita">
              <span class="lever"></span>
              Si
            </label>
          </div>
          </div>          
          <div class="col s1 m1 l1"><br></div>
          <div class="col s12 m5 l5" >
              <label for="fecha_visita">Fecha visita:</label>
              <input id="fecha_visita" type="date">    
          </div><br><br>
        <div class="input-field row col s12 m6 l6"><br><br>
          <i class="col s1"> <br></i>
          <select id="atendido" class="browser-default col s10" required>
            <option selected disabled="">¿Listo?</option>
            <option value="No">No</option>
            <option value="Sí">Sí</option> 
          </select>
        </div>
        <div class="col s12 m6 l6">
          <p><br><br><br>
            <input type="checkbox" id="campo" <?php if ($resultado['campo'] ==1) {
              echo 'checked'; }else{ echo ''; } ?> />
            <label for="campo">En Campo</label>
          </p>
        </div><br><br>
        <div class="row col s12"><br><br><br> 
        <h4 class="hide-on-med-and-down">Material:</h4>
        </div>   
        </div>
        </div>
        <div class="row">
          <?php 
          $contador = 1;
          if ($resultado['campo'] ==1) { ?>
                <div class="col s12 m6 l6">
                  <div class="row">
                  <div class="input-field col s12 m6 l6">
                    <i class="material-icons prefix">satellite</i>
                    <input id="antena" type="text" class="validate" data-length="15" required>
                    <label for="antena">Antena (ej: Lite Beam M5):</label>
                  </div>
                  <div class="input-field col s12 m6 l6">
                    <i class="material-icons prefix">pan_tool</i>
                    <input id="mano_obra" type="text" class="validate" data-length="15" required>
                    <label for="mano_obra">Mano de obra:</label>
                  </div>
                  </div>
                  <div class="input-field">
                    <i class="material-icons prefix">router</i>
                    <input id="router" type="text" class="validate" data-length="15" required>
                    <label for="router">Router (ej: Tp-Link):</label>
                  </div>
                  <div class="row">
                    <div class="col s4 m3 l3">
                    <p>
                      <br>
                      <input type="checkbox" id="otros"/>
                      <label for="otros">Otros</label>
                    </p>
                  </div>
                  <div class="input-field col s8 m9 l9">
                    <input id="mas" type="text" class="validate" data-length="15" required>
                    <label for="mas">¿Cuales? (ej: 3 Camaras, 1 Grabador, etc.):</label>
                  </div>
                  </div>
                </div>
                
                <!-- AQUI SE ENCUENTRA LA DOBLE COLUMNA EN ESCRITORIO.-->
                <div class="col s12 m6 l6">
                  <div class="input-field col s12 m6 l6">
                    <i class="material-icons prefix">settings_input_hdmi</i>
                    <input id="cable" type="number" class="validate" data-length="15" required>
                    <label for="cable">Cable Red (metros):</label>
                  </div>
                  <div class="input-field col s12 m6 l6">
                    <i class="material-icons prefix">priority_high</i>
                    <input id="tubos" type="number" class="validate" data-length="15" required>
                    <label for="tubos">Tubos (piezas):</label>
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
                  <div class="col s12 m3 l3"><br><br>
                    <input type="checkbox" id="reemplazo"/>
                    <label for="reemplazo">Reemplazo</label><br><br>
                  </div>
                  <div class="input-field col s12 m6 l6">
                    <i class="material-icons prefix">attach_money</i>
                    <input id="costo" type="number" class="validate" data-length="15" required>
                    <label for="costo">Costo Servicio $0.00:</label>
                  </div>
                  <div class="col s4 m3 l3">
                  <p>
                    <br>
                    <input type="checkbox" id="credito"/>
                    <label for="credito">Credito</label>
                  </p>
            </div>
                </div>
              <?php } ?>  
                <input id="id_cliente" value="<?php echo htmlentities($cliente['id_cliente']);?>" type="hidden"><br><br><br>
                <a onclick="update_reporte(<?php echo $bandera;?>, <?php echo $contador;?>);" class="waves-effect waves-light btn pink right"><i class="material-icons right">send</i>ACTUALIZAR REPORTE</a> 
        </div>       
    </form>
      
  </div> 
<br>
<div class="row col s12">
  <?php $sql = mysqli_query($conn, "SELECT * FROM reportes WHERE atendido = 1  and id_cliente = $id_cliente ORDER BY id_reporte DESC"); ?>
  <h3 class="hide-on-med-and-down">Reportes Atendidos</h3>
  <h5 class="hide-on-large-only">Reportes Atendidos</h5>
  <table class="bordered  highlight responsive-table">
    <thead>
      <tr>
        <th>Reporte No.</th>
        <th>Cliente</th>
        <th>Descripción</th>
        <th>Solución</th>
        <th>Fecha</th>
        <th>Comunidad</th>
        <th>Técnico</th> 
      </tr>      
    </thead>
    <tbody>
    <?php 
    //Obtiene la cantidad de filas que hay en la sql 
    $filas = mysqli_num_rows($sql);
    //Si no existe ninguna fila que sea igual a $sqlBusqueda, entonces mostramos el siguiente mensaje
    if ($filas == 0) {
      echo '<script>M.toast({html:"No se encontraron reportes.", classes: "rounded"})</script>';
    } else {
    //La variable $resultado contiene el array que se genera en la sql, así que obtenemos los datos y los mostramos en un bucle    
    while($resultados = mysqli_fetch_array($sql)) {
      $id_reporte = $resultados['id_reporte'];
      $cliente = mysqli_fetch_array(mysqli_query($conn, "SELECT nombre, lugar, telefono FROM clientes WHERE id_cliente=$id_cliente"));
      $id_comunidad = $cliente['lugar'];
      $comunidad = mysqli_fetch_array(mysqli_query($conn, "SELECT nombre FROM comunidades WHERE id_comunidad=$id_comunidad"));
      $id_tecnico = $resultados['tecnico'];

      if($id_tecnico==''){
          $tecnico[0] = 'Sin tecnico';
        }else{
          $tecnico = mysqli_fetch_array(mysqli_query($conn, "SELECT user_name FROM users WHERE user_id=$id_tecnico"));  
        }
      ?>
                  <tr>
                    <td><b><?php echo $id_reporte;?></b></td>
                    <td><a class="tooltipped" data-position="top" data-tooltip="<?php echo 'Telefono: '. $cliente['telefono']; echo '  Comunidad: '.$comunidad['nombre'];?>"><?php echo $cliente['nombre'];?></a></td>
                    <td><?php echo $resultados['descripcion'];?></td>
                    <td><?php echo $resultados['solucion'];?></td>
                    <td><?php echo $resultados['fecha'];?></td>
                    <td><?php echo $comunidad['nombre'];?></td>
                    <td><?php echo $tecnico[0];?></td>
                  </tr>                  
<?php          
    }//Fin while $resultados
  } //Fin else $filas
    ?>
    </tbody>
  </table>  
</div>
</div>
<br>
<?php mysqli_close($conn); ?>  
</div>
</body>
<?php } ?>  
</html>
