<!DOCTYPE html>
<html>
<head>
	<title>SIC | Atender Orden</title>
</head>
<?php
include ('fredyNav.php');
include('../php/conexion.php');
include('../php/cobrador.php');
$id_user = $_SESSION['user_id'];
if (isset($_GET['id_orden']) == false) {
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
  $id_orden = $_GET['id_orden'];
  $tecnico = $_SESSION['user_name'];
?>
<script>
function insert_extra(orden){
  var textoCantidad = $("input#cantidad").val();
  var textoDescripcion = $("input#descripcion").val();

  if (textoCantidad == "" || textoCantidad == 0) {
    M.toast({html: "El campo Cantidad se encuentra vacío o en 0.", classes: "rounded"});
  }else if (textoDescripcion == "") {
    M.toast({html: "El campo Descripcion se encuentra vacío.", classes: "rounded"});
  }else{
    $.post("../php/insert_extra.php", {
      valorCantidad : textoCantidad,
      valorIdOrden: orden,
      valorDescripcion:textoDescripcion,
    }, function(mensaje) {
      $("#extra").html(mensaje);
    });
  }
};
function update_orden() {
    var textoEstatus = $("select#estatus").val();
    var textoEstatusI = $("input#estatusI").val();
    var textoIdOrden = $("input#id_orden").val();

    if (textoEstatusI == 'PorConfirmar') {
     
        $.post("../php/update_orden.php", {
          valorIdOrden: textoIdOrden,
          valorEstatus: textoEstatus,
          valorEstatusI: textoEstatusI
        }, function(mensaje) {
            $("#resultado_update_orden").html(mensaje);
        });

    }else if (textoEstatusI == 'Revisar') {
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
    }else if (textoEstatusI == 'Cotizado' || textoEstatusI == 'Autorizado(Pedir)') {
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
    }else if (textoEstatusI == 'Ejecutar') {
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
 function subir(id){
    var textoNombre = $("input#nombre").val();
    $.post("../views/modal_doc.php", { 
            valorId: id
    }, function(mensaje) {
    $("#documento").html(mensaje);
    });
  };
  function editar(id){
    $.post("../views/editar_doc.php", { 
            valorId: id
    }, function(mensaje) {
    $("#documento").html(mensaje);
    });
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
                <?php } //FIN IF  ?> 
                <div id="documento"></div>  
                <div class="row col s10"><br>
                  <b>Cotizacion: </b> $<?php echo $orden['precio'];?>  -  <b>Documento: </b><a href = "../files/cotizaciones/<?php echo $orden['cotizacion_n'];?>" target = "blank"><?php echo $orden['cotizacion_n'];?></a> 
                  <div class="right">
                    <a onclick="editar(<?php echo $id_orden; ?>);" class="btn-small pink waves-effect waves-light <?php echo ($orden['cotizacion_n'] == '')? 'disabled': ''; ?> rigth"><i class="material-icons">edit</i></a>
                    <a onclick="subir(<?php echo $id_orden; ?>);" class="btn-small green waves-effect waves-light <?php echo ($orden['cotizacion_n'] != '')? 'disabled': ''; ?> rigth"><i class="material-icons">file_upload</i></a>
                  </div>
                </div><br><br><br><br>
                <div id="extra">
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
                        </table>'; 
                  } ?>
                </div>
              </p>
              <br>
              <a href="#!" class="secondary-content"><span class="new badge green" data-badge-caption="<?php echo 'FECHA DE REGISTRO: '.$orden['fecha'];?>"></span></a>
            </li>
      </ul>
      </div>
    	<form class="col s12">
        <div class="row">
          <?php if ($orden['estatus'] == 'Ejecutar') { ?>
          <div class="row col s12">
            <div class="col s12 m2 l2">
              <h4 class="hide-on-med-and-down">Extras:</h4>
              <h6 class="hide-on-large-only">Extras:</h6>
            </div>      
            <div class="row col s9 m3 l3">
              <div class="input-field">
                <i class="material-icons prefix">payment</i>
                <input id="cantidad" type="number" class="validate" data-length="6" value="0" required>
                <label for="cantidad">Cantidad:</label>
              </div>
            </div>
            <div class="row col s9 m4 l4">
              <div class="input-field">
                <i class="material-icons prefix">edit</i>
                <input id="descripcion" type="text" class="validate" data-length="6" required>
                <label for="descripcion">Descripcion:</label>
              </div>
            </div><br>            
              <div class="col l1"><br></div>
              <a onclick="insert_extra(<?php echo $id_orden;?>);" class="waves-effect waves-light btn pink  col s8 m2 l2"><i class="material-icons right">send</i>Registrar Extra</a>
            </form>         
          </div> 
          <?php } ?>
          <?php if ($orden['estatus'] == 'Revisar') { ?>
           <div class="col s12 l9 m9">
           <div class="input-field col s12 l6 m6">
             <i class="material-icons prefix">touch_app</i>
             <textarea id="trabajo" class="materialize-textarea validate" data-length="150" required><?php echo $orden['trabajo'];?></textarea> 
             <label for="trabajo">Trabajo (ej: Se Ejecutara...):</label>
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
          <?php }elseif ($orden['estatus'] == 'Cotizado' OR $orden['estatus'] == 'Autorizado(Pedir)' OR $orden['estatus'] == 'Ejecutar') { ?>
          <div class="col s12 l9 m9"><br>
           <div class="input-field col s12 l4 m4">
              <h5><b>Total = $<?php echo $orden['precio']+$totalE;?></b></h5>
           </div>
           <div class="input-field col s12 l8 m8">
              <i class="material-icons prefix">check</i>
              <input id="solucion" type="text" class="validate" data-length="50" value="<?php echo $orden['solucion'];?>" required>
              <label for="solucion">Solucion (Descripcion de que se hizo):</label>
           </div>

          <?php if ($orden['estatus'] == 'Ejecutar') { ?>
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
                <?php if ($id_user == 56 OR $id_user == 10 OR $id_user == 49) { ?>
                <option value="Autorizado(Pedir)">Autorizado(Pedir)</option> 
                <?php 
                } //FIN IF PARA AUTORIZADO
                if ($orden['estatus'] == 'Autorizado(Pedir)' OR $orden['estatus'] == 'Ejecutar') {
                ?>
                <option value="Ejecutar">Ejecutar</option> 
                <option value="Facturar">Facturar</option> 
                <?php }//FIN DEL IF ?>
                <option value="Pendiente">Pendiente</option> 
                <option value="Cancelada">Cancelada</option> 
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