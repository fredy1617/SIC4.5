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
    var textoEstatus = $("select#estatus").val();
    var textoEstatusI = $("input#estatusI").val();
    var textoIdOrden = $("input#id_orden").val();

    if (textoEstatusI == 'Revisar') {
      var textoTrabajo = $("textarea#trabajo").val();
      var textoMaterial = $("textarea#mat").val();

      textoTecnico = '<?php echo $tecnico;?>';
      textoApoyo = 0;
      for(var i=1;i<=8;i++){
        if(document.getElementById('tecnico'+i).checked==true){
          var textoApoyo = $("input#tecnico"+i).val();
        }
      }      
      textoTecnicos = textoTecnico+', '+textoApoyo;
      if (textoApoyo == 0) {
        textoTecnicos = textoTecnico
      }

      if (textoTrabajo == "") {
        M.toast({html:"El campo Trabajo no puede ir vacio.",classes: "rounded"}); 
      }else if (textoMaterial == ""){
        M.toast({html:"El campo Material no puede ir vacio.",classes: "rounded"}); 
      }else{
        $.post("../php/update_orden.php", {
          valorIdOrden: textoIdOrden,
          valorTrabajo: textoTrabajo,
          valorMaterial:textoMaterial,
          valorTecnicos:textoTecnicos,
          valorEstatus: textoEstatus,
          valorEstatusI: textoEstatusI
        }, function(mensaje) {
            $("#resultado_update_orden").html(mensaje);
        });
      }
    }else if (textoEstatusI == 'Cotizar') {
      var textoPrecio = $("input#precio").val();
      var textoSolucion = $("input#solucion").val();

      if (textoPrecio == "") {
        M.toast({html:"El campo Precio no puede ir vacio o en 0.",classes: "rounded"}); 
      }else{
        $.post("../php/update_orden.php", {
          valorIdOrden: textoIdOrden,
          valorPrecio: textoPrecio,
          valorSolucion: textoSolucion,
          valorEstatus: textoEstatus,
          valorEstatusI: textoEstatusI
        }, function(mensaje) {
            $("#resultado_update_orden").html(mensaje);
        });
      }
    }else if (textoEstatusI == 'Cotizado' || textoEstatusI == 'Pedir') {
      var textoSolucion = $("input#solucion").val();

      if (textoSolucion == "") {
        M.toast({html:"El campo Solucion no puede ir vacio.",classes: "rounded"}); 
      }else{
        $.post("../php/update_orden.php", {
          valorIdOrden: textoIdOrden,
          valorSolucion: textoSolucion,
          valorEstatus: textoEstatus,
          valorEstatusI: textoEstatusI
        }, function(mensaje) {
            $("#resultado_update_orden").html(mensaje);
        });
      }
    }else if (textoEstatusI == 'Realizar') {
      var textoSolucion = $("input#solucion").val();

      textoTecnico = '<?php echo $tecnico;?>';
      textoApoyo = 0;
      for(var i=1;i<=8;i++){
        if(document.getElementById('tecnico'+i).checked==true){
          var textoApoyo = $("input#tecnico"+i).val();
        }
      }      
      textoTecnicos = textoTecnico+', '+textoApoyo;
      if (textoApoyo == 0) {
        textoTecnicos = textoTecnico
      }

      if (textoSolucion == "") {
        M.toast({html:"El campo Solucion no puede ir vacio.",classes: "rounded"}); 
      }else{
        $.post("../php/update_orden.php", {
          valorIdOrden: textoIdOrden,
          valorTecnicos:textoTecnicos,          
          valorSolucion: textoSolucion,
          valorEstatus: textoEstatus,
          valorEstatusI: textoEstatusI
        }, function(mensaje) {
            $("#resultado_update_orden").html(mensaje);
        });
      }
    }
};
</script>
<body>
	<div class="container">
		<div class="row">
	      <h3 class="hide-on-med-and-down">Atender Orden:</h3>
	      <h5 class="hide-on-large-only">Atender Orden:</h5>
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
                <br><hr>               
                <b>Solicitud: </b><?php echo $orden['solicitud'];?><br>  
                <?php if ($orden['estatus'] != 'Revisar') { ?>             
                <b>Trabajo: </b><?php echo $orden['trabajo'];?><br>                
                <b>Material: </b><?php echo $orden['material'];?><br> 
                <?php } ?>               
              </p>
              <br>
              <a href="#!" class="secondary-content"><span class="new badge green" data-badge-caption="<?php echo 'FECHA DE REGISTRO: '.$orden['fecha'];?>"></span></a>
            </li>
      </ul>
      </div>
    	<form class="col s12">
        <div class="row">
          <?php if ($orden['estatus'] == 'Revisar') { ?>
           <div class="col s12 l9 m9">
           <div class="input-field col s12 l6 m6">
             <i class="material-icons prefix">touch_app</i>
             <textarea id="trabajo" class="materialize-textarea validate" data-length="150" required><?php echo $orden['trabajo'];?></textarea> 
             <label for="trabajo">Trabajo (ej: Se realizara...):</label>
           </div>
           <div class="input-field col s12 l6 m6">
             <i class="material-icons prefix">assignment</i>
             <textarea id="mat" class="materialize-textarea validate" data-length="150" required><?php echo $orden['material'];?></textarea> 
             <label for="mat">Material (ej: 4 Camaras, Cable de Red):</label>
           </div>
           <label>APOYO (solo toma uno):</label>
            <p>
            <?php
            $bandera = 1; 
            $sql_tecnico = mysqli_query($conn,"SELECT * FROM users WHERE area='Taller' OR area='Redes'  OR user_id = 49 OR user_id = 28 OR user_id = 25");
            while($tecnico = mysqli_fetch_array($sql_tecnico)){
            ?>
              <div class="col s12 m4 l3">
                <input type="checkbox" value="<?php echo $tecnico['user_name'];?>" id="tecnico<?php echo $bandera;?>"/>
                <label for="tecnico<?php echo $bandera;?>"><?php echo $bandera;?>.-<?php echo $tecnico['user_name'];?></label>
              </div>
            <?php
              $bandera++;
            }$bandera--;
            ?>
            </p><br><br><br><br>
           </div>
          <?php }elseif ($orden['estatus'] == 'Cotizar') { ?>
          <div class="col s12 l9 m9"><br>
           <div class="input-field col s12 l4 m4">
              <i class="material-icons prefix">monetization_on</i>
              <input id="precio" type="number" class="validate" data-length="30" value="<?php echo $orden['precio'];?>" required>
              <label for="precio">Precio:</label>
           </div>
           <div class="input-field col s12 l8 m8">
              <i class="material-icons prefix">check</i>
              <input id="solucion" type="text" class="validate" data-length="50" value="<?php echo $orden['solucion'];?>" required>
              <label for="solucion">Solucion (Descripcion de que se hizo):</label>
           </div>
          </div>
          <?php }elseif ($orden['estatus'] == 'Cotizado' OR $orden['estatus'] == 'Pedir' OR $orden['estatus'] == 'Realizar') { ?>
          <div class="col s12 l9 m9"><br>
           <div class="input-field col s12 l4 m4">
              <h5>Precio: $<?php echo $orden['precio'];?></h5>
           </div>
           <div class="input-field col s12 l8 m8">
              <i class="material-icons prefix">check</i>
              <input id="solucion" type="text" class="validate" data-length="50" value="<?php echo $orden['solucion'];?>" required>
              <label for="solucion">Solucion (Descripcion de que se hizo):</label>
           </div>

          <?php if ($orden['estatus'] == 'Realizar') { ?>
            <label>APOYO (solo toma uno):</label>
            <p>
            <?php
            $bandera = 1; 
            $sql_tecnico = mysqli_query($conn,"SELECT * FROM users WHERE area='Taller' OR area='Redes'  OR user_id = 49 OR user_id = 28 OR user_id = 25");
            while($tecnico = mysqli_fetch_array($sql_tecnico)){
            ?>
              <div class="col s12 m4 l3">
                <input type="checkbox" value="<?php echo $tecnico['user_name'];?>" id="tecnico<?php echo $bandera;?>"/>
                <label for="tecnico<?php echo $bandera;?>"><?php echo $bandera;?>.-<?php echo $tecnico['user_name'];?></label>
              </div>
            <?php
              $bandera++;
            }$bandera--;
            ?>
            </p>
          <?php } ?>           
          </div>
          <?php } ?>
           <div class="col s12 l3 m3">
           <div class="input-field">
           <label>Estatus:</label><br><br>
              <select id="estatus" class="browser-default" required>
                <option selected value="<?php echo $orden['estatus'];?>"><?php echo $orden['estatus'];?></option>
                <option value="Revisar">Revisar</option> 
                <option value="Cotizar">Cotizar</option> 
                <option value="Cotizado">Cotizado</option> 
                <option value="Pedir">Pedir</option> 
                <option value="Realizar">Realizar</option> 
                <option value="Facturar">Facturar</option> 
              </select>
           </div>
            <input id="id_orden" value="<?php echo htmlentities($id_orden);?>" type="hidden"><br><br>
            <input id="estatusI" value="<?php echo htmlentities($orden{'estatus'});?>" type="hidden">
            <a onclick="update_orden();" class="waves-effect waves-light btn pink right"><i class="material-icons right">send</i>ACTUALIZAR</a> 
          </div>  
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