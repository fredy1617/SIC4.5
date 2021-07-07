<!DOCTYPE html>
<html lang="en">
  <head>
    <?php
      include('fredyNav.php');
      include ('../php/cobrador.php');
    ?>
    <title>SIC | Perfiles</title>
    <script>
      function insert_perfil() {
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
          $.post("../php/insert_perfil.php", {
              valorNombre: textoNombre,
              valorDescripcion: textoDescripcion,
              valorPrecio: textoPrecio
            }, function(mensaje) {
              $("#resultado_perfil").html(mensaje);
            }); 
        }
      };
      function borrar(Id){
        $.post("../php/borrar_perfil.php", { 
                valorId: Id
        }, function(mensaje) {
        $("#resultado_perfil").html(mensaje);
        }); 
      };
    </script>
  </head>
  <main>
  <body>
    <div class="container">
      <div class="row" >
        <h3 class="hide-on-med-and-down">Registrar Perfil</h3>
        <h5 class="hide-on-large-only">Registrar Perfil</h5>
      </div>
      <div class="row">
        <div class="input-field col s7 m4 l4">
          <i class="material-icons prefix">router</i>
          <input type="text" id="nombre">
          <label for="nombre">Nombre (codigo de mikrotik):</label>
        </div>        
        <div class="input-field col s5 m4 l4">
          <i class="material-icons prefix">edit</i>
          <input type="text" id="descripcion" value="">
          <label for="descripcion">Descripcion (ej: Ficha 1 Hora):</label>
        </div>
        <div class="input-field col s5 m3 l3">
          <i class="material-icons prefix">monetization_on</i>
          <input type="number" id="precio" value="0">
          <label for="precio">Costo:</label>
        </div>
        <div class="input-field">
          <a onclick="insert_perfil();" class="waves-effect waves-light btn pink left right"><i class="material-icons center">send</i></a>
        </div>
      </div>        
      <div id="resultado_perfil">
        <div class="row"><br><br>
          <h3 class="hide-on-med-and-down col s12 m6 l6">Perfiles</h3>
          <h5 class="hide-on-large-only col s12 m6 l6">Perfiles</h5>
        </div>
        <table class="bordered highlight responsive-table">
            <thead>
              <tr>
                <th>No.</th>
                <th>Nombre</th>
                <th>Descripcion</th>
                <th>Costo</th>
                <th>Usuario</th>
                <th>Editar</th>
                <th>Borrar</th>
              </tr>
            </thead>
            <tbody>
            <?php
              $sql_perfiles = mysqli_query($conn, "SELECT * FROM perfiles ORDER BY id DESC");
              if(mysqli_num_rows($sql_perfiles)<=0){
                echo "<h4>No se encontraron perfiles registrados</h4>";
              }else{
                while($perfil = mysqli_fetch_array($sql_perfiles)){
                  $id_user = $perfil['usuario'];
                  $Usuario = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM users WHERE user_id = $id_user"));
                  ?>
                  <tr>
                    <td><?php echo $perfil['id'] ?></td>
                    <td><?php echo $perfil['nombre'] ?></td>
                    <td><?php echo $perfil['descripcion'] ?></td>
                    <td><?php echo $perfil['costo'] ?></td>
                    <td><?php echo $Usuario['firstname'] ?></td>
                    <td><form method="post" action="../views/editar_perfil.php"><input name="id" type="hidden" value="<?php echo $perfil['id'];?>"><button type="submit" class="btn-floating btn-tiny waves-effect waves-light pink"><i class="material-icons">edit</i></button></form></td>
                    <td><a onclick="borrar(<?php echo $perfil['id'];?>);" class="btn btn-floating red darken-1 waves-effect waves-light"><i class="material-icons">delete</i></a></td>
                  </tr>
                  <?php
                }
              }
            ?>
            </tbody>
        </table><br><br>
      </div>
    </div>
  </body>
  </main>
</html>