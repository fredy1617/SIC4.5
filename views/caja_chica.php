<!DOCTYPE html>
<html>
  <head>
  	<title>SIC | Caja Chica</title>
    <?php
    include('fredyNav.php');
    include('../php/conexion.php');
    include('../php/superAdmin.php');
    ?>
    <script>
      function showContent() {
        element2 = document.getElementById("content");
        var textoOp = $("select#opcion").val();

        if (textoOp == 'Egreso') {
          element2.style.display='block';
        }
        else {
          element2.style.display='none';
        }
      };
      function imprimir(id){
        var a = document.createElement("a");
            a.target = "_blank";
            a.href = "../php/ticket_caja_ch.php?Id="+id;
            a.click();
      };
      function insert_caja(){ 
        var textoCantidad = $("input#cantidad").val();  
        var textoOp = $("select#opcion").val();
        if (textoOp == 'Egreso') {
          var textoDescripcion = $("input#ingreso_desc").val();
        }
        else {
          textoDescripcion = 'Ingreso';
        }

        if (textoOp == "" || textoOp ==0) {
          M.toast({html:"Seleccione una opcion.", classes: "rounded"});
        }else if (textoCantidad == "" || textoCantidad ==0) {
          M.toast({html:"El campo Cantidad se encuentra vacío o en 0.", classes: "rounded"});
        }else {
          $.post("../php/insert_caja.php", { 
              valorCantidad: textoCantidad,
              valorDescripcion: textoDescripcion,
              valorTipo: textoOp,
            }, function(mensaje) {
                $("#mostrar_resultados").html(mensaje);
                  
            });
        }
      };
      function borrar_caja(Id){
        $.post("../php/borrar_caja.php", { 
                valorId: Id
        }, function(mensaje) {
        $("#mostrar_resultados").html(mensaje);
        }); 
      };
  </script>
  </head>
  <body>
  	<div class="container" id="mostrar_resultados">
      <?php       
      // SACAMOS LA SUMA DE TODOS LOS EGRESOSO E INGRESOSO DE LA CAJA CHICA
      $Suma_Ingresos = mysqli_fetch_array(mysqli_query($conn, "SELECT SUM(cantidad) AS suma FROM historila_caja_ch WHERE tipo = 'Ingreso'"));
      $Suma_Egresoso = mysqli_fetch_array(mysqli_query($conn, "SELECT SUM(cantidad) AS suma FROM historila_caja_ch WHERE tipo = 'Egreso'"));
      //SE HACE EL CALCULO DEL TOTAL DE LA CAJA CHICA
      $Total = $Suma_Ingresos['suma']-$Suma_Egresoso['suma'];
      ?>
      <br><hr><hr><br>
  		<div class="row">
   			<h3 class="col s12 m7 l7">>> Caja Chica:</h3>
        <h4 class="col s11 m4 l4">TOTAL = <span class="new badge green" data-badge-caption="">$<?php echo $Total; ?></h4>
      </div>
      <hr><hr><br>
      <div class="row">
        <h3>Accion:</h3>
      </div>
      <div class="row">
        <div class="input-field row">
          <i class="col s1"> <br></i>
          <select id="opcion" class="browser-default col s12 m3 l3" required onchange="javascript:showContent()">
            <option value="0" selected >Opciones:</option>
            <option value="Ingreso" >Ingreso</option>
            <option value="Egreso" >Egreso</option>
          </select>
          <div class="row col s12 m3 l3">
            <div class="input-field">
              <i class="material-icons prefix">payment</i>
              <input id="cantidad" type="number" class="validate" data-length="6" required>
              <label for="cantidad">Cantidad: </label>
            </div>
          </div>
          <div class="input-field col s12 m4 l4" id="content" style="display: none;">
            <i class="material-icons prefix">edit</i>
            <input id="ingreso_desc" type="text" class="validate" data-length="100" required>
            <label for="ingreso_desc">Descripcion:</label>
          </div>
        <a onclick="insert_caja();" class="waves-effect waves-light btn pink right"><i class="material-icons right">send</i>Registrar</a>
        <br>
      </div>
      <br><hr><hr><br>
      <div class="row" >
        <div class="col s12 m6 l6">
          <h4>Ingresos: </h4>
          <table>
            <thead>
                <tr>
                  <th>Id</th>
                  <th>Cantidad</th>
                  <th>Fecha y Hora</th>
                  <th>Descripcion</th>
                  <th>Usuario</th>
                  <th>Imprimir</th>
                  <th>Borrar</th>
                </tr>
            </thead>
            <tbody>
              <?php
              $ingresos = mysqli_query($conn, "SELECT * FROM historila_caja_ch WHERE tipo = 'Ingreso'");
              $aux = mysqli_num_rows($ingresos);
              if ($aux > 0) {
                while ($ingreso = mysqli_fetch_array($ingresos)) {
                  $id_user = $ingreso['usuario'];
                  $user = mysqli_fetch_array(mysqli_query($conn, "SELECT user_name FROM users WHERE user_id = '$id_user'"));
              ?>
                  <tr>
                    <td><b><?php echo $ingreso['id'];?></b></td>         
                    <td>$<?php echo $ingreso['cantidad'];?></td>
                    <td><?php echo $ingreso['fecha'].' '.$ingreso['hora'];?></td>
                    <td><?php echo $ingreso['descripcion'];?></td>
                    <td><?php echo $user['user_name'];?></td>
                    <td><a onclick="imprimir(<?php echo $ingreso['id']; ?>);" class="btn btn-floating pink waves-effect waves-light"><i class="material-icons">print</i></a></td>
                    <td><a onclick="borrar_caja(<?php echo $ingreso['id']; ?>);" class="btn btn-floating red darken-1 waves-effect waves-light"><i class="material-icons">delete</i></a></td>
                  </tr>
                <?php 
                }//fin while
              }else{
                echo "<center><b><h3>No se han registrado ingresos</h3></b></center>";
              }
              ?>
            </tbody>
          </table>
        </div>
        <div class="col s12 m6 l6">
          <h4>Egresos: </h4>
          <table >
            <thead>
                <tr>
                  <th>Id</th>
                  <th>Cantidad</th>
                  <th>Fecha y Hora</th>
                  <th>Descripcion</th>
                  <th>Usuario</th>
                  <th>Imprimir</th>
                  <th>Borrar</th>
                </tr>
            </thead>
            <tbody>
              <?php
              $egresos = mysqli_query($conn, "SELECT * FROM historila_caja_ch WHERE tipo = 'Egreso'");
              $aux = mysqli_num_rows($egresos);
              if ($aux > 0) {
                while ($egreso = mysqli_fetch_array($egresos)) {
                  $id_user = $egreso['usuario'];
                  $user = mysqli_fetch_array(mysqli_query($conn, "SELECT user_name FROM users WHERE user_id = '$id_user'"));
              ?>
                  <tr>
                    <td><b><?php echo $egreso['id'];?></b></td>         
                    <td>$<?php echo $egreso['cantidad'];?></td>
                    <td><?php echo $egreso['fecha'].' '.$egreso['hora'];?></td>
                    <td><?php echo $egreso['descripcion'];?></td>
                    <td><?php echo $user['user_name'];?></td>
                    <td><a onclick="imprimir(<?php echo $egreso['id']; ?>);" class="btn btn-floating pink waves-effect waves-light"><i class="material-icons">print</i></a></td>
                    <td><a onclick="borrar_caja(<?php echo $egreso['id']; ?>);" class="btn btn-floating red darken-1 waves-effect waves-light"><i class="material-icons">delete</i></a></td>
                  </tr>
                <?php 
                }//fin while
              }else{
                echo "<center><b><h3>No se han registrado egresos</h3></b></center>";
              }
              ?>
            </tbody>
          </table>
        </div>
      </div>
  	</div>
  </body>
</html>