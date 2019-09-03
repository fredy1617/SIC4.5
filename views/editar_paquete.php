<!DOCTYPE html>
<html lang="en">
<head>
<?php
  include('fredyNav.php');
  include('../php/admin.php');
?>
<title>SIC | Editar Paquete</title>
<script>
  function update_paquete() {
      var textoIdPaquete = $("input#id_paquete").val();
      var textoSubida = $("select#subida").val();
      var textoBajada = $("select#bajada").val();
      var textoMensualidad = $("input#mensualidad").val();
    
      if (textoSubida == 0) {
        M.toast({html :"Seleccione la velocidad de subida.", classes: "rounded"});
      }else if(textoBajada == 0){
        M.toast({html :"Seleccione la velocidad de bajada.", classes: "rounded"});
      }else if(textoMensualidad == 0){
        M.toast({html :"Indique la mensualidad. No puede quedar en 0.", classes: "rounded"});
      }else{
        $.post("../php/update_paquete.php", {
            valorIdPaquete: textoIdPaquete,
            valorSubida: textoSubida,
            valorBajada: textoBajada,
            valorMensualidad: textoMensualidad
          }, function(mensaje) {
              $("#resultado_update_paquete").html(mensaje);
          }); 
      }
  };
</script>
</head>
<main>
<?php
include('../php/conexion.php');
if (isset($_POST['no_paquete']) == false) {
  ?>
  <script>
    function atras(){
      M.toast({html: "Regreando al listado...", classes: "rounded"});
      setTimeout("location.href='paquetes.php'",1000);
    }
    atras();
  </script>
  <?php
}else{
$id_paquete = $_POST['no_paquete'];
?>
<body>
<div id="resultado_update_paquete">
</div>
<?php
$paquete = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM paquetes WHERE id_paquete=$id_paquete"));
?>
  <div class="container">
  <br>
  <h3 class="hide-on-med-and-down">Editando Paquete</h3>
  <h5 class="hide-on-large-only">Editando Paquete</h5>
  <br>
    <div class="row">
     <input type="hidden" id="id_paquete" value="<?php echo $paquete['id_paquete'];?>">
      <div class="input-field col s6 m4 l4">
          <select id="bajada" class="browser-default">
            <option value="<?php echo $paquete['bajada']; ?>" selected><?php echo $paquete['bajada']; ?></option>
            <option value="128k">128 Kilobytes</option>
            <option value="256k">256 Kilobytes</option>
            <option value="512k">512 Kilobytes</option>
            <option value="768k">768 Kilobytes</option>
            <option value="1M">1 Mega</option>
            <option value="2M">2 Megas</option>
            <option value="3M">3 Megas</option>
            <option value="4M">4 Megas</option>
            <option value="5M">5 Megas</option>
            <option value="10M">10 Megas</option>
            <option value="15M">15 Megas</option>
            <option value="20M">20 Megas</option>
          </select>
      </div>
      <div class="input-field col s6 m4 l4">
          <select id="subida" class="browser-default">
            <option value="<?php echo $paquete['subida']; ?>" selected><?php echo $paquete['subida']; ?></option>
            <option value="128k">128 Kilobytes</option>
            <option value="256k">256 Kilobytes</option>
            <option value="512k">512 Kilobytes</option>
            <option value="768k">768 Kilobytes</option>
            <option value="1M">1 Mega</option>
            <option value="2M">2 Megas</option>
            <option value="3M">3 Megas</option>
            <option value="4M">4 Megas</option>
            <option value="5M">5 Megas</option>
            <option value="10M">10 Megas</option>
            <option value="15M">15 Megas</option>
            <option value="20M">20 Megas</option>
          </select>
      </div>
      <div class="input-field col s6 m4 l4">
         <i class="material-icons prefix">monetization_on</i>
        <input type="number" id="mensualidad" value="<?php echo $paquete['mensualidad']; ?>">
        <label for="mensualidad">Mesnsualidad</label>
      </div>
      <div class="input-field col s12 m12 l12">
        <a onclick="update_paquete();" class="waves-effect waves-light btn pink left right"><i class="material-icons center">send</i></a>
      </div>
    </div>
    <br><br>
  </div>
</body>
<?php
}
?>
</main>
</html>