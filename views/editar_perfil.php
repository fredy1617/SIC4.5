<!DOCTYPE html>
<html lang="en">
  <head>
  <?php
    include('fredyNav.php');
  ?>
  <title>SIC | Editar Perfil</title>
  <script>
    function update_perfil( id) {
        var textoNombre = $("input#nombre").val();
        var textoDescripcion = $("input#descripcion").val();
        var textoPrecio = $("input#precio").val();

        if (textoNombre == "") {
          M.toast({html :"Por favor ingrese el nombre la comunidad.", classes: "rounded"});
        }else if(textoDescripcion == 0){
          M.toast({html :"El precio de instalación no puede quedar en 0.", classes: "rounded"});
        }else if(textoPrecio == 0){
          M.toast({html :"Por favor seleccione un servidor.", classes: "rounded"});
        }else{
          $.post("../php/update_perfil.php", {
              valorId: id,
              valorNombre: textoNombre,
              valorDescripcion: textoDescripcion,
              valorPrecio: textoPrecio
            }, function(mensaje) {
              $("#resultado_update").html(mensaje);
            }); 
        }
      };
  </script>
  </head>
  <?php
  if (isset($_POST['id']) == false) {
    ?>
    <script>    
      function atras() {
        M.toast({html: "Regresando al listado...", classes: "rounded"})
        setTimeout("location.href='perfiles.php'", 1000);
      }
      atras();
    </script>
    <?php
  }else{
    $id = $_POST['id'];
    $Perfil = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM perfiles WHERE id = $id"));
    ?>
    <body>
      <div id="resultado_update"></div>
      <div class="container"><br><br>
        <h3 class="hide-on-med-and-down">Editando Perfil</h3>
        <h5 class="hide-on-large-only">Editando Perfil</h5><br>
        <div class="row">
          <div class="input-field col s7 m4 l4">
            <i class="material-icons prefix">router</i>
            <input type="text" id="nombre" value="<?php echo $Perfil['nombre'];?>">
            <label for="nombre">Nombre (codigo de mikrotik):</label>
          </div>        
          <div class="input-field col s5 m4 l4">
            <i class="material-icons prefix">edit</i>
            <input type="text" id="descripcion" value="<?php echo $Perfil['descripcion'];?>">
            <label for="descripcion">Descripcion (ej: Ficha 1 Hora):</label>
          </div>
          <div class="input-field col s5 m3 l3">
            <i class="material-icons prefix">monetization_on</i>
            <input type="number" id="precio" value="<?php echo $Perfil['costo'];?>">
            <label for="precio">Costo:</label>
          </div>
          <div class="input-field col s12 m12 l12">
            <a onclick="update_perfil(<?php echo $id;?>);" class="waves-effect waves-light btn pink left right"><i class="material-icons center">send</i></a>
          </div>
        </div><br><br>
      </div>
    </body>
  <?php
  }
  ?>
</html>