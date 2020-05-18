<!DOCTYPE html>
<html lang="en">
<head>
<?php
  include('fredyNav.php');
  include ('../php/conexion.php');
$tecnico = $_SESSION['user_name'];
?>
<title>SIC | Alta Instalaciones</title>
<script>
function insert_abono(id) {   
    var textoCantidad = $("input#abono").val();

    if(document.getElementById('banco').checked==true){
      textoTipo_cambio   = "Banco";
    }else{
      textoTipo_cambio = "Efectivo";
    }

    if (textoCantidad == "" || textoCantidad == 0) {
      M.toast({html:"El campo abono no puede ir vacio o en 0.", classes: "rounded"});
    }else{
      $.post("../php/insert_abono_inst.php", {
          valorCantidad: textoCantidad,
          valorTipo_Cambio: textoTipo_cambio,
          valorIdCliente: id
        }, function(mensaje) {
            $("#resultado_cliente").html(mensaje);
        }); 
    }
};
</script>
</head>
<?php
if (isset($_POST['id_cliente']) == false) {
  ?>
  <script>
    function atras(){
      M.toast({html: "Regresando a instalaciones pendientes", classes: "rounded"});
      setTimeout("location.href='instalaciones.php'",1000);
    }
    atras();
  </script>
  <?php
}else{
  ?>
<body>
<div class="container" id="resultado_cliente">
</div>
	<div class="container row">
    	<?php
        $id_cliente = $_POST['id_cliente'];
        $datos = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM clientes WHERE id_cliente=$id_cliente"));

        $id_comunidad = $datos['lugar'];
        $comunidad = mysqli_fetch_array(mysqli_query($conn, "SELECT nombre FROM comunidades WHERE id_comunidad=$id_comunidad"));

        $id_paquete = $datos['paquete'];
        $paquete = mysqli_fetch_array(mysqli_query($conn, "SELECT subida, bajada FROM paquetes WHERE id_paquete=$id_paquete"));
        ?>
      <div class="row" >
      <h3 class="hide-on-med-and-down">Abono de Instalacion</h3>
      <h5 class="hide-on-large-only">Abono de Instalacion</h5>
      </div>
        <ul class="collection">
            <li class="collection-item avatar">
              <img src="../img/cliente.png" alt="" class="circle">
              <span class="title"><b>No. Cliente: </b><?php echo $datos['id_cliente'];?></span>
              <p><b>Nombre(s): </b><?php echo $datos['nombre'];?><br>
                <b>Telefono: </b><?php echo $datos['telefono'];?><br>
                <b>Comunidad: </b><?php echo $comunidad['nombre'];?><br>
                <b>Direccion: </b><?php echo $datos['direccion'];?><br>
                <b>Referencia: </b><?php echo $datos['referencia'];?><br>
                <b>Comunidad: </b><?php echo $comunidad['nombre'];?><br>
                <b>Paquete: </b> Subida: <?php echo $paquete['subida'];?> | Bajada: <?php echo $paquete['bajada'];?><br>
                <b>Total: </b>$<?php echo $datos['total'];?><br>
                <b>Dejó: </b>$<?php echo $datos['dejo'];?><br>
                <b>Resta: </b>$<?php echo $datos['total']-$datos['dejo'];?><br>              
              </p>
            </li>
        </ul>
        <div class="row">
        <form class="col s12">
          <div class="row">
            <div class="col s1"><br></div>
            <div class="input-field col s12 m5 l5">
              <i class="material-icons prefix">local_atm</i>
              <input id="abono" type="number" class="validate" data-length="6" required>
              <label for="abono">Abono:</label>
            </div>
            <div class="col s12 m4 l4">
              <p>
                <br>
                <input type="checkbox" id="banco"/>
                <label for="banco">Banco</label>
              </p>
            </div>
          </div>
        </form>
        </div>
        <div class="row">
          <a onclick="insert_abono(<?php echo $datos['id_cliente'];?>);" class="waves-effect waves-light btn pink right"><i class="material-icons right">send</i> Registrar Abono </a>          
        </div>
    </div>
    <?php
    mysqli_close($conn);
    ?>
</body>
<?php
}
?>
</html>