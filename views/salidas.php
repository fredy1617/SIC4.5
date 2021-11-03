<!DOCTYPE html>
<html lang="en">
<head>
<?php
  include('fredyNav.php');
  include('../php/conexion.php');
  include('../php/cobrador.php');
?>
<title>SIC | Salidas</title>
<script>
function borrar_refa(IdRefaccion){
    $.post("../php/borrar_refa.php", {           
            valorIdRefaccion: IdRefaccion,
    }, function(mensaje) {
    $("#refa_borrar").html(mensaje);
    }); 
};
function sacar(){
    var textoIdDispositivo = $("input#id_dispositivo").val();
    var textoRef = $("input#ref").val();

    if (document.getElementById('banco').checked == true) {
      textoTipo_Cambio = "Banco";
    }else if (document.getElementById('san').checked==true) {
      textoTipo_Campio = "SAN";
    }else{
      textoTipo_Cambio = "Efectivo";
    }
    if ((document.getElementById('banco').checked==true || document.getElementById('san').checked==true) && textoRef == "") {
        M.toast({html: 'Los pagos en banco deben de llevar una referencia.', classes: 'rounded'});
    }else if (document.getElementById('banco').checked==false && document.getElementById('san').checked==false && textoRef != "") {
          M.toast({html: 'Pusiste referencia y no elegiste Banco o SAN.', classes: 'rounded'});
    }else{
      $.post("../php/dar_salida.php", {           
              valorIdDispositivo: textoIdDispositivo,
              valorRef: textoRef,
              valorTipo_Cambio: textoTipo_Cambio,
      }, function(mensaje) {
      $("#sacar").html(mensaje);
      }); 
    }
};
function salida() {
    var textoLink = $("textarea#link").val();
    var textoObservaciones = $("textarea#observaciones").val();
    var textoManoObra = $("input#mano").val();
    var textoIdDispositivo = $("input#id_dispositivo").val();
    var textoEstatus = $("select#estatus").val();
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
           entra = "No"      
        }else{
          entra = "Si";
          textoRefacciones += textoDesc+" - "+textoPrecioR+", ";
        }
        }
        n++;
    }
    if (entra == "No") {
      //M.toast({html:"Dice que no.", classes: "rounded"});
    }else if(textoManoObra == ""){
      M.toast({html:"El campo Mano de Obra se encuentra vacío.", classes: "rounded"});
    }else if(textoEstatus == '0'){
      M.toast({html:"Seleccione un Estatus por favor.", classes: "rounded"});
    }else {

      $.post("../php/update_salida.php", {
          valorLink: textoLink,
          valorObservaciones: textoObservaciones,
          valorManoObra: textoManoObra,
          valorIdDispositivo: textoIdDispositivo,
          valorEstatus: textoEstatus,
          valorRefacciones: textoRefacciones
        }, function(mensaje) {
            $("#refrescar").html(mensaje);
        }); 
    }
};
</script>
</head>
<main>
<?php
if (isset($_POST['id_dispositivo']) == false) {
  ?>
  <script>    
    function atras() {
      M.toast({html: "Regresando a listos.", classes: "rounded"})
      setTimeout("location.href='listos.php'", 1000);
    }
    atras();
  </script>
  <?php
}else{
  $id_dispositivo = $_POST['id_dispositivo'];
?>
<body>
	<div class="container" id="refrescar">
    <div class="row">
      <h2 class="hide-on-med-and-down">Salida</h2>
      <h4 class="hide-on-large-only">Salida</h4>
    </div>
    <div id="sacar"></div>
    	  <?php        
        $datos = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM dispositivos WHERE id_dispositivo = $id_dispositivo"));
        ?>
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
                  ?>
                  <b>Extras: </b>Color <?php echo $datos['color'];?>, con <?php echo $datos['cables'];?><br>
                 <?php 
                 }else{
                  ?>
                  <b>Extras: </b><?php echo $datos['extras'];?><br>
                  <?php
                 }
                 ?>                 
                 <b>Contraseña: </b><?php echo $datos['contra'];?><br>
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
                 <b>Resta: </b><?php echo "$".$resto;?>
                 <a href="#!" class="primary-content"><span class="new badge red" data-badge-caption="<?php echo 'FECHA DE SALIDA: '.$datos['fecha_salida'];?>"></span></a>
              <br><br>
              <a href="#!" class="secondary-content"><span class="new badge green" data-badge-caption="<?php echo 'FECHA DE ENTRADA: '.$datos['fecha'];?>"></span></a>
                 <hr>
                 <b>Falla: </b><?php echo $datos['falla'];?>
              </p>
              <br>              
            </li>
        </ul>
        <div class="row">
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
              <div class="col s12 m3 l3">
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
              <div class="input-field col s12 m2 l2"><br>
                  <i class="material-icons prefix">monetization_on</i>
                  <input id="mano" type="number" class="validate" data-length="6" value="<?php  if($datos['precio'] > 0){ echo $datos['precio']; }else{ echo $datos['mano_obra'];}?>" required>
                  <label for="mano">Mano de Obra:</label>
                </div>
                <div class="input-field col s12 m3 l3"><br>
                  <h5><b>TOTAL: $<?php if($datos['precio'] > 0){ echo $datos['precio']; }else{ echo $datos['mano_obra']+$datos['t_refacciones'];}?></b></h5>
                </div>
                <?php if (in_array($_SESSION['user_id'], array(10, 70, 49, 88, 38, 84, 90))) { 
                  $Ser = '';
                }else{ $Ser = 'disabled="disabled"';}?>
                <div class="col s6 m1 l1">
                  <p>
                    <br>
                    <input type="checkbox" id="banco" <?php echo $Ser;?>/>
                    <label for="banco">Banco</label>
                  </p>
                </div>
                <div class="col s6 m1 l1">
                  <p>
                    <br>
                    <input type="checkbox" id="san" <?php echo $Ser;?>/>
                    <label for="san">SAN</label>
                  </p>
                </div>
                <br>
                <div>
                 <input name="id_dispositivo" id="id_dispositivo" type="hidden" class="validate center" data-length="200" value="<?php echo $datos['id_dispositivo'];?>"><br>
                <a onclick="salida();" class="btn btn-floating pink  tooltipped" data-position="bottom" data-tooltip="GUARDAR"><i class="material-icons">save</i></a>
                <button onclick="salida();" type="submit"  class="btn btn-floating pink waves-effect waves-light tooltipped" data-position="bottom" data-tooltip="GUARDAR e IMPRIMIR"><i class="material-icons">print</i></button>
                <a onclick="sacar();" class="btn btn-floating pink waves-effect waves-light tooltipped" data-position="bottom" data-tooltip="SALIDA"><i class="material-icons">exit_to_app</i></a>
                </div>
                <div class="col s6 m2 l2">
                  <div class="input-field">
                    <input id="ref" type="text" class="validate" data-length="15" required value="">
                    <label for="ref">Referencia:</label>
                  </div>
                </div>
             </div>             
          </form>
        </div>
        <?php
        mysqli_close($conn);
        ?>
    <br><br>
  </div>
</body>
<?php
}
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
</main>
</html>