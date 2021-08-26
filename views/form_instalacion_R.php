<html>
<head>
	<title>SIC | Formulario Instalación</title>
<?php 
include('fredyNav.php');
if (isset($_POST['no_cliente']) == false) {
  ?>
  <script>    
    function atras() {
      M.toast({html: "SIN CLIENTE...", classes: "rounded"})
      setTimeout("location.href='form_orden.php'", 800);
    };
    atras();
  </script>
  <?php
}else{
$no_cliente = $_POST['no_cliente'];
$Cliente =  mysqli_fetch_array(mysqli_query($conn,"SELECT * FROM canceladas WHERE id_cliente=$no_cliente"));
$id_comunidad = $Cliente['lugar'];
$comunidad =  mysqli_fetch_array(mysqli_query($conn,"SELECT * FROM comunidades WHERE id_comunidad=$id_comunidad"));
?>
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
function insert_cliente(id) {
    var textoNombreC = $("input#nombreCompleto").val();
    var textoTelefono = $("input#telefono").val();
    var textoComunidad = $("select#comunidad").val();
    var textoDireccion = $("textarea#direccion").val();   
    var textoReferencia = $("textarea#referencia").val();
    var textoPaquete = $("select#paquete").val();
    var textoAnticipo = $("input#Anticipo").val();
    var textoCostoTotal = $("input#CostoTotal").val();
    var textoTipoInst = $("select#tipo").val();

    if(document.getElementById('banco').checked==true){ textoTipo = "Banco";
    }else{   textoTipo = "Efectivo";  }
    Entra = "Si";

    if (document.getElementById('IntyTel').checked==true) {
      textoServicio = "Internet y Telefonia";
      if (textoTipoInst == "") {Entra = "No";}
    }else if(document.getElementById('Internet').checked==true){
      textoServicio = "Internet";
      if (textoTipoInst == "") {Entra = "No";}      
    }else if(document.getElementById('Telefonia').checked==true){
      textoServicio = "Telefonia";
    }
  
    if (textoNombreC == "") {
      M.toast({html: 'El campo Nombre(s) se encuentra vacío.', classes: 'rounded'});
    }else if(textoTelefono.length < 10){
      M.toast({html: 'El telefono tiene que tener al menos 10 dijitos.', classes: 'rounded'});
    }else if(textoComunidad == "0"){
      M.toast({html: 'No se ha seleccionado una comunidad aún.', classes: 'rounded'});
    }else if(textoPaquete == "0"){
      M.toast({html: 'No se ha seleccionado un paquete de internet aún.', classes: 'rounded'});
    }else if(textoDireccion == ""){
      M.toast({html: 'El campo Dirección se encuentra vacío.', classes: 'rounded'});
    }else if (document.getElementById('IntyTel').checked==false && document.getElementById('Internet').checked==false && document.getElementById('Telefonia').checked==false ) {
      M.toast({html: 'Elige una opcion de Internet o Telefonia.', classes: 'rounded'});
    }else if(textoReferencia == ""){
      M.toast({html: 'La referencia esta vacia.', classes: 'rounded'});
    }else if(textoCostoTotal == "" || textoCostoTotal == 0){
      M.toast({html: 'El Costo Total se encuentra vacío o en 0.', classes: 'rounded'});
    }else if(Entra =="No"){
      M.toast({html: 'Seleccione un Tipo.', classes: 'rounded'});
    }else{
      $.post("../php/insert_cliente.php", {
          valorNombres: textoNombreC,
          valorTelefono: textoTelefono,
          valorComunidad: textoComunidad,
          valorDireccion: textoDireccion,
          valorReferencia: textoReferencia,
          valorPaquete: textoPaquete,
          valorAnticipo: textoAnticipo,
          valorCostoTotal: textoCostoTotal,
          valorTipo: textoTipo,
          valorTipoInst: textoTipoInst,
          valorServicio: textoServicio,
          valorId: id,
          valorVer: 'Cancelado'
        }, function(mensaje) {
            $("#resultado_insert_cliente").html(mensaje);
        }); 
    }
};
</script>
</head>
<main>
<body>
<div class="container">
  <div class="row" >
      <h3 class="hide-on-med-and-down">Registrar Instalación</h3>
      <h5 class="hide-on-large-only">Registrar Instalación</h5>
  </div>
  <div id="resultado_insert_cliente">
  </div>
   <div class="row">
    <form class="col s12">
      <div class="row">
        <div class="input-field col s12 m7 l7">
          <i class="material-icons prefix">account_circle</i>
          <input id="nombreCompleto" type="text" class="validate" data-length="30" required  value="<?php echo $Cliente['nombre']; ?>">
          <label for="nombreCompleto">Nombre (s)   Apellido Paterno   Apellido Materno:</label>
        </div> 
        <div class="input-field col s12 m5 l5">
          <i class="material-icons prefix">phone</i>
          <input id="telefono" type="text" class="validate" data-length="13" required  value="<?php echo $Cliente['telefono']; ?>">
          <label for="telefono">Teléfono:</label>
        </div> 
      </div>
      <div class="row"  id="datos"></div>
      <h6><br><i class="material-icons prefix">comment</i><b> Referencia:</b></h6>
      <div class="input-field col s12 m8 l8">
        <textarea id="referencia" class="materialize-textarea validate" data-length="100" required> <?php echo $Cliente['referencia']; ?></textarea>
        <label for="referencia">Casa de Color  Cercas De:  ej. (Escuela, Iglesia)  Especificación: ej. (Dos pisos, Porton blanco):</label>
      </div>
      <div class="input-field col s12 m4 l4">
        <i class="material-icons prefix">location_on</i>
        <textarea id="direccion" class="
         materialize-textarea validate" data-length="100" required><?php echo $Cliente['direccion']; ?></textarea>
        <label for="direccion">Direccion:</label>
      </div>
      <div class="col s12 m6 l6">  
        <div class="input-field row">
          <i class="col s1"> <br></i>
          <select id="comunidad" class="browser-default col s10" required>
            <option value="<?php echo $Cliente['lugar']; ?>" selected><?php echo $comunidad['nombre']; ?></option>
            <?php
            require('../php/conexion.php');
                $sql = mysqli_query($conn,"SELECT * FROM comunidades ORDER BY nombre");
                while($comunidad = mysqli_fetch_array($sql)){
                  ?>
                  <option value="<?php echo $comunidad['id_comunidad'];?>"><?php echo $comunidad['nombre'].', '.$comunidad['municipio'].' -> $'. $comunidad['instalacion'];?></option>
                  <?php
                } 
            ?>
          </select>
        </div>        
        <div class="row">
          <div class="col s1"><br></div>
        <div class="col s12 m4 l4">
          <p>
            <br>
            <input type="checkbox" id="Internet"  onchange="javascript:showContent()"/>
            <label for="Internet">Internet</label>
          </p>
        </div>
        <div class="col s12 m7 l7">
          <p>
            <br>
            <input type="checkbox" id="IntyTel"  onchange="javascript:showContent()"/>
            <label for="IntyTel">Internet y Telefonia</label>
          </p>
        </div>
        </div><br>
        <div class="input-field row">
          <i class="material-icons prefix">monetization_on</i>
          <input id="CostoTotal" type="number" class="validate" data-length="20" required value="0">
          <label for="CostoTotal">CostoTotal:</label>
        </div>
      </div>
      <!-- AQUI SE ENCUENTRA LA DOBLE COLUMNA EN ESCRITORIO.-->
      <div class="col s12 m6 l6">          
        <div class="input-field row">
          <i class="col s1"> <br></i>
          <select id="paquete" class="browser-default col s10" required>
            <option value="0" selected >Paquete</option>
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
        </div><br>
        <div class="row">
          <div class="col s1"><br></div>
        <div class="col s12 m4 l4">
          <p>
            <br>
            <input type="checkbox" id="Telefonia"/>
            <label for="Telefonia">Telefonia</label>
          </p>
        </div>
        <div class="input-fiel col s12 m7 l7" id="content" style="display: none;"><br>
              <select id="tipo" class="browser-default" required>
                <option value="" selected>Tipo:</option>
                <option value="0">Prepago</option>
                <option value="1">Contrato</option>
              </select>
            </div>
        </div>
        <div class="row">
        <div class="input-field col s8 m9 l9">
          <i class="material-icons prefix">local_atm</i>
          <input id="Anticipo" type="number" class="validate" data-length="6" required value="0">
          <label for="Anticipo">Anticipó:</label>
        </div>
        <div class="col s4 m3 l3">
          <p>
            <br>
            <input type="checkbox" id="banco"/>
            <label for="banco">Banco</label>
          </p>
        </div>
        </div>
      </div>
    </div>
</form>
      <a onclick="insert_cliente(<?php echo $no_cliente; ?>);" class="waves-effect waves-light btn pink right"><i class="material-icons right">send</i>ENVIAR</a>
  </div> 
</div><br>
</body>
</main>}
<?php } ?>
</html>
