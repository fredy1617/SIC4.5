<!DOCTYPE html>
<html>
<head>
	<title>SIC | SISTEMA VS MIKROTIK</title>
<?php 
include('fredyNav.php');
?>
<script >
function servInfo() {
    var id = $("select#servidor").val();
    if (id == 0) {
      M.toast({html:"Seleccione un servidor.", classes: "rounded"});
    }else{
      M.toast({html: 'Esto puede tardar unos minutos espera...', classes: 'rounded'});
	  $.post("../php/servidor_info.php", {
	    id: id,
	  }, function(mensaje) {
	    $("#info").html(mensaje);
	  }); 
	}
};
function tabInfoMik(){
	var id = $("select#servidor").val();
    M.toast({html:"Llenando tabla temporal MIKROTIK.", classes: "rounded"});

    if (id == 0) {
      M.toast({html:"Seleccione un servidor.", classes: "rounded"});
    }else{
	  $.post("../php/info_queue.php", {
	    id: id,
	  }, function(mensaje) {
	    $("#llenado").html(mensaje);
	  }); 
	}
};
function vaciar(es){
	var id = $("select#servidor").val();
    M.toast({html:"Funcion para eliminar los queues del servidor seleccionado.", classes: "rounded"});0
    if (id == 0) {
      M.toast({html:"Seleccione un servidor.", classes: "rounded"});
    }else{
	  $.post("../php/eliminar_queues.php", {
	    id: id,
	  }, function(mensaje) {
	    $("#vaciar").html(mensaje);
	  }); 
	}
};
function comparar(id){
    M.toast({html:"Funcion para comparar MIKROTIK vs SISTEMA.", classes: "rounded"});
    M.toast({html:"Puede tardar algunos minutos...", classes: "rounded"});
    $.post("../php/comparar_queues.php", {
	    id: id
	}, function(mensaje) {
	    $("#tabla").html(mensaje);
	}); 
};
function comparar2(id){
    M.toast({html:"Funcion para comparar MIKROTIK vs SISTEMA.", classes: "rounded"});
    M.toast({html:"Puede tardar algunos minutos...", classes: "rounded"});
    $.post("../php/comparar_queues_mikrotik.php", {
	    id: id
	}, function(mensaje) {
	    $("#tabla").html(mensaje);
	}); 
};
</script>
</head>
<body>
	<div class="container">
		<div class="row">
			<br><br>
			<div id="Continuar"></div>
			<h3 class="hide-on-med-and-down col s12 m5 l5 blue-text">Sistema VS Mikrotik:</h3>
      		<h5 class="hide-on-large-only col s12 m5 l5 blue-text">Sistema VS Mikrotik:</h5>
      		<div class="input-field col l3 m3 s10"><br>
		        <select id="servidor" class="browser-default">
		          <option value="0" selected>Seleccione un servidor</option>
		          <?php 
		          $sql = mysqli_query($conn,"SELECT * FROM servidores ");
		          while($Servidor = mysqli_fetch_array($sql)){
		          ?>
		            <option value="<?php echo $Servidor['id_servidor'];?>"><?php echo $Servidor['nombre'];?></option>
		          <?php
		          }
		          ?>
		        </select>
		    </div> 
		    <div class="col l2 m2 s4"><br><br>
		      <button class="btn waves-light waves-effect right pink" onclick="servInfo();tabInfoMik();">LLenar<i class="material-icons prefix right">list</i></button>
		    </div> 
		    <div class="col l2 m2 s4"><br><br>
		      <button class="btn waves-light waves-effect right red darken-2" onclick="vaciar();">Vaciar<i class="material-icons prefix right">delete</i></button>
		    </div> 
		</div>
		<div id="llenado"></div>
		<div id="vaciar"></div>
		<div class="row col s12" id="info">
			<!-- Informacion del servidor en forma de botones -->		
			<?php
			if (isset($_GET['id']) == true) {
				$id = $_GET['id'];//RECIBIMOS EL ID DEL SERVIDOR AL QUE SE LE HIZO LA PETICION DE LOS QUEUES
				$serv = mysqli_fetch_array(mysqli_query($conn,"SELECT * FROM servidores WHERE id_servidor = $id"));

				#VERIFICAMOS QUE SE HAYAN INSERTADO A LA TABLA tmp_mikrotik DICHOS QUEUES PERTENECIENTES AL SERVIDOR
				if (mysqli_num_rows(mysqli_query($conn, "SELECT * FROM tmp_mikrotik WHERE servidor = '$id'")) >0) {
					#SI ES CORRECTO MOSTRAMOS UN BOTO QUE ACTIVARA LA FUNCION DE COMPARAR AMBAS TABLAS
					echo "<h4>Servidor: ".$serv['nombre']."(".$id.")</h4>";
					echo '<br><div class="col s1"><br></div><div class="center col s12 l5 m5"><a class="col s12 waves-effect waves-light btn-large green" onclick = "comparar('.$id.');"><i class="material-icons left">info_outline</i>COMPARAR EN SISTEMA Y NO EN MIKROTIK</a></div><div class="center col s12 l5 m5"><a class="col s12 waves-effect waves-light btn-large red" onclick = "comparar2('.$id.');"><i class="material-icons left">info_outline</i>COMPARAR EN MIKROTIK Y NO EN SISTEMA</a></div>';
				}else{
					echo '<br><div class="col s3"><br></div><div class="center col s12 l6 m6"><h4 class="red-text">NO SE ENCONTRARON QUEUES</h4><div class="col s3"></div>';
					echo '<br><div class="col s2"><br></div><div class="center col s12 l8 m8"><h5>Servidor: '.$serv['nombre'].'('.$id.')</div>';
				}
			}
			?>
			<div id="tabla"></div>
		</div>
	</div>
</body>
</html>