<!DOCTYPE html>
<html>
<head>
	<title>SIC | Atender Dispositivo</title>
</head>
<?php
include ('fredyNav.php');
include('../php/conexion.php');
include('../php/cobrador.php');
if (isset($_POST['id_dispositivo']) == false) {
  ?>
  <script>    
    function atras() {
      M.toast({html: "Regresando a pendientes.", classes: "rounded"})
      setTimeout("location.href='pendientes.php'", 1000);
    }
    atras();
  </script>
  <?php
}else{
  $id_dispositivo = $_POST['id_dispositivo'];
?>
<script>
function borrar(IdPago){

  $.post("../php/borrar_anti.php", { 
          valorIdPago: IdPago,
  }, function(mensaje) {
  $("#borrar_pagos").html(mensaje);
  }); 
}
function insert_anticipo(){
	var textoMonto = $("input#monto").val();
	textoIdDispositivo = <?php echo $id_dispositivo; ?>;

	if (document.getElementById('banco').checked == true) {
		textoTipo_Cambio = "Banco";
	}else{
		textoTipo_Cambio = "Efectivo";
	}
	if (textoMonto == "" || textoMonto == 0) {
		M.toast({html: "El campo Cantidad se encuentra vacío o en 0.", classes: "rounded"});
	}else{
		$.post("../php/insert_anticipo.php", {
			valorMonto : textoMonto,
			valorIdDispositivo: textoIdDispositivo,
			valorTipo_Cambio:textoTipo_Cambio,
		}, function(mensaje) {
			$("#resp_anticipo").html(mensaje);
		});
	}
};
function salida(){
	  var textoLink = $("textarea#link").val();
    var textoObservaciones = $("textarea#observaciones").val();
    var textoManoObra = $("input#mano").val();
    var textoEstatus = $("select#estatus").val();
    var textoExtras = $("input#extra").val();
    var textoContra = $("input#contra").val();
    textoIdDispositivo = <?php echo $id_dispositivo; ?>;

    n=1;
    textoRefacciones = '';
    entra = "Si";
    while(n<11){
      var textoNumero = $("input#numero"+n).val();
      if (textoNumero == null) {
          //M.toast({html :"No hay numero."+n, classes: "rounded"});
        }else{
          //M.toast({html :"Es: "+textoNumero, classes: "rounded"});
        var textoDesc = $("input#Desc_"+n).val();
        var textoPrecioR = $("input#precio"+n).val();
        if (textoDesc == "") {
           M.toast({html :"Ingrese una Descripción En: Descripcion No."+n, classes: "rounded"});
           entra = "No";
        }else if (textoPrecioR == "") {
           M.toast({html :"Ingrese un Precio En: Precio No."+n, classes: "rounded"});   
           entra = "No";     
        }else{
          entra = "Si";
          textoRefacciones += textoDesc+" - "+textoPrecioR+", ";
        }
        }
        n++;
    }
    if (entra == "No") {
      M.toast({html:"Dice que no.", classes: "rounded"});
    }else if(textoManoObra == "" || textoManoObra == 0){
      M.toast({html:"El campo Mano de Obra se encuentra vacío.", classes: "rounded"});
    }else if(textoEstatus == '0'){
      M.toast({html:"Seleccione un Estatus por favor.", classes: "rounded"});
    }else {
      $.post("../php/update_dispositivo.php", {
          valorLink: textoLink,
          valorObservaciones: textoObservaciones,
          valorManoObra: textoManoObra,
          valorIdDispositivo: textoIdDispositivo,
          valorEstatus: textoEstatus,
          valorExtras: textoExtras,
          valorContra: textoContra,
          valorRefacciones: textoRefacciones
        }, function(mensaje) {
            $("#refrescar").html(mensaje);
        }); 
    }
};
</script>
<body>
	<div class="container" id="refrescar">
		<div class="row">
	      <h3 class="hide-on-med-and-down">Atender Dispositivo:</h3>
	      <h5 class="hide-on-large-only">Atender Dispositivo:</h5>
    	</div>
    <?php        
        $datos = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM dispositivos WHERE id_dispositivo = $id_dispositivo"));
    ?>
      <div class="row">
   		<ul class="collection">
            <li class="collection-item avatar">
              <div class="hide-on-large-only"><br><br></div>
              <img src="../img/cliente.png" alt="" class="circle">
              <span class="title"><b>Folio: </b><?php echo $datos['id_dispositivo'];?></span>
              <p><b>Nombre: </b><?php echo $datos['nombre'];?><br>
                <b>Telefono: </b><?php echo $datos['telefono'];?><br>
                 <b>Dispositivo: </b><?php echo $datos['tipo'];?> <?php echo $datos['marca'];?><br>
                 <b>Modelo: </b><?php echo $datos['modelo'];?><br>
                 <?php if ($datos['extras'] == NULL) {
                    $ext = 'Color '.$datos['color'].' con '.$datos['cables'];
                 }else{
                    $ext = $datos['extras'];
                 }
                 ?>  
                 <div class="col s12">
                  <b class="col s4 m2 l2">Extras: </b>
                  <div class="col s12 m6 l6">
                    <input type="text" id="extra" name="extra" value="<?php echo $ext;?>">
                  </div>
                 </div>   
                 <div class="col s12">
                  <b class="col s4 m2 l2">Contraseña: </b>
                  <div class="col s12 m6 l6">
                    <input type="text" id="contra" name="contra" value="<?php echo $datos['contra'];?>">
                  </div>
                 </div>            
                 <?php 
                 if ($datos['precio']==0) {
                 	$Tot = $datos['mano_obra']+$datos['t_refacciones'];
                 }else{
                 	$Tot = $datos['precio'];
                 }
                  $sql = mysqli_query($conn, "SELECT * FROM pagos WHERE id_cliente = '$id_dispositivo' AND descripcion = 'Anticipo' AND tipo = 'Dispositivo'");
                  $Total_anti = 0;
                  if (mysqli_num_rows($sql)>0) {
                    
                    while ($anticipo = mysqli_fetch_array($sql)) {

                      $Total_anti += $anticipo['cantidad'];
                    }
                  }
                 $resto = $Tot-$Total_anti;
                
                 ?>
                 <b>Total: </b><?php echo "$".$Tot;?><br>
                 <b>Anticipo: </b><?php echo "$".$Total_anti;?><br>
                 <b>Resta: </b><?php echo "$".$resto;?><br>

                 <hr>
                 <b>Falla: </b><?php echo $datos['falla'];?>
              </p>
              <br>
              <a href="#!" class="secondary-content"><span class="new badge green" data-badge-caption="<?php echo 'FECHA DE ENTRADA: '.$datos['fecha'];?>"></span></a>
            </li>
      </ul>
      </div>
    <div class="row">	
    		<div class="row col s12">
    			<div class="col s12 m3 l3">
    			<div id="resp_anticipo"></div>
    			<h3 class="hide-on-med-and-down">Abonar:</h3>
     			<h5 class="hide-on-large-only">Abonar:</h5>
     			</div>
     			<form class="col s12 m9 l9">        
		      		<div class="row col s9 m5 l5">
		        	<div class="input-field">
		          	  <i class="material-icons prefix">payment</i>
		          	  <input id="monto" type="number" class="validate" data-length="6" value="0" required>
		          	  <label for="monto">Cantidad:</label>
		        	</div>
		            </div>
		            <div class="col s3 m4 l4">
			        <p><br>
			            <input type="checkbox" id="banco"/>
			            <label for="banco">Banco</label>
			          </p>
		        </div><br>
		        <a onclick="insert_anticipo();" class="waves-effect waves-light btn pink  col s8 m3 l3"><i class="material-icons right">send</i>Registrar Anticipo</a>
		      </form>    			
    		</div>
    		<div class="row">
            <div id="borrar_pagos">
              <table>
                <thead>
                  <th>#</th>
                  <th>Descripcion</th>
                  <th>Fecha</th>
                  <th>Cantidad</th>
                  <th>Borrar</th>
                </thead>
                <tbody>                 
                  <?php
                  $sql = mysqli_query($conn, "SELECT * FROM pagos WHERE id_cliente = '$id_dispositivo' AND descripcion = 'Anticipo' AND tipo = 'Dispositivo'");
                  $Total = 0;
                  if (mysqli_num_rows($sql)>0) {
                    $aux= 0;
                    
                    while ($anticipo = mysqli_fetch_array($sql)) {
                      $aux++;
                      $Total += $anticipo['cantidad'];
                      ?>
                    <tr>
                      <td><?php echo $aux; ?></td>
                      <td><?php echo $anticipo['descripcion']; ?></td>
                      <td><?php echo $anticipo['fecha'].' '.$anticipo['hora']; ?></td>
                      <td>$<?php echo $anticipo['cantidad']; ?></td>
                      <td><a onclick="borrar(<?php echo $anticipo['id_pago']; ?>);" class="btn btn-floating red darken-1 waves-effect waves-light"><i class="material-icons">delete</i></a></td>
                    </tr>
                      <?php
                    }
                  }
                  ?>
                  <tr>
                  	<td></td>
                  	<td></td>
                  	<td><b>TOTAL:</b></td>
                  	<td>$<?php echo $Total; ?></td>
                  </tr>
                </tbody>
              </table>
            </div>
        </div>
    	<form class="col s12" action="../php/folioSalida.php" target="_blank" method="POST"><br>
    		    <div class="input-field col s12 m6 l6">
              <i class="material-icons prefix">insert_link</i>
              <textarea id="link" class="materialize-textarea validate" data-length="150" ><?php echo $datos['link'];?></textarea>
              <label for="link">Link del Articulo:</label>
            </div> 
            <div class="input-field col s12 m6 l6">
              <i class="material-icons prefix">comment</i>
              <textarea id="observaciones" class="materialize-textarea validate" data-length="150" ><?php echo $datos['observaciones'];?></textarea>
              <label for="observaciones">Observaciones:</label>
            </div> 
            <div class="row">
            <h4>Refacciones:</h4>
            <div id="refa_borrar">
              <table>
                <thead>
                  <th>#</th>
                  <th>Refacción</th>
                  <th>Precio</th>
                  <th>Borrar</th>
                </thead>
                <tbody>                 
                  <?php
                  $sql = mysqli_query($conn, "SELECT * FROM refacciones WHERE id_dispositivo = '$id_dispositivo' ");
                  $Total = 0;
                  if (mysqli_num_rows($sql)>0) {
                    $aux= 0;
                    while ($refas = mysqli_fetch_array($sql)) {
                      $aux++;
                      $Total += $refas['cantidad'];
                      ?>
                    <tr>
                      <td><?php echo $aux; ?></td>
                      <td><?php echo $refas['descripcion']; ?></td>
                      <td>$<?php echo $refas['cantidad']; ?></td>
                      <td><a onclick="borrar_refa(<?php echo $refas['id_refaccion']; ?>);" class="btn btn-floating red darken-1 waves-effect waves-light"><i class="material-icons">delete</i></a></td>
                    </tr>
                      <?php
                    }
                  }else{
                  	echo "<h4 class = 'center'>No hay refacciones</h4>";
                  }
                  ?>
                  <tr>
                  	<td></td>
                  	<td><b>SUBTOTAL:</b></td>
                  	<td>$<?php echo $Total; ?></td>
                  </tr>
                </tbody>
              </table>
            </div>
            <div class="button">
             <button type="button" id="add_Desc" class="waves-effect waves-light btn pink right"><i class="material-icons right">add</i>Agregar</button>
            </div>
          </div><br> 
          <div class="row">
          	<div class="col s12 m4 l4">
        		<label><i class="material-icons">assignment_late</i> Estatus:</label>
	          	<div class="input-field">
	              <select id="estatus" class="browser-default">
	                <option value="<?php echo $datos['estatus'];?>" selected><?php echo $datos['estatus'];?></option>
	                <option value="Cotizado">Cotizado</option>
	                <option value="En Proceso">En Proceso</option>
	                <option value="Listo">Listo</option>
	                <option value="Listo (No Reparado)">Listo (No Reparado)</option>
	              </select>
	            </div>
	          </div>
	          <div class="input-field col s12 m3 l3"><br>
                <i class="material-icons prefix">monetization_on</i>
                <input id="mano" type="number" class="validate" data-length="6" value="<?php  if($datos['precio'] > 0){ echo $datos['precio']; }else{ echo $datos['mano_obra'];}?>" required>
                <label for="mano">Mano de Obra:</label>
              </div>
              <div class="input-field col s12 m3 l3"><br>
              	<h5><b>TOTAL: $<?php if($datos['precio'] > 0){ echo $datos['precio']; }else{ echo $datos['mano_obra']+$datos['t_refacciones'];}?></b></h5>
              </div>
              <div>
               <br><br>
               <a onclick="salida();" class="waves-effect waves-light btn pink right tooltipped" data-position="bottom" data-tooltip="Solo guardar"><i class="material-icons right">save</i>Guardar</a>
              </div>
	         </div>		
    	</form>    	
    </div>
	</div>
</body>
<?php
}
 mysqli_close($conn);
?>
<script>
  $(document).ready(function() {
    $("#add_Desc").click(function(){
        var contador = $("input[type='text']").length;

        $(this).before('<div class="row"><div class= "col s12 m6 l6"><div class="input-field"><i class="material-icons prefix">comment</i><input type="text" id="Desc_'+ contador +'" name="Desc[]"/><label for="Desc_'+ contador +'">Descripción No.'+ contador +' :</label></div></div><div class="col s10 m4 l4"><div class="input-field"><i class="material-icons prefix">attach_money</i><input type="number" id="precio'+ contador +'" name="precio[]"/><label for="precio'+ contador +'">Preco No.'+ contador +' :</label></div></div><input id="numero'+ contador +'" value="'+ contador +'" type="hidden"><button type="button" class="delete_Desc btn-floating btn-tiny waves-effect waves-light pink "><i class="material-icons prefix">delete</i></button></div>');
    });

    $(document).on('click', '.delete_Desc', function(){
        $(this).parent().remove();
    });
});
</script>
</html>