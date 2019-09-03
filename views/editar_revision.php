<!DOCTYPE html>
<html>
<head>
	<title>SIC | REVISION edit</title>
<?php
include ("fredyNav.php");
include ('../php/conexion.php');
$id_cliente = $_POST['id_cliente'];
$cliente = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM clientes WHERE id_cliente = '$id_cliente'"));
$id_paquete = $cliente['paquete'];
$paquete_cliente = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM paquetes WHERE id_paquete='$id_paquete'"));
?>
<script>
	function update_fecha_cortre(){
		textoIdCliente = <?php echo $id_cliente; ?>;
		var textoFecha = $("input#fecha_corte").val();
		var textoPaquete = $("select#paquete").val();
  
		if(textoPaquete == "0"){
      		M.toast({html: "No se ha seleccionado un paquete de internet aún.", classes: "rounded"});
      	}else{
		$.post("../php/update_fecha_cortre.php", {
          valorIdCliente: textoIdCliente,
          valorFecha: textoFecha,
          valorPaquete: textoPaquete
          }, function(mensaje) {
              $("#resultado_update").html(mensaje);
        });
		}
	};
</script>
</head>
<body>
	<div class="container">
	  <br>
	  <div id="resultado_update"></div>
	  <h3 class="hide-on-med-and-down">Editar</h3>
	  <h5 class="hide-on-large-only">Editar</h5>
	  <br>
	  <div class="row">
      <div class="col s12 m6 l6">
        <h5><?php echo "No.".$id_cliente."  ".$cliente['nombre'];?></h5>
      </div>
      <div class="input-field col s12 m3 l3"><br>
          <i class="material-icons prefix">date_range</i>
          <input id="fecha_corte" type="date" class="validate" value="<?php echo $cliente['fecha_corte'];?>" required>
          <label for="fecha_corte">Fecha de Corte:</label>
      </div>
      <div class="col s12 m3 l3">
      <label><i class="material-icons">import_export</i>Paquete:</label>
        <div class="input-field ">
          <select id="paquete" class="browser-default" required>
            <option value="<?php echo $paquete_cliente['id_paquete'];?>" selected>$<?php echo $paquete_cliente['mensualidad'];?> Velocidad: <?php echo $paquete_cliente['bajada'].'/'.$paquete_cliente['subida'];?></option>
            <?php
                $sql = mysqli_query($conn,"SELECT * FROM paquetes");
                while($paquete = mysqli_fetch_array($sql)){
                  ?>
                    <option value="<?php echo $paquete['id_paquete'];?>">$<?php echo $paquete['mensualidad'];?> Velocidad: <?php echo $paquete['bajada'].'/'.$paquete['subida'];?></option>
                  <?php
                } 
                mysqli_close($conn);
            ?>
          </select>
        </div>
      </div>      
      <div class="input-field col s12 m12 l12">
        <a onclick="update_fecha_cortre();" class="waves-effect waves-light btn pink left right"><i class="material-icons center">send</i></a>
      </div>
  	  </div>
	</div>
</body>
</html>