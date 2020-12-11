<!DOCTYPE html>
<html>
<head>
	<title>SIC | Atender Orden</title>
</head>
<?php
include ('fredyNav.php');
include('../php/conexion.php');
include('../php/cobrador.php');
if (isset($_POST['id_orden']) == false) {
  ?>
  <script>    
    function atras() {
      M.toast({html: "Regresando a listado de ordenes.", classes: "rounded"})
      setTimeout("location.href='ordenes_servicio.php'", 1000);
    }
    atras();
  </script>
  <?php
}else{
  $id_orden = $_POST['id_orden'];
  $tecnico = $_SESSION['user_name'];
?>
<script>
function update_orden() {
    var textoLiquidar = $("input#liquidar").val();
    var textoIdOrden = $("input#id_orden").val();
    var textoIdCliente = $("input#id_cliente").val();

    if (textoLiquidar == 0) {
      var textoLiquidarS = $("input#liquidar_s").val();

      if(document.getElementById('banco').checked==true){
        textoTipoE = 'Banco';
      }else{
        textoTipoE = 'Efectivo';
      }

      $.post("../php/update_orden_f.php", {
          valorIdOrden: textoIdOrden,
          valorLiquidarS: textoLiquidarS,
          valorIdCliente: textoIdCliente,
          valorTipoE: textoTipoE
      }, function(mensaje) {
          $("#resultado_update_orden").html(mensaje);
      });
    }else{
      $.post("../php/update_orden_f.php", {
          valorIdOrden: textoIdOrden,
          valorIdCliente: textoIdCliente,
          valorLiquidarS: 0
      }, function(mensaje) {
          $("#resultado_update_orden").html(mensaje);
      });
    }
};
</script>
<body>
	<div class="container">
		<div class="row">
	      <h3 class="hide-on-med-and-down">Atender Factura:</h3>
	      <h5 class="hide-on-large-only">Atender Factura:</h5>
    	</div>
      <div id="resultado_update_orden"></div>
    <?php   
      $orden =  mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM orden_servicios WHERE id = $id_orden"));
      $id_cliente = $orden['id_cliente'];
      $datos = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM especiales WHERE id_cliente = $id_cliente"));
      $id_counidad = $datos['lugar'];
      $Comunidad = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM comunidades WHERE id_comunidad = $id_counidad"));
    ?>
      <div class="row">
   		<ul class="collection">
            <li class="collection-item avatar">
              <div class="hide-on-large-only"><br><br></div>
              <img src="../img/cliente.png" alt="" class="circle">
              <span class="title"><b>Folio: </b><?php echo $id_orden;?></span>
              <p><b>Nombre: </b><?php echo $datos['nombre'];?><br>
                <b>Telefono: </b><?php echo $datos['telefono'];?><br>                  
                <b>Comunidad: </b><?php echo $Comunidad['nombre'];?><br>
                <b>Referencia: </b><?php echo $datos['referencia'];?><br>                
                <b>Solicitud: </b><?php echo $orden['solicitud'];?><br>             
                <b>Trabajo: </b><?php echo $orden['trabajo'];?><br>                
                <b>Material: </b><?php echo $orden['material'];?><br> 
                <b>Solucion: </b><?php echo $orden['solucion'];?><br> 
                <b>Cotizacion: </b> $<?php echo $orden['precio'];?><br> 
                  <?php
                  $totalE = 0;
                  $Extras = mysqli_query($conn, "SELECT * FROM orden_extras WHERE id_orden = $id_orden");
                  echo '<b class = "col s2">Extra(s): </b>';
                  if (mysqli_num_rows($Extras) > 0) {
                    echo '<table class = "col s6">
                        <thead>
                          <tr>
                          <th>Descripcion</th>
                          <th>Cantida</th>
                          </tr>
                        </thead>
                        <tbody>';
                    while ($extra = mysqli_fetch_array($Extras)) {
                      $totalE += $extra['cantidad'];
                      echo '<tr>
                          <td>'.$extra['descripcion'].'</td>
                          <td> $'.$extra['cantidad'].'</td>
                          </tr>';
                    }
                    echo '  </tbody>
                        </table><br><br><br><br><br><br>'; 
                  } ?><br>
                <b>TOTAL: $<?php echo $orden['precio']+$totalE;?></b><br> 
                <hr>
              </p>
              <br>
            </li>
      </ul>
      </div>
    	<form class="col s12">
        <div class="row">
            <div class="col s2"><br></div>
            <?php if ($orden['liquidada'] == 0) { ?>
            <div class="input-field col s12 m4 l4">
              <i class="material-icons prefix">local_atm</i>
              <input id="liquidar_s" type="number" class="validate" data-length="6" required>
              <label for="liquidar_s">Liquidar:</label>
            </div>
            <div class="col s8 m3 l3">
              <p>
                <br>
                <input type="checkbox" id="banco"/>
                <label for="banco">Banco</label>
              </p>
            </div> 
          <?php } ?>
            <input id="id_orden" value="<?php echo htmlentities($id_orden);?>" type="hidden">
            <input id="id_cliente" value="<?php echo htmlentities($id_cliente);?>" type="hidden">
            <input id="liquidar" value="<?php echo htmlentities($orden['liquidada']);?>" type="hidden"><br>
            <a onclick="update_orden();" class="waves-effect waves-light btn pink"><i class="material-icons right">check</i>FACTURADO</a> 
            <a href = "../php/ticket_orden.php?Id=<?php echo $id_orden;?>" target = "blank" class="waves-effect waves-light btn pink right"><i class="material-icons right">print</i>TIKET</a><br>
      </div>  
    </form>   	
    </div>
</body>
<?php
}
mysqli_close($conn);
?>
</script>
</html>