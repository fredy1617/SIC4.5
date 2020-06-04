<html>
<head>
  <title>SIC | Pagos Central</title>
</head>
<?php 
include('fredyNav.php');
include('../php/conexion.php');
date_default_timezone_set('America/Mexico_City');
$Fecha_Hoy = date('Y-m-d');
if (isset($_POST['id_central']) == false) {
  ?>
  <script>    
    function atras() {
      M.toast({html: "Regresando a centrales.", classes: "rounded"})
      setTimeout("location.href='centrales.php'", 800);
    };
    atras();
  </script>
  <?php
}else{
$id_central = $_POST['id_central'];
?>
<script>   
function imprimir(id_pago){
  var a = document.createElement("a");
      a.target = "_blank";
      a.href = "../php/imprimir.php?IdPago="+id_pago;
      a.click();
};
function borrar(IdPago){
    var textoIdCentral = $("input#id_central").val();
  $.post("../php/borrar_pago_central.php", { 
          valorIdPago: IdPago,
          valorIdCentral: textoIdCentral
  }, function(mensaje) {
  $("#mostrar_pagos").html(mensaje);
  }); 
};
function insert_pago() {  
    var textoCantidad = $("input#cantidad").val();
    var textoMes = $("select#mes").val();
    var textoAño = $("select#año").val();
    var textoIdCentral = $("input#id_central").val();

    //Todo esto solo para agregar la descripcion automatica
    textoDescripcion = textoMes+" "+textoAño;
    
    if(document.getElementById('anual').checked==true){
      textoTipo = "Anual";
    }else if(document.getElementById('mensual').checked==true){
      textoTipo = "Mensual";
    }

    if (textoCantidad == "" || textoCantidad ==0) {
        M.toast({html: 'El campo Cantidad se encuentra vacío o en 0.', classes: 'rounded'});
    }else if (textoMes == 0) {
        M.toast({html: 'Seleccione un mes.', classes: 'rounded'});
    }else if (textoAño == 0) {
        M.toast({html: 'Seleccione un año.', classes: 'rounded'});
    }else if (document.getElementById('anual').checked==false && document.getElementById('mensual').checked==false) {
      M.toast({html: 'Elige una opcion Anual o Mensual.', classes: 'rounded'});
    }else {
        $.post("../php/insert_pago_central.php" , { 
            valorTipo: textoTipo,
            valorCantidad: textoCantidad,
            valorDescripcion: textoDescripcion,
            valorIdCentral: textoIdCentral,
            valorMes: textoMes
          }, function(mensaje) {
              $("#mostrar_pagos").html(mensaje);
          });  
    }    
};
</script>
<body>
<?php
$sql = "SELECT * FROM centrales WHERE id=$id_central";
$datos = mysqli_fetch_array(mysqli_query($conn, $sql));
//Sacamos la Comunidad
$id_comunidad = $datos['comunidad'];
$comunidad = mysqli_fetch_array(mysqli_query($conn, "SELECT nombre FROM comunidades WHERE id_comunidad='$id_comunidad'"));
?>
<div class="container">
  <h3 class="hide-on-med-and-down">Realizando pago de la central:</h3>
  <h5 class="hide-on-large-only">Realizando pago de la central:</h5>
  <ul class="collection">
    <li class="collection-item avatar">
      <img src="../img/cliente.png" alt="" class="circle">
      <span class="title"><b>No. Central: </b><?php echo $datos['id'];?></span>
      <p><b>Encargado: </b><?php echo $datos['nombre'];?><br>
         <b>Telefono: </b><?php echo $datos['telefono'];?><br>
         <b>Comunidad: </b><?php echo $comunidad['nombre'];?><br>
         <b>Dirección: </b><?php echo $datos['direccion'];?><br>
         <b>Coordenada: </b><?php echo $datos['coordenadas'];?><br>
         <b>Fecha Vencimineto de Renta: </b><?php echo $datos['vencimiento_renta'];?><br>
         <b>Descripcion General: </b><?php echo $datos['descripcion_gral'];?><br>
         <br>
      </p>
    </li>
  </ul> 
<div id="imprimir"></div><br>
<div class="row">
  <div class="col s12">
    <div class="row">
      <a href="equipos.php?id=<?php echo $datos['id'];?>" class="waves-effect waves-light btn pink right "><i class="material-icons right">visibility</i>VER EQUIPOS</a>
        
<!-- ----------------------------  PAGOS   ---------------------------------------->
      <div class="col s12 m12 l12">
        <h4 class="hide-on-med-and-down pink-text "><< Pagos: >></h4>
        <h5 class="hide-on-large-only  pink-text"><< Pagos: >></h5>
        <form class="row" name="formMensualidad"><br>
          <div class="input-field col s6 m3 l3">
            <i class="material-icons prefix">payment</i>
            <input id="cantidad" type="number" class="validate" data-length="6" value="0" required>
            <label for="cantidad">Cantidad :</label>
          </div>
          <div class="input-field col s6 m2 l2">
            <select id="mes" class="browser-default">
              <option value="0" selected>Seleccione Mes</option>
              <option value="ENERO">Enero</option>
              <option value="FEBRERO">Febrero</option>
              <option value="MARZO">Marzo</option>
              <option value="ABRIL">Abril</option>
              <option value="MAYO">Mayo</option>
              <option value="JUNIO">Junio</option>
              <option value="JULIO">Julio</option>
              <option value="AGOSTO">Agosto</option>
              <option value="SEPTIEMBRE">Septiembre</option>
              <option value="OCTUBRE">Octubre</option>
              <option value="NOVIEMBRE">Noviembre</option>
              <option value="DICIEMBRE">Diciembre</option>
            </select>
          </div>
          <div class="row col s8 m2 l2"><br>
            <select id="año" class="browser-default">
              <option value="0" selected>Seleccione Año</option>
              <option value="2019">2019</option>
              <option value="2020">2020</option>
              <option value="2021">2021</option>
              <option value="2022">2022</option>
            </select>
          </div>
          <div class="input-field col s6 m2 l2">
            <p>
              <input type="checkbox" id="anual"/>
              <label for="anual">Anual</label>
            </p>
          </div>
          <div class="input-field col s6 m3 l3">
            <p>
              <input type="checkbox" id="mensual"/>
              <label for="mensual">Mensual</label>
            </p>
          </div>
          <input id="id_central" value="<?php echo htmlentities($datos['id']);?>" type="hidden">
        </form>
        <a onclick="insert_pago();" class="waves-effect waves-light btn pink right "><i class="material-icons right">send</i>Registrar Pago</a><br>
        <h4>Historial </h4>
         <div id="mostrar_pagos">
        <table class="bordered highlight responsive-table">
          <thead>
            <tr>
              <th>#</th>
              <th>Cantidad</th>
              <th>Tipo</th>
              <th>Descripción</th>
              <th>Usuario</th>
              <th>Fecha</th>
              <!--<th>Imprimir</th>-->
              <th>Borrar</th>
            </tr>
          </thead>
          <tbody>
          <?php
          $sql_pagos = "SELECT * FROM pagos_centrales WHERE id_central = ".$datos['id']." ORDER BY id DESC";
          $resultado_pagos = mysqli_query($conn, $sql_pagos);
          $aux = mysqli_num_rows($resultado_pagos);
          if($aux>0){
          while($pagos = mysqli_fetch_array($resultado_pagos)){
            $id_user = $pagos['usuario'];
            $user = mysqli_fetch_array(mysqli_query($conn, "SELECT user_name FROM users WHERE user_id = '$id_user'"));
          ?>
            <tr>
              <td><b><?php echo $aux;?></b></td>
              <td>$<?php echo $pagos['cantidad'];?></td>
              <td><?php echo $pagos['tipo'];?></td>
              <td><?php echo $pagos['descripcion'];?></td>
              <td><?php echo $user['user_name'];?></td>
              <td><?php echo $pagos['fecha'];?></td>
              <!--<td><a onclick="imprimir(<?php echo $pagos['id'];?>);" class="btn btn-floating pink waves-effect waves-light"><i class="material-icons">print</i></a></td>-->
              <td><a onclick="borrar(<?php echo $pagos['id'];?>);" class="btn btn-floating red darken-1 waves-effect waves-light"><i class="material-icons">delete</i></a></td>
            </tr>
            <?php
            $aux--;
            }//Fin while
            }else{
            echo "<center><b><h5 class = 'red-text'>Esta central aún no ha registrado pagos</h5 ></b></center>";
          }
          ?> 
          </tbody>
        </table>
        </div>
      </div>

    </div>
  </div>
</div>

</div><!----------------CONTAINER----------------->
</body>
<?php } ?>
</html>