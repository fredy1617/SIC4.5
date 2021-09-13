<html>
<head>
	<title>SIC | Editar Cliente</title>
<?php 
include('fredyNav.php');
include('../php/conexion.php');
include('../php/cobrador.php');
?>
<script>
function update_cliente() {
    var textoIdCliente = $("input#id_cliente").val();
    var textoNombres = $("input#nombres").val();
    var textoTelefono = $("input#telefono").val();
    var textoComunidad = $("select#comunidad").val();
    var textoDireccion = $("input#direccion").val();
    var textoFechaCorte = $("input#fecha_corte").val();
    var textoFechaSus = $("input#fecha_sus").val();
    var textoFechaCT = $("input#fecha_corteT").val();
    var textoReferencia = $("input#referencia").val();
    var textoPaquete = $("select#paquete").val();
    var textoIP = $("input#ip").val();
    var textoTipo = $("select#tipo").val();
    var textoCoordenada = $("input#coordenada").val();
    var textoExtencion = $("input#tel_servicio").val();

    Entra = "Si";
    if (document.getElementById('IntyTel').checked==true) {
      textoServicio = "Internet y Telefonia";
      if (textoTipo == "") {Entra = "No";}
    }else if(document.getElementById('Internet').checked==true){
      textoServicio = "Internet";
      if (textoTipo == "") {Entra = "No";}      
    }else if(document.getElementById('Telefonia').checked==true){
      textoServicio = "Telefonia";
    }
    if(document.getElementById('terminos').checked==true){
      textoTerminos = 1;
    }else{
      textoTerminos = 0;
    }

    if (textoNombres == "") {
      M.toast({html: "El campo Nombre(s) se encuentra vacío.", classes: "rounded"});
    }else if(textoTelefono == ""){
      M.toast({html: "El campo Telefono se encuentra vacío.", classes: "rounded"});
    }else if(textoComunidad == "0"){
      M.toast({html: "No se ha seleccionado una comunidad aún.", classes: "rounded"});
    }else if(textoDireccion == ""){
      M.toast({html: "El campo Dirección se encuentra vacío.", classes: "rounded"});
    }else if(textoReferencia == ""){
      M.toast({html: "El campo Referencia se encuentra vacío.", classes: "rounded"});
    }else if (document.getElementById('IntyTel').checked==false && document.getElementById('Internet').checked==false && document.getElementById('Telefonia').checked==false ) {
      M.toast({html: 'Elige una opcion de Internet o Telefonia.', classes: 'rounded'});
    }else if(textoPaquete == "0"){
      M.toast({html: "No se ha seleccionado un paquete de internet aún.", classes: "rounded"});
    }else if(textoIP == ""){
      M.toast({html: "El campo IP se encuentra vacío.", classes: "rounded"});
    }else if(Entra =="No"){
      M.toast({html: 'Seleccione un Tipo.', classes: 'rounded'});
    }else{
      $.post("../php/update_cliente.php", {
          valorIdCliente: textoIdCliente,
          valorNombres: textoNombres,
          valorTelefono: textoTelefono,
          valorComunidad: textoComunidad,
          valorDireccion: textoDireccion,
          valorReferencia: textoReferencia,
          valorPaquete: textoPaquete,
          valorIP: textoIP,
          valorFechaCorte: textoFechaCorte,
          valorFechaSus: textoFechaSus,
          valorFechaCT: textoFechaCT,
          valorTipo: textoTipo,
          valorServicio: textoServicio,
          valorCoordenada: textoCoordenada,
          valorTerminos: textoTerminos,
          valorExtencion: textoExtencion
        }, function(mensaje) {
            $("#resultado_update_cliente").html(mensaje);
        }); 
    }
};
</script>
</head>
<main>
<?php
if (isset($_POST['no_cliente']) == false) {
  ?>
  <script>
    function atras(){
      M.toast({html: "Regresando a clientes de administrador...", classes: "rounded"});
      setTimeout("location.href='admin_clientes.php'",1000);
    }
    atras();
  </script>
  <?php  
}else{
?>
<body onload="javascript:showContent()">
<div class="container">
<?php
$id_cliente = $_POST['no_cliente'];
$cliente = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM clientes WHERE id_cliente='$id_cliente'"));
$valor = "";
$id = 0;
if ($cliente['contrato'] == 1) {
  $valor = "Contratro";
  $id = 1;
}
if ($cliente['Prepago'] == 1) {
  $valor = "Prepago";
}

$ch = 'checked'; $ch2 = ''; $ch3 = ''; 
if ($cliente['servicio'] == 'Telefonia') {
  $ch = ''; $ch2 = ''; $ch3 = 'checked'; 
}elseif ($cliente['servicio'] == 'Internet y Telefonia') {
  $ch = ''; $ch2 = 'checked'; $ch3 = ''; 
}

$id_comunidad = $cliente['lugar'];
$comunidad = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM comunidades WHERE id_comunidad='$id_comunidad'"));
$id_paquete = $cliente['paquete'];
$paquete_cliente = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM paquetes WHERE id_paquete='$id_paquete'"));
?>

  <br><h3 class="hide-on-med-and-down">Editar cliente No. <?php echo $cliente['id_cliente'];?></h3>
  <h5 class="hide-on-large-only">Editar cliente No. <?php echo $cliente['id_cliente']; ?></h5><br><br>
  <div id="resultado_update_cliente">
  </div>
   <div class="row">
    <form class="col s12">
    <input id="id_cliente" type="hidden" class="validate" data-length="200" value="<?php echo $cliente['id_cliente'];?>" required>
      <div class="row">
        <div class="col s12 m6 l6">
        <div class="input-field">
          <i class="material-icons prefix">account_circle</i>
          <input id="nombres" type="text" class="validate" data-length="50" value="<?php echo $cliente['nombre'];?>" required>
          <label for="nombres">Nombre Completo:</label>
        </div>
        <div class="input-field">
          <i class="material-icons prefix">phone</i>
          <input id="telefono" type="text" class="validate" data-length="13" value="<?php echo $cliente['telefono'];?>" required>
          <label for="telefono">Teléfono:</label>
        </div>
        <div class="row">
        <div class="col s12 m6 l6">
        <label><i class="material-icons">location_on</i>Comunidad:</label>
        <div class="input-field">
          <select id="comunidad" class="browser-default" required>
            <option value="<?php echo $comunidad['id_comunidad'];?>" selected><?php echo $comunidad['nombre'];?> - $<?php echo $comunidad['instalacion'];?></option>
              <?php
                $sql = mysqli_query($conn,"SELECT * FROM comunidades ORDER BY nombre");
                while($comunidad = mysqli_fetch_array($sql)){
                  ?>
                    <option value="<?php echo $comunidad['id_comunidad'];?>"><?php echo $comunidad['nombre'].', '.$comunidad['municipio'];?> - $<?php echo $comunidad['instalacion'];?></option>
                  <?php
                } 
            ?>
          </select>
        </div>
      </div>
      <div class="col s12 m6 l6">
      <label><i class="material-icons">import_export</i>Paquete:</label>
        <div class="input-field ">
          <select id="paquete" class="browser-default" required>
            <option value="<?php echo $paquete_cliente['id_paquete'];?>" selected>$<?php echo $paquete_cliente['mensualidad'];?> Velocidad: <?php echo $paquete_cliente['bajada'].'/'.$paquete_cliente['subida'];?></option>
            <?php
                $sql = mysqli_query($conn,"SELECT * FROM paquetes");
                while($paquete = mysqli_fetch_array($sql)){
                  ?>
                    <option value="<?php echo $paquete['id_paquete'];?>">$<?php echo $paquete['mensualidad'];?> Velocidad: <?php echo $paquete['bajada'].'/'.$paquete['subida'];?></option>
                  <?php
                } 
                mysqli_close($conn);
            ?>
          </select>
        </div>
      </div>
      </div>
       <div class="row">
          <div class="col s1 m1 l1"><br></div>
        <div class="col s12 m4 l4">
          <p>
            <br>
            <input type="checkbox" id="Internet" <?php echo $ch;?> onchange="javascript:showContent()"/>
            <label for="Internet">Internet</label>
          </p>
        </div>
        <div class="col s12 m7 l7">
          <p>
            <br>
            <input type="checkbox" id="IntyTel"  <?php echo $ch2;?>  onchange="javascript:showContent()"/>
            <label for="IntyTel">Internet y Telefonia</label>
          </p>
        </div>
        </div><br>
        <div class="input-field">
          <i class="material-icons prefix">location_on</i>
          <input id="direccion" type="text" class="validate" data-length="100" value="<?php echo $cliente['direccion'];?>" required>
          <label for="direccion">Direccion:</label>
        </div>
        <div class="input-field">
          <i class="material-icons prefix">event_available</i>
          <input id="fecha_sus" type="date" class="validate" value="<?php echo $cliente['fecha_instalacion'];?>" required>
          <label for="fecha_sus">Fecha de Suscripción:</label>
        </div>
      </div>

<!--------- AQUI SE ENCUENTRA LA DOBLE COLUMNA EN ESCRITORIO.----------->
      <div class="col s12 m6 l6">
        <div class="input-field">
          <i class="material-icons prefix">comment</i>
          <input id="referencia" type="text" class="validate" data-length="150" required value="<?php echo $cliente['referencia'];?>">
          <label for="referencia">Referencia:</label>
        </div>
        <div class="input-field col s12 m6 l6">
          <i class="material-icons prefix">edit</i>
          <input id="ip" type="text" class="validate" data-length="15" value="<?php echo $cliente['ip'];?>" required>
          <label for="ip">IP:</label>
        </div>
        <div class="col s12 m6 l6">
          <p>
            <br>
            <input type="checkbox" <?php if ($cliente['t_c'] == 1) { echo "checked"; }else{ echo ""; }?> id="terminos"/>
            <label for="terminos">Terminos y Condiciones</label>
          </p>
        </div>
      <div class="row">
        <div class="col s12"><br>
        <div class="input-field col s12 m6 l6">
              <i class="material-icons prefix">add_location</i>
              <input id="coordenada" type="text" class="validate" data-length="15" required value="<?php echo $cliente['coordenadas'];?>">
              <label for="coordenada">Coordenada:</label>
            </div>
            <div class="input-field col s12 m6 l6">
              <i class="material-icons prefix">phone</i>
              <input id="tel_servicio" type="text" class="validate" data-length="15" required value="<?php echo $cliente['tel_servicio'];?>">
              <label for="tel_servicio">Telefono Servicio:</label>
            </div>
      </div>
      </div>
      <div class="row">
          <div class="col s1"><br></div>
        <div class="col s12 m4 l4">
          <p>
            <br>
            <input type="checkbox" <?php echo $ch3;?> id="Telefonia"/>
            <label for="Telefonia">Telefonia</label>
          </p>
        </div>
        <div class="input-fiel col s12 m7 l7" id="content" style="display: none;"><br>
          <select id="tipo" class="browser-default" required>
            <option value="<?php echo $id; ?>" selected><?php echo $valor; ?></option>
            <option value="0">Prepago</option> 
            <option value="1">Contrato</option>   
          </select>
        </div>
        </div><br>
        <?php
        $id_user = $_SESSION['user_id']; 
        if ($id_user == 49 OR $id_user == 10 OR $id_user == 75 OR $id_user == 70) {
          $FCORTE = '';
        }else{
          $FCORTE = 'disabled';
        }
        ?>
        <div class="input-field">
          <i class="material-icons prefix">date_range</i>
          <input <?php echo $FCORTE;?>  id="fecha_corte" type="date" class="validate" value="<?php echo $cliente['fecha_corte'];?>" required>
          <label for="fecha_corte">Fecha de Corte Internet:</label>
        </div>
        <div class="input-field">
          <i class="material-icons prefix">event</i>
          <input id="fecha_corteT" type="date" class="validate" value="<?php echo $cliente['corte_tel'];?>" required>
          <label for="fecha_corteT">Fecha de Corte Telefono:</label>
        </div>
      </div>
    </div>
</form>
      <a onclick="update_cliente();" class="waves-effect waves-light btn pink right"><i class="material-icons right">send</i>ACTUALIZAR CAMBIOS</a>
  </div> 
</div>
<br>
</body>
<?php } ?>
<script>
   function showContent() {
        element = document.getElementById("content");
        if (document.getElementById('IntyTel').checked==true || document.getElementById('Internet').checked==true) {
            element.style.display='block';
        }
        else {
            element.style.display='none';
        }
    };
</script>
</main>
</html>