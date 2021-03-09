<html>
<head>
  <title>SIC | Reporte</title>
<?php 
include('fredyNav.php');
?>
<script>
  function showContent() {
    element = document.getElementById("content");
    element2 = document.getElementById("content2");
    var textoReporte = $("select#reporte").val();

    if (textoReporte == 'Cambio De Domicilio') {
      element.style.display='block';
    }
    else {
      element.style.display='none';
    }
    if (textoReporte == 'Cambio De Contraseña') {
      element2.style.display='block';
    }
    else {
      element2.style.display='none';
    }      
  };

  function verificar_reporte() {  
    var textoNombre = $("input#nombres").val();
    var textoTelefono = $("input#telefono").val();
    var textoDireccion = $("input#direccion").val();
    var textoReferencia = $("input#referencia").val();
    var textoCoordenadas = $("input#coordenadas").val();
    var textoReporte = $("select#reporte").val();
    var textoIdCliente = $("input#id_cliente").val();

    if (textoReporte == 'Cambio De Domicilio') {
      var textoCambio = $("input#cambio").val();
      if (textoCambio == '') {
        No = 'No';
        text = 'Colocar el domicilio nuevo.';
      }else{
        No = 'Si';
        textoDescripcion = textoReporte+': '+textoCambio;
      }
    }else if (textoReporte == 'Cambio De Contraseña') {
      var textoCambio = $("input#cambio2").val();
      if (textoCambio.length < 8) {
        No = 'No';
        text = 'La Contraseña debe de ser minimo de 8 caracteres.';
      }else{
        No = 'Si';
        textoDescripcion = textoReporte+' A: '+textoCambio;
      }
    }else{
      No ='Si';
      textoDescripcion = textoReporte;
    }

    if(document.getElementById('otros').checked==true){
      textoMas = $("input#mas").val();
      if (textoMas == '') {
        Entra = 'No';
      }else{
        Entra = 'Si';
        textoDescripcion = textoMas;

        if (textoIdCliente > 10000) {
          if(document.getElementById('mantenimiento').checked==true){
            textoDescripcion = 'Mantenimiento: '+textoMas;
          }
          if(document.getElementById('especial').checked==true){
            textoDescripcion = 'Reporte Especial: '+textoMas;
          }
        }
      }
    }else{
      Entra = 'Si';
    }

    if(document.getElementById('otros').checked==false && textoReporte == 0){
      M.toast({html:"Elige una opcion de reporte.", classes: "rounded"})
    }else if(Entra == "No"){
      M.toast({html:"Especifique el reporte !", classes: "rounded"})
    }else if((textoTelefono.length) < 10){
      M.toast({html:"Ingrese un numero de Telefono valido", classes: "rounded"})
    }else if(No == "No"){
      M.toast({html:""+text, classes: "rounded"})
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
<script>
  function irconsumo(){  
    textoIdCliente = <?php echo $no_cliente; ?>;
    $.post("../php/ir_consumo.php", { 
      valorCliente:textoIdCliente,
    }, function(mensaje) {
    $("#consumo_ir").html(mensaje);
    }); 
  };
</script>
<body>
  <div id="consumo_ir"></div>
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
         <b>Fecha de Instalación: </b><?php echo $datos['fecha_instalacion'];?><a onclick="irconsumo();" class="waves-effect waves-light btn pink right"><i class="material-icons right">equalizer</i>CONSUMO</a><br>
         <?php
         if ($datos['id_cliente'] < 10000) {
          $Pago = mysqli_fetch_array(mysqli_query($conn, "SELECT descripcion FROM pagos WHERE id_cliente = '$no_cliente'  AND tipo = 'Mensualidad' ORDER BY id_pago DESC LIMIT 1"));
          //Separamos el stringv
          if ($datos['servicio'] == 'Internet y Telefonia' OR $datos['servicio'] == 'Internet') {
          date_default_timezone_set('America/Mexico_City');
          $mes_actual = date('Y-m');

          if ($Pago != "") {
            $ver = explode(" ", $Pago['descripcion']);
            $array =  array('ENERO' => '01','FEBRERO' => '02', 'MARZO' => '03','ABRIL' => '04', 'MAYO' => '05', 'JUNIO' => '06', 'JULIO' => '07', 'AGOSTO' => '08', 'SEPTIEMBRE' => '09', 'OCTUBRE' => '10', 'NOVIEMBRE' => '11',  'DICIEMBRE' => '12');
            $fecha_pago = date($ver[1].'-'.$array[$ver[0]]);
            if ($fecha_pago >= $mes_actual) {

              $color = "green";
              $MSJ = "AL-CORRIENTE";
            }else{
              $color = "red darken-3";
              $MSJ = "DEUDOR !";
            }
         ?>
         <a href="#!" class="secondary-content"><span class="new badge <?php echo $color;?>" data-badge-caption="<?php echo $MSJ;?>"></span></a>
         <?php
         }
        }
        if ($datos['servicio'] == 'Internet y Telefonia' OR $datos['servicio'] == 'Telefonia') {
         if ($datos['tel_cortado'] == 0) {
           $estado = "ACTIVO";
           $col = "green";
         }else{
           $estado = "CORTADO";
           $col = "red";
         }
         ?>
         <b>Extención:  <?php echo $datos['tel_servicio'];?></b><br>
         <b>Telefono:  <a class="<?php echo $col;?>-text"><?php echo $estado;?></a></b><br>
          <?php  
        }
      }?>
      </p>
    </li>
  </ul>

  <div class="row">
    <div class="col s12">
      <form class="col s12" name="formMensualidad">
      <br>
      <div class="input-field row">
          <i class="col s1"> <br></i>
          <select id="reporte" class="browser-default col s12 m4 l4" required onchange="javascript:showContent()">
            <option value="0" selected >Opciones:</option>
            <option value="No Tiene Internet" >No Tiene Internet</option>
            <option value="Internet Intermitente" >Internet Intermitente</option>
            <option value="Internet Lento" >Internet Lento</option>
            <option value="Cambio De Domicilio" >Cambio De Domicilio</option>
            <option value="Cambio De Contraseña" >Cambio De Contraseña</option>
          </select>
          <div class="input-field col s12 m6 l6" id="content" style="display: none;">
            <input id="cambio" type="text" class="validate" data-length="100" required>
            <label for="cambio">Referencia (Casa de color blanco, dos pisos cercas de la iglesia):</label>
        </div>
        <div class="input-field col s10 m5 l5" id="content2" style="display: none;">
            <input id="cambio2" type="text" class="validate" data-length="100" required>
            <label for="cambio2">Contraseña (Minimos 8 caracteres):</label>
        </div>
      </div>
      <div class="row">
        <?php if ($datos['id_cliente']<10000) {
        ?>
        <div class="col s1">
          <br>
        </div>
        <?php } ?>
        <div class="col s3 m2 l2">
          <p><br>
            <input type="checkbox" id="otros"/>
            <label for="otros">Otra Opción</label>
          </p>
        </div>
        <div class="input-field col s8 m6 l6">
            <input id="mas" type="text" class="validate" data-length="100" required>
            <label for="mas">Especifica (ej: Revicion de camaras, Aumentar paquete, etc.):</label>
        </div>
        <?php if ($datos['id_cliente']>=10000) {
        ?>
        <div class="col s4 m2 l2">
          <p><br>
            <input type="checkbox" id="mantenimiento"/>
            <label for="mantenimiento">Mantenimiento</label>
          </p>
        </div>
        <div class="col s4 m2 l2">
          <p><br>
            <input type="checkbox" id="especial"/>
            <label for="especial">Especial</label>
          </p>
        </div>
      <?php } ?>
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
