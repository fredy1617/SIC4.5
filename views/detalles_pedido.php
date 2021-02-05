<html>
<head>
	<title>SIC | Detalles Pedido</title>
</head>
<?php 
include('../views/fredyNav.php');
include('../php/conexion.php');
if (isset($_GET['folio']) == false) {
  ?>
  <script>
    function atras(){
      M.toast({html: "Regresando a pedidios", classes: "rounded"});
      setTimeout("location.href='pedidos.php'",1000);
    }
    atras();
  </script>
  <?php
}else{
date_default_timezone_set('America/Mexico_City');
$Fecha_Hoy = date('Y-m-d');
$folio = $_GET['folio'];
$user_id = $_SESSION['user_id'];
$Pedido = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM pedidos WHERE folio = $folio"));
?>
<script>
function add_material(){
  var textoDescripcion = $("input#descripcion").val();
  var textoProveedor = $("input#proveedor").val();
  textoFolio = <?php echo $folio; ?>;

  $.post("../php/add_material.php", { 
        valorDescripcion: textoDescripcion,
        valorProveedor: textoProveedor,
        valorFolio:textoFolio
  }, function(mensaje) {
  $("#materialALL").html(mensaje);
  }); 
};
function actualizaCheck(id){
  textoFolio = <?php echo $folio; ?>;
  if (document.getElementById('todos'+id).checked==true) {
      textoListo = 1;
  }else{
      textoListo = 0;
  } 
  $.post("../php/actualizaCheck.php", { 
        valorFolio:textoFolio,
        valorListo:textoListo,
        valorID:id
  }, function(mensaje) {
  $("#materialALL").html(mensaje);
  }); 
};
function borrar(id){
  textoFolio = <?php echo $folio; ?>;
  $.post("../php/borrar_material.php", { 
          valorFolio:textoFolio,
          valorID: id
  }, function(mensaje) {
  $("#materialALL").html(mensaje);
  }); 
};
function selCerrar(){
  $(document).ready(function(){
      $('#cerrar').modal();
      $('#cerrar').modal('open'); 
  });
};
function selObservacion(id){
  textoFolio = <?php echo $folio; ?>;
  $.post("../views/modal_observacion.php", { 
          valorFolio:textoFolio,
          valorID: id
  }, function(mensaje) {
  $("#materialALL").html(mensaje);
  }); 
};
  
</script>
<body>
<div class="container">
  <div id="materialALL"></div>
   <div class="row"><br><br>
   <ul class="collection">
        <?php
        $id_orden = $Pedido['id_orden'];
        if ($id_orden >= 100000) {
          #ES UNA ORDEN DE SERVICIO
          $orden = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM orden_servicios WHERE id = $id_orden"));
          $Hacer = $orden['solicitud'];
          $accion = '<a href="atender_orden.php?id_orden='.$id_orden.'" class="">'.$id_orden.'</a> - ('.$orden['solicitud'].').';
        }else{
          #ES UN MANTENIMIENTO
          $mantenimiento = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM reportes WHERE id_reporte = $id_orden"));
          $accion = '<form action="atender_reporte.php" method="post"><input type="hidden" name="id_reporte" value="'.$id_orden.'"><button type="submit" class="">'.$id_orden.'</button> - ('.$mantenimiento['descripcion'].').</form>';
        }
        ?>
        <li class="collection-item avatar">
            <img src="../img/cliente.png" alt="" class="circle">
            <span class="title"><b>No. Folio: </b><?php echo $folio;?></span><br>
            <b>Cliente: </b><?php echo $Pedido['nombre'];?><br>
            <b>Orden: </b><?php echo $accion;?><br>
            <b>Fecha de Creación: </b><?php echo $Pedido['fecha'];?><br>
            <b>Hora de Creación: </b><?php echo $Pedido['hora'];?><br>
            <div class="row col s10"><br>
              <b>Acción : </b>
              <div class="right">
              <?php  if ($Pedido['cerrado'] == 0) {  ?>
                <a onclick="selCerrar();" class="waves-effect waves-light btn pink <?php echo ($user_id == $Pedido['usuario'])? '':'disabled'; ?>"><i class="material-icons right">lock</i>CERRAR PEDIDO</a> 
              <?php } else if ($Pedido['cerrado'] == 1 AND $Pedido['estatus'] == 'No Autorizado')  {  // FIN IF $Hay ?>
                <form method="post" action="../php/autorizar_pedido.php"><input type="hidden" name="folio" value="<?php echo $folio;?>"><button type="submit" class="btn pink waves-effect waves-light <?php echo($user_id == 10 OR $user_id == 49 OR $user_id == 56)? '':'disabled'; ?>"><i class="material-icons right">check</i>Autorizar Pedido</button></form>
              <?php } // FIN IF ?>                    
              </div>
            </div>
            <a href="../php/imprimir_pedido.php?folio=<?php echo $folio;?>" target="blank" class="waves-effect waves-light btn pink right"><i class="material-icons right">print</i>IMPRIMIR PEDIDO</a>
        </li>
    </ul><br>
    <?php if (($Pedido['cerrado'] == 0) OR ($Pedido['cerrado'] == 1 AND ($user_id == 10 OR $user_id == 49))) { ?>
    <h5>Agregar Material</h5>
    <form class="row">
    	<div class="input-field col s12 m5 l5">
          <i class="material-icons prefix">edit</i>
          <input id="descripcion" type="text" class="validate" data-length="100" required>
          <label for="descripcion">Maretrial (Nombre y descripcion):</label>
      </div>
      <div class="input-field col s12 m4 l4">
          <i class="material-icons prefix">contact_mail</i>
          <input id="proveedor" type="text" class="validate" required>
          <label for="proveedor">Proveedor Sujerido:</label>
      </div>
      <a onclick="add_material();" class="waves-effect waves-light btn pink"><i class="material-icons right">send</i>Agregar</a> 
    </form>
    <?php
    } //FIN IF MATERIAL
    $LISTOS = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM detalles_pedidos WHERE folio = $folio AND listo = 1"));
    $detalles_pedido = mysqli_query($conn, "SELECT * FROM detalles_pedidos WHERE folio = $folio");
    $TOTAL = mysqli_num_rows($detalles_pedido);
    $color = ($LISTOS == $TOTAL)? 'green':'red';
    $user = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM users WHERE user_id = $user_id"));
    $Check = 'disabled';
    $Button = 'disabled';
    if ((($user_id == 10 OR $user_id == 49 OR $user_id == 25 OR $user_id == 28 OR $user['area'] == 'Redes') AND $Pedido['cerrado'] == 0) OR ($Pedido['cerrado'] == 1 AND ($user_id == 10 OR $user_id == 49))) {
      $Button = '';
    }
    if (($user_id == 10 OR $user_id == 49 OR $user_id == 66 OR $user_id == 56) AND $Pedido['cerrado'] == 1 AND $Pedido['estatus'] == 'Autorizado') {
      $Check = '';
    }
    ?>
    <h4>Material (<b class="<?php echo $color; ?>-text"><?php echo $LISTOS; ?> / <?php echo $TOTAL; ?></b>):</h4>
    <form class="col s12">
    	<table>
    		<thead>
    			<tr>
    				<th>Listo</th>
            <th>Descripcion</th>
            <th>Proveedor</th>
            <th>Registro</th>
            <th>Observacion</th>
    				<th>Observo</th>
    				<th>Borrar</th>
    			</tr>
    		</thead>
    		<tbody>
    		<?php
    		if($TOTAL>0){
    			while($material = mysqli_fetch_array($detalles_pedido)){
            $user_id_mat = $material['usuario']; 
            $user_mat = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM users WHERE user_id = $user_id_mat"));
            $user_id_o = $material['observo']; 
            $user_o = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM users WHERE user_id = $user_id_o"));
    		?>
    			<tr>
    				<td><p>
		            <input <?php echo $Check; ?> type="checkbox" <?php echo ($material['listo'] == 1 )? 'checked': ''; ?> onclick="actualizaCheck(<?php echo $material['id']; ?>);" id="todos<?php echo $material['id'] ?>"/>
		            <label for="todos<?php echo $material['id'] ?>"></label>
         			</p></td>
            <td><?php echo $material['descripcion']; ?></td>
            <td><?php echo $material['proveedor']; ?></td>
    				<td><?php echo $user_mat['firstname']; ?></td>
            <td><?php if ($Pedido['cerrado'] == 1 AND $Pedido['estatus'] == 'No Autorizado' AND ($user_id == 10 OR $user_id == 49 OR $user_id == 56)) { 
              echo ($material['observacion'] == 'N/A')? '<a onclick="selObservacion('.$material['id'].');" class="waves-effect waves-light btn-small pink"><i class="material-icons left">edit</i>AGREGAR</a> ': $material['observacion']; 
              }else{ echo  'N/A';}?></td>
            <td><?php echo $user_o['firstname']; ?></td>
    				<td><a onclick="borrar(<?php echo $material['id'] ?>);" class="btn btn-floating red darken-1 waves-effect waves-light <?php echo $Button; ?>"><i class="material-icons">delete</i></a></td>
    			</tr>    			
    		<?php
    			}
    		}
    		?>
    		</tbody>
    	</table>
    </form>  
  </div> 
</div>
<!-- Modal CERRAR PEDIDO IMPOTANTE! -->
<div id="cerrar" class="modal"><br>
  <div class="modal-content">
    <h4 class="red-text darken-2 center"><b>¿ESTAS SEGURO DE CERRAR EL PEDIDO?</b></h4><br>
    <h6 class="red-text darken-1 "><b>Una vez cerrado el pedido no se podra modificar (ni agregar, ni eliminar material)</b></h6>
  </div><br>
  <div class="modal-footer">
      <form method="post" action="../php/cerrar_pedido.php" class="right"><input name="folio" type="hidden" value="<?php echo $folio; ?>"><button type="submit" class="btn green accent-4 waves-effect waves-light"><i class="material-icons right">send</i>ACEPTAR</button></form>
      <a href="#" class="modal-action modal-close waves-effect waves-green btn red accent-4">CANCELAR<i class="material-icons right">close</i></a>
  </div><br>
</div>
<!--Cierre modal CERRAR PEDIDO IMPOTANTE! -->
</body>
<?php } ?>  
</html>
