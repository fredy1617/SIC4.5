<!DOCTYPE html>
<html lang="en">
<head>
<?php
  include('fredyNav.php');
  include('../php/cobrador.php');
?>
</style>
<title>SIC | Servidores</title>
<script>
  function insert_Servidor() {
      var textoIP = $("input#ip").val();
      var textoUser = $("input#user").val();
      var textoPass = $("input#pass").val();
      var textoPort = $("input#port").val();
      var textoNombre = $("input#nombre").val();
  if (textoIP == "") {
        M.toast({html :"El campo IP no puede estar vacío.", classes: "rounded"});
      }else if(textoUser == ""){
        M.toast({html :"El campo Usuario no puede estar vacío.", classes: "rounded"});
      }else if(textoPass == ""){
        M.toast({html :"El campo Contraseña no puede estar vacío.", classes: "rounded"});
      }else if(textoPort == ""){
        M.toast({html :"El campo Puerto no puede estar vacío.", classes: "rounded"});
      }else if(textoNombre == ""){
        M.toast({html :"El campo Nombre no puede estar vacío.", classes: "rounded"});
      }else{
        $.post("../php/insert_servidor.php", {
            valorIP: textoIP,
            valorUser: textoUser,
            valorPass: textoPass,
            valorPort: textoPort,
            valorNombre: textoNombre
          }, function(mensaje) {
              $("#resultado_servidor").html(mensaje);
          }); 
      }
  };
  function test(id_servidor) {
      if (id_servidor == "") {
        M.toast({html:"Ocurrio un error al seleccionar el Servidor.", classes: "rounded"});
      }else{
        $.post("../php/test.php", {
            valorIdServidor: id_servidor,
          }, function(mensaje) {
              $("#test").html(mensaje);
          }); 
      }
  };
</script>
</head>
<main>
<body>
  <div id="test"></div>
  <div class="container">
  <div class="row" >
      <h3 class="hide-on-med-and-down">Registrar Servidor</h3>
      <h5 class="hide-on-large-only">Registrar Servidor</h5>
  </div>
    <div class="row">
      <div class="input-field col s12 m4 l4">
        <i class="material-icons prefix">settings_ethernet</i>
        <input type="text" id="ip" data-length="15">
        <label for="ip">IP o dirección del Servidor:</label>
      </div>
      <div class="input-field col s12 m4 l4">
        <i class="material-icons prefix">account_circle</i>
        <input type="text" id="user" data-length="20" >
        <label for="user">Usuario:</label>
      </div>
      <div class="input-field col s12 m4 l4">
        <i class="material-icons prefix">lock</i>
        <input type="text" id="pass" data-length="20">
        <label for="pass">Contraseña:</label>
      </div>
      <div class="input-field col s12 m7 l7">
        <i class="material-icons prefix">created</i>
        <input type="text" id="nombre" data-length="50">
        <label for="nombre">Nombre:</label>
      </div>
      <div class="input-field col s12 m4 l4">
        <i class="material-icons prefix">settings_input_hdmi</i>
        <input type="number" id="port" data-length="6">
        <label for="port">Puerto:</label>
      </div> 
      <div class="input-field right">
        <a onclick="insert_Servidor();" class="waves-effect waves-light btn pink left"><i class="material-icons center">send</i></a>
      </div>
    </div>
    <div id="resultado_servidor">
         <div class="row" >
              <h3 class="hide-on-med-and-down">Servidores</h3>
              <h5 class="hide-on-large-only">Servidores</h5>
          </div>
          <table class="bordered highlight responsive-table">
              <thead>
                  <tr>
                      <th>No. Servidor</th>
                      <th>Nombre</th>
                      <th>IP</th>
                      <th>Usuarios</th>
                      <th>Contraseña</th>
                      <th>Puerto</th>
                      <th>Editar</th>
                      <th>Test</th>
                  </tr>
              </thead>
              <tbody>
              <?php
              include('../php/conexion.php');
              $sql_tmp = mysqli_query($conn,"SELECT * FROM servidores");
              $columnas = mysqli_num_rows($sql_tmp);
              if($columnas == 0){
                  ?>
                  <h5 class="center">No hay Servidores</h5>
                  <?php
              }else{
                  while($tmp = mysqli_fetch_array($sql_tmp)){
              ?>
                  <tr>
                    <td><?php echo $tmp['id_servidor']; ?></td>
                    <td><?php echo $tmp['nombre']; ?></td>
                    <td><a href="http://<?php echo $tmp['ip']; ?>" target="_blank"><?php echo $tmp['ip']; ?></a></td>
                    <td><?php echo $tmp['user']; ?></td>
                    <td><?php echo $tmp['pass']; ?></td>
                    <td><?php echo $tmp['port']; ?></td>
                    <td><form method="post" action="../views/editar_servidor.php"><input name="no_servidor" type="hidden" value="<?php echo $tmp['id_servidor']; ?>"><button type="submit" class="btn-floating btn-tiny waves-effect waves-light pink"><i class="material-icons">edit</i></button></form></td>
                    <td><a onclick="test(<?php echo $tmp['id_servidor'];?>);" class="btn btn-floating pink waves-effect waves-light"><i class="material-icons">swap_horiz</i></a></td>
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