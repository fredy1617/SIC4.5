<html>
<head>
<?php 
include('fredyNav.php');
?>
<script>
  function modificar_reporte(){
    var texoReferencia = $("textarea#referencia").val();
    var textoDescripcion = $("textarea#descripcion").val();
    var textoIdReporte = $("input#id_reporte").val();
    $.post("../php/modificar_reporte.php", {
          valorReferencia: texoReferencia,
          valorDescripcion: textoDescripcion,
          valorIdReporte: textoIdReporte 
        }, function(mensaje) {
            $("#modificar_reporte").html(mensaje);
    });
  };
</script>
</head>
<main>
<?php
if (isset($_POST['id_reporte'])==false) {
  ?>
  <script>
    function atras(){
      M.toast({html: "Regresando a reportes pendientes...", classes: "rounded"});
      setTimeout("location.href='reportes.php'", 1000);
    }
    atras();
  </script>
  <?php
}else{
?>
<body>
<?php
include('../php/conexion.php');
$id_reporte = $_POST['id_reporte'];
$reporte = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM reportes WHERE id_reporte=$id_reporte"));
$id_cliente = $reporte['id_cliente'];
$cliente = mysqli_fetch_array(mysqli_query($conn, "SELECT referencia FROM clientes WHERE id_cliente=$id_cliente"));
?>
<div class="container">
  <h3 class="hide-on-med-and-down">Editar Reporte (<?php echo $id_reporte; ?>)</h3>
  <h5 class="hide-on-large-only">Editar Reporte (<?php echo $id_reporte; ?>)</h5>

  <div id="modificar_reporte">
  </div>

  <div class="row">
    <div class="col s12">
      <form class="col s12" name="formMensualidad">
      <br>
      <div class="row">
        <div class="input-field col s12 m6 l6">
          <i class="material-icons prefix">comment</i>
          <textarea id="referencia"  class="materialize-textarea validate" data-length="150" required><?php echo $cliente['referencia']; ?></textarea>
          <label for="referencia">Referencia: </label>
        </div>
        <div class="input-field col s12 m6 l6">
          <i class="material-icons prefix">description</i>
          <textarea id="descripcion"  class="materialize-textarea validate" data-length="200"><?php echo $reporte['descripcion']; ?></textarea>
          <label for="descripcion">Descripción:</label>
        </div>
      <input id="id_reporte" value="<?php echo $id_reporte;?>" type="hidden">
      </div>
    </form>
    <a onclick="modificar_reporte();" class="waves-effect waves-light btn pink right"><i class="material-icons right">send</i>Editar Reporte</a>
    </div>
  </div>

<?php 
mysqli_close($conn);
?>
</div>
</body>
<?php
}
?>
</main>
</html>