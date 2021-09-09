<!DOCTYPE html>
<html lang="en">
<head>
<?php
  if (!$_GET) {
    header('Location:usuarios.php?pagina=1');//para la paginacion
  }
  include('fredyNav.php');
?>
<title>SIC | Usuarios</title>
<script>
  function eliminar(id){
    $.post("../php/delete_user.php", {
            valorId: id,
          }, function(mensaje) {
              $("#resultado_usuarios").html(mensaje);
          }); 
  };
  function validar_email( email ) 
  {
      var regex = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
      return regex.test(email) ? true : false;
  };
  function insert_usuario() {
      var textoNombre = $("input#nombre").val();
      var textoApellidos = $("input#apellidos").val();
      var textoEmail = $("input#email").val();
      var textoUsuario = $("input#usuario").val();
      var textoContra = $("input#contra").val();
      var textoRepiteContra = $("input#repite_contra").val();
      var textoRol = $("select#rol").val();
    
      if (textoNombre == "") {
        M.toast({html:"Por favor ingrese el nombre(s).", classes: "rounded"});
      }else if(textoApellidos == ""){
        M.toast({html:"Por favor ingrese los apellidos.", classes: "rounded"});
      }else if(textoEmail == ""){
        M.toast({html:"Por favor ingrese un Email.", classes: "rounded"});
      }else if (!validar_email(textoEmail)) {
        M.toast({html:"Por favor ingrese un Email correcto.", classes: "rounded"});
      }else if(textoUsuario == ""){
        M.toast({html:"Por favor ingrese el nombre de usuario.", classes: "rounded"});
      }else if(textoContra == ""){
        M.toast({html:"Por favor ingrese una contraseña.", classes: "rounded"});
      }else if ((textoContra.length) < 6) {
        M.toast({html:"Por favor ingrese una contraseña mas larga.", classes: "rounded"});
      }else if(textoContra != textoRepiteContra){
        M.toast({html:"Las contraseñas no coinciden.", classes: "rounded"});
      }else if(textoRol == 0){
        M.toast({html:"Seleccione un rol de usuario.", classes: "rounded"});
      }else{
        $.post("../php/insert_user.php", {
            valorNombre: textoNombre,
            valorApellidos: textoApellidos,
            valorEmail: textoEmail,
            valorUsuario: textoUsuario,
            valorContra: textoContra,
            valorRol: textoRol
          }, function(mensaje) {
              $("#resultado_usuarios").html(mensaje);
          }); 
      }
  };
</script>
</head>
<main>
<body>
  <div class="container">
  <?php 
    include('../php/admin.php');
  ?>
  <h3>Registrar Usuario</h3>
    <div class="row">
    <div class="input-field col s12 m6 l3">
        <input type="text" class="validate" required id="nombre">
        <label for="nombre">Nombre</label>
      </div>
      <div class="input-field col s12 m6 l3">
        <input type="text" class="validate" required id="apellidos">
        <label for="apellidos">Apellidos</label>
      </div>
      <div class="input-field col s12 m6 l3">
        <input type="email" class="validate" required id="email">
        <label for="email">E-mail</label>
      </div>
      <div class="input-field col s12 m6 l3">
        <input type="text" class="validate" required id="usuario">
        <label for="usuario">Nombre de usuario</label>
      </div>
      <div class="input-field col s12 m6 l3">
        <input type="password" class="validate" required id="contra">
        <label for="contra">Contraseña</label>
      </div>
      <div class="input-field col s12 m6 l3">
        <input type="password" class="validate" required id="repite_contra">
        <label for="repite_contra">Repite Contraseña</label>
      </div>
      <div class="input-field col s12 m6 l3">
          <select id="rol" class="browser-default">
            <option value="0" selected>Seleccione un rol</option>
            <option value="Taller">Taller</option>
            <option value="Redes">Redes</option>
            <option value="Oficina">Oficina</option>
            <option value="Administrador">Administrador</option>
            <option value="Cobrador">Cobrador</option>
          </select>
          <label></label>
      </div>
      <div class="input-field col s12 m6 l3">
        <a onclick="insert_usuario();" class="waves-effect waves-light btn pink right"><i class="material-icons left">send</i></a>
      </div>
    </div>
    <div id="resultado_usuarios">
    <h3>Usuarios</h3>
            <table class="bordered highlight responsive-table">
                <thead>
                    <tr>
                        <th>Nombre(s)</th>
                        <th>Apellidos</th>
                        <th>Usuario</th>
                        <th>E-mail</th>
                        <th>Rol</th>
                        <th>Eliminar</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                include('../php/conexion.php');
                $filas_x_pagina=5;//paginacion
                $iniciar = ($_GET['pagina']-1)*$filas_x_pagina; //paginacion              
                $sql_tmp1 = mysqli_query($conn,"SELECT * FROM users");
                $sql_tmp = mysqli_query($conn,"SELECT * FROM users LIMIT $iniciar,$filas_x_pagina");//paginacion
                $filas = mysqli_num_rows($sql_tmp1);
                $paginas=$filas/$filas_x_pagina;//paginacion
                $paginas = ceil($paginas);//paginacion
                if($filas == 0){
                    ?>
                    <h5 class="center">No hay usuarios</h5>
                    <?php
                }else{
                    while($tmp = mysqli_fetch_array($sql_tmp)){
                ?>
                    <tr>
                      <td><?php echo $tmp['firstname']; ?></td>
                      <td><?php echo $tmp['lastname']; ?></td>
                      <td><?php echo $tmp['user_name']; ?></td>
                      <td><?php echo $tmp['user_email']; ?></td>
                      <td><?php echo $tmp['area']; ?></td>
                      <td><a onclick="eliminar(<?php echo $tmp['user_id'];?>);" class="btn-floating btn-tiny waves-effect waves-light red darken-1"><i class="material-icons">delete</i></a></td>
                    </tr>
                <?php
                    }
                }
                mysqli_close($conn);
                ?>
                </tbody>
            </table>
            <ul class="pagination"> 
              <li class="waves-effect">
                <a href="usuarios.php?pagina= <?php echo $_GET['pagina']<=1 ?  $paginas:$_GET['pagina']-1 ?>"><i class="material-icons">chevron_left</i></a></li>
              <?php for ($i=0; $i < $paginas; $i++) { ?>
              <li class="waves-effect 
              <?php echo $_GET['pagina']==$i+1 ? 'active':'' ?>">
                <a href="usuarios.php?pagina=<?php echo $i+1 ?>"><?php echo $i+1 ?></a>
              </li>
              <?php } ?>
              <li class="waves-effect">
              <a href="usuarios.php?pagina=<?php echo $_GET['pagina']>=$paginas ? 1:$_GET['pagina']+1 ?>"><i class="material-icons">chevron_right</i></a></li>
            </ul>
            <br><br>
        </div>
  </div>
</body>
</main>
</html>