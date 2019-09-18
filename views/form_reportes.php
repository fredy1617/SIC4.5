<html>
<head>
  <title>SIC | Reporte</title>
<?php 
include('fredyNav.php');
?>
<script>
  function verificar_reporte() {  
    var textoNombre = $("input#nombres").val();
    var textoTelefono = $("input#telefono").val();
    var textoDireccion = $("input#direccion").val();
    var textoReferencia = $("input#referencia").val();
    var textoCoordenadas = $("input#coordenadas").val();
    var textoDescripcion = $("textarea#descripcion").val();
    var textoIdCliente = $("input#id_cliente").val();

    if(textoDescripcion==""){
      M.toast({html:"El campo descripción no puede estar vacío.", classes: "rounded"})
    }else{
      $.post("modal_rep.php", {
          valorNombre: textoNombre,
          valorTelefono: textoTelefono,
          valorDireccion: textoDireccion,
          valorReferencia: textoReferencia,
          valorCoordenada: textoCoordenadas,
          valorDescripcion: textoDescripcion,
          valorIdCliente: textoIdCliente 
        }, function(mensaje) {
            $("#Continuar").html(mensaje);
        });
    }
  };
</script>

</head>
<main>

<?php
require('../php/conexion.php');

if (isset($_POST['no_cliente']) == false) {
  ?>
  <script>    
    function atras() {
      M.toast({html: "Regresando a clientes.", classes: "rounded"})
      setTimeout("location.href='clientes.php'", 1000);
    }
    atras();
  </script>
  <?php
}else{
$no_cliente = $_POST['no_cliente'];
$sql = mysqli_query($conn, "SELECT * FROM clientes WHERE id_cliente=$no_cliente");
$filas = mysqli_num_rows($sql);
if ($filas == 0) {
  $sql = mysqli_query($conn, "SELECT * FROM especiales WHERE id_cliente=$no_cliente");
}
$datos = mysqli_fetch_array($sql);


//Sacamos la Comunidad
$id_comunidad = $datos['lugar'];
$comunidad = mysqli_fetch_array(mysqli_query($conn, "SELECT nombre FROM comunidades WHERE id_comunidad='$id_comunidad'"));

?>
<body>
<div class="container row" id="Continuar" >
  <div class="row" >
      <h3 class="hide-on-med-and-down">Creando Reporte para el cliente:</h3>
      <h5 class="hide-on-large-only">Creando Reporte para el cliente:</h5>
      </div>
  <div id="resultado_insert_pago">
  </div>
  <ul class="collection">
    <li class="collection-item avatar">
      <img src="../img/cliente.png" alt="" class="circle">
      <span class="title">
        <b>No. Cliente: </b><?php echo $datos['id_cliente'];?></span>
          <br>
         <div class="col s12"><br>
          <b class="col s4 m2 l2">Nombre(s):  </b>
          <div class="col s12 m9 l9">
          <input id="nombres" type="text" class="validate" value="<?php echo $datos['nombre'];?>">
          </div>
         </div>
         <div class="col s12">
          <b class="col s4 m2 l2">Telefono:  </b>
          <div class="col s12 m9 l9">
          <input id="telefono" type="text" class="validate" value="<?php echo $datos['telefono'];?>">
          </div>
         </div>
         <div class="col s12">
          <b class="col s4 m2 l2">Direccion: </b>
          <div class="col s12 m9 l9">
          <input id="direccion" type="text" class="validate" value="<?php echo $datos['direccion'];?>">
          </div>
         </div>
         <div class="col s12">
          <b class="col s4 m2 l2">Referencia: </b>
          <div class="col s12 m9 l9">
            <input id="referencia" type="text" class="validate" value="<?php echo $datos['referencia'];?>">
          </div>
         </div>
         <div class="col s12">
          <b class="col s4 m2 l2">Coordenadas: </b>
          <div class="col s12 m9 l9">
            <input id="coordenadas" type="text" class="validate" value="<?php echo $datos['coordenadas'];?>">
          </div>
         </div><br>
         <b>Comunidad: </b><?php echo $comunidad['nombre'];?><br>
         <b>Fecha de Instalación: </b><?php echo $datos['fecha_instalacion'];?><br>
         <?php
          $Pago = mysqli_fetch_array(mysqli_query($conn, "SELECT descripcion FROM pagos WHERE id_cliente = '$no_cliente'  AND tipo = 'Mensualidad' ORDER BY id_pago DESC LIMIT 1"));
          //Separamos el string
          $ver = explode(" ", $Pago['descripcion']);
          $array =  array('ENERO' => '01','FEBRERO' => '02', 'MARZO' => '03','ABRIL' => '04', 'MAYO' => '05', 'JUNIO' => '06', 'JULIO' => '07', 'AGOSTO' => '08', 'SEPTIEMBRE' => '09', 'OCTUBRE' => '10', 'NOVIEMBRE' => '11',  'DICIEMBRE' => '12');
          $fecha_pago = $array[$ver[0]].'-'.$ver[1];
          date_default_timezone_set('America/Mexico_City');
          $mes_actual = date('m-Y');
          if ($fecha_pago >= $mes_actual) {
            $color = "green";
            $MSJ = "AL-CORRIENTE";
          }else{
            $color = "red darken-3";
            $MSJ = "DEUDOR !";
          }

         ?>
         
         <a href="#!" class="secondary-content"><span class="new badge <?php echo $color;?>" data-badge-caption="<?php echo $MSJ;?>"></span></a>

      </p>
    </li>
  </ul>

  <div class="row">
    <div class="col s12">
      <form class="col s12" name="formMensualidad">
      <br>
      <div class="row">
        <div class="col s1">
          <br>
        </div>
        <div class="input-field col s12 m10 l10">
          <i class="material-icons prefix">description</i>
          <textarea id="descripcion" class="materialize-textarea validate" data-length="200"></textarea>
          <label for="descripcion">Descripción de Reporte:</label>
        </div>
      </div>
      <input id="id_cliente" value="<?php echo htmlentities($datos['id_cliente']);?>" type="hidden">
    </form>
    <a onclick="verificar_reporte();" class="waves-effect waves-light btn pink right"><i class="material-icons right">send</i>Registrar Reporte</a>
    </div>
  </div>

<h4>Historial Reportes</h4>
  <div id="mostrar_pagos">
    <table class="bordered highlight responsive-table">
    <thead>
      <tr>
        <th>#</th>
        <th>Fecha</th>
        <th>Descripción</th>
        <th>Ultima Modificación</th>
        <th>Solución</th>
        <th>Técnico</th>
        <th>Estatus</th>
      </tr>
    </thead>
    <tbody>
<?php
$sql_pagos = "SELECT * FROM reportes WHERE id_cliente = ".$datos['id_cliente']." ORDER BY id_reporte DESC";
$resultado_pagos = mysqli_query($conn, $sql_pagos);
$aux = mysqli_num_rows($resultado_pagos);
if($aux>0){
while($pagos = mysqli_fetch_array($resultado_pagos)){
  $id_tecnico = $pagos['tecnico'];
  $tecnico = mysqli_fetch_array(mysqli_query($conn, "SELECT user_name FROM users WHERE user_id = '$id_tecnico'"));
  if($pagos['atendido']==1){
    $atendido = '<span class="green new badge" data-badge-caption="Atendido">';
  }else if($pagos['atendido']==2){
    $atendido = '<span class="yellow darken-3 new badge" data-badge-caption="EnProceso">';
  }else{
    $atendido = '<span class="red new badge" data-badge-caption="Revisar">';
  }
  ?>
  <tr>
    <td><b><?php echo $aux;?></b></td>
    <td><?php echo $pagos['fecha'];?></td>
    <td><?php echo $pagos['descripcion'];?></td>
    <td><?php echo $pagos['fecha_solucion'];?></td>
    <td><?php echo $pagos['solucion'];?></td>
    <td><?php echo $tecnico['user_name'];?></td>
    <td><?php echo $atendido;?></td>
  </tr>
  <?php
  $aux--;
}
}else{
  echo "<center><b><h3>Este cliente aún no ha registrado reportes</h3></b></center>";
}
?> 
        </tbody>
      </table>
  </div>
<br>
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
