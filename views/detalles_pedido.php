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
$Pedido = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM pedidos WHERE folio = $folio"));
?>
<script>
function add_material(){
  var textoDescripcion = $("input#descripcion").val();
  textoFolio = <?php echo $folio; ?>;

  $.post("../php/add_material.php", { 
        valorDescripcion: textoDescripcion,
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
</script>
<body>
<div class="container">
  <div id="materialALL"></div>
   <div class="row"><br><br>
   <ul class="collection">
        <li class="collection-item avatar">
            <img src="../img/cliente.png" alt="" class="circle">
            <span class="title"><b>No. Folio: </b><?php echo $folio;?></span><br>
            <b>Cliente: </b><?php echo $Pedido['nombre'];?><br>
            <b>Orden: </b><?php echo $Pedido['id_orden'];?><br>
            <b>Fecha: </b><?php echo $Pedido['fecha'];?><br>
            <b>Hora: </b><?php echo $Pedido['hora'];?><br>
        </li>
    </ul><br>
    <h5>Agregar Material</h5>
    <form class="row">
    	<div class="col s1"><br></div>
    	<div class="input-field col s12 m6 l6">
            <i class="material-icons prefix">edit</i>
            <input id="descripcion" type="text" class="validate" data-length="6" required>
            <label for="descripcion">Maretrial (Nombre y descripcion):</label>
        </div>
        <a onclick="add_material();" class="waves-effect waves-light btn pink"><i class="material-icons right">send</i>Agregar</a> 
    </form>
    <?php
    $LISTOS = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM detalles_pedidos WHERE folio = $folio AND listo = 1"));
    $detalles_pedido = mysqli_query($conn, "SELECT * FROM detalles_pedidos WHERE folio = $folio");
    $TOTAL = mysqli_num_rows($detalles_pedido);
    $color = ($LISTOS == $TOTAL)? 'green':'red';
    $user_id = $_SESSION['user_id'];
    $user = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM users WHERE user_id = $user_id"));
    $Check = 'disabled';
    $Button = 'disabled';
    if ($user_id == 10 OR $user_id == 49 OR $user_id == 25 OR $user_id == 28 OR $user['area'] == 'Redes') {
      $Button = '';
    }
    if ($user_id == 10 OR $user_id == 49 OR $user_id == 66 OR $user_id == 56) {
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
    				<th>Registro</th>
    				<th>Borrar</th>
    			</tr>
    		</thead>
    		<tbody>
    		<?php
    		if($TOTAL>0){
    			while($material = mysqli_fetch_array($detalles_pedido)){
            $user_id_mat = $material['usuario']; 
            $user_mat = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM users WHERE user_id = $user_id_mat"));
    		?>
    			<tr>
    				<td><p>
		            <input <?php echo $Check; ?> type="checkbox" <?php echo ($material['listo'] == 1 )? 'checked': ''; ?> onclick="actualizaCheck(<?php echo $material['id']; ?>);" id="todos<?php echo $material['id'] ?>"/>
		            <label for="todos<?php echo $material['id'] ?>"></label>
         			</p></td>
            <td><?php echo $material['descripcion']; ?></td>
    				<td><?php echo $user_mat['firstname']; ?></td>
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
</body>
<?php } ?>  
</html>
