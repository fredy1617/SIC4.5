<html>
<head>
	<title>SIC | Atender Reporte</title>
<?php 
include('fredyNav.php');
include('../php/conexion.php');
?>
<script>
function update_reporte() {
    var textoAtendido = $("select#atendido").val();
    var textoCorte = $("input#fecha_corte").val();
    var id_Pago = $("input#id_pago").val();    
    var id_cliente = $("input#id_cliente").val(); 
    if (textoAtendido == "") {
      M.toast({html:"Elegir si fue riegistrado o no.", classes: "rounded"});
    }else{
      $.post("../php/update_tel.php", {
          valorAtendido: textoAtendido,
          valorCorte: textoCorte,
          valorIdPago: id_Pago,
          valorIdCliente: id_cliente
        }, function(mensaje) {
            $("#resultado_update_tel").html(mensaje);
        }); 
    }
    }
</script>
</head>
<main>
<?php
if (isset($_POST['id_pago']) == false) {
  ?>
  <script>
    function atras(){
      M.toast({html: "Regresando...", classes: "rounded"})
      setTimeout("location.href='tel.php'", 800);
    }
    atras();
  </script>
  <?php
}else{
//Cliente, reporte y comunidad
$id_pago = $_POST['id_pago'];
?>
<body>
<div class="container">
<?php
//Cliente, reporte y comunidad
$id_pago = $_POST['id_pago'];
$resultado = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM pagos WHERE id_pago = $id_pago"));
$id_cliente = $resultado['id_cliente'];
$cliente = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM clientes WHERE id_cliente=$id_cliente"));
$id_user=$resultado['id_user'];
$usuario = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM users WHERE user_id = $id_user"));
$id_comunidad = $cliente['lugar'];
$comunidad = mysqli_fetch_array(mysqli_query($conn, "SELECT nombre FROM comunidades WHERE id_comunidad=$id_comunidad"));
?>
  <h3 class="hide-on-med-and-down">Cotejar pago en Adaptix <?php echo $id_pago;?></h3>
  <h5 class="hide-on-large-only">Cotejat pago de Adaptix <?php echo $id_pago; ?></h5>
  <br><br>
  <div id="resultado_update_tel">
  </div>
   <div class="row">
   <ul class="collection">
            <li class="collection-item avatar">
              <img src="../img/cliente.png" alt="" class="circle">
              <span class="title"><b>No. Cliente: </b><?php echo $cliente['id_cliente'];?></span>
              <p><b>Nombre(s): </b><?php echo $cliente['nombre'];?><br>
                <b>Telefono: </b><?php echo $cliente['telefono'];?><br>
                <b>Comunidad: </b><?php echo $comunidad['nombre'];?><br>
                <?php 
                  if ($resultado['tipo'] == 'Mes-Tel'){
                    $tipo_tel = 'MENSUALIDAD';
                    $da= 'date';
                  }else if ($resultado['tipo'] == 'Min-extra') {
                     $tipo_tel = 'MINUTOS EXTRA';
                     $da = 'hidden';
                  }
                ?>
                <b>Tipo de pago:  <a class="blue-text"><?php echo $tipo_tel;?></a></b><br>
                <b>Cantidad pagada: </b><?php echo "$". $resultado['cantidad'];?><br>
                <b>Fecha de suscripcion: </b><?php echo $cliente['fecha_instalacion'];?><br>
                <b>Fecha de Corte: </b><?php echo $cliente['corte_tel']; ?><br>
                <b>Registro: </b><?php echo $usuario['firstname'].' ('.$usuario['user_name'].')'; ?><br>
                <span class="new badge pink hide-on-med-and-up" data-badge-caption="<?php echo $resultado['fecha'];?>"></span><br>
              </p>
              <a class="secondary-content "><span class="new badge pink hide-on-small-only" data-badge-caption="<?php echo $resultado['fecha'];?>"></span></a>
            </li>
        </ul>
    <form class="col s12">
    <input id="id_pago" type="hidden" class="validate" data-length="200" value="<?php echo $id_pago;?>" required>
    <input id="id_cliente" type="hidden" class="validate" data-length="200" value="<?php echo $cliente['id_cliente'];?>" required>
        <div class="input-field col l6 m6 s12">
          <h6>Registro de pago</h6>
          <select id="atendido" required>
            <option selected value="">¿El pago fue registrado en Adaptix?</option>
            <option value="1">No registrado en Adaptix</option>
            <option value="2">Registrado en Adaptix</option>
          </select>
        </div>
        
        <div class="input-field col s12 m6 l6">
          <h6 >Fecha de próximo corte</h6>        
          <input type="<?php echo $da; ?>" name="fecha_corte" id="fecha_corte" value="<?php echo $cliente['corte_tel']; ?>">
        </div>
    </form>
      <a onclick="update_reporte();" class="waves-effect waves-light btn pink right"><i class="material-icons right">send</i>COTEJAR PAGO</a>
  </div> 
</div>
<br>
</body>
<?php
}
?>
</main>
</html>
