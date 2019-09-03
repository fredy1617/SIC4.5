<html>
<head>
	<title>SIC | Todos</title>
<?php 
include('fredyNav.php');
?>
<!--Inicia Script de dispositivos-->
<script>
  function buscar_folio_pendiente() {
    var textoBusqueda = $("input#buscar_dispositivo_pendiente").val();
    if (textoBusqueda != "") {
        $.post("../php/buscar_dispositivo_pendiente.php", {valorBusqueda: textoBusqueda}, function(mensaje) {
            $("#resultado_dispositivo_pendiente").html(mensaje);
        }); 
    } else { 
        ("#resultado_dispositivo_pendiente").html('No se encontraron dispositivos.');
  };
};
</script>
<!--Termina script dispositivos-->
</head>
<main>
<body>
<div class="container">
  <h3>Todos</h3>
  <nav>
    <div class="nav-wrapper">
      <form>
        <div class="input-field pink lighten-4">
          <input id="buscar_dispositivo_pendiente" type="search" placeholder="Folio o nombre del cliente" maxlength="30" value="" autocomplete="off" onKeyUp="buscar_folio_pendiente()" autofocus="true" required>
          <label class="label-icon" for="search"><i class="material-icons">search</i></label>
          <i class="material-icons">close</i>
        </div>
      </form>
    </div>
  </nav> 
  <p><div id="resultado_dispositivo_pendiente"> 
  </div></p>
</div>
<br>
</body>
</main>
</html>