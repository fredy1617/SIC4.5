<html>
<head>
  <title>SIC | Realizar Pago</title>
</head>
<?php 
include('fredyNav.php');
include('../php/conexion.php');
date_default_timezone_set('America/Mexico_City');
$Fecha_Hoy = date('Y-m-d');
if (isset($_POST['id']) == false) {
  ?>
  <script>    
    function atras() {
      M.toast({html: "Regresando a centrales.", classes: "rounded"})
      setTimeout("location.href='centrales.php'", 800);
    };
    atras();
  </script>
  <?php
}else{
$id_equipo = $_POST['id'];
?>
<script>   
function update_equipo(id_equipo) {  
    var textoNombre = $("input#nombre").val();
    var textoMarca = $("input#marca").val();
    var textoModelo = $("input#modelo").val();
    var textoIP = $("input#ip").val();
    var textoDescripcion = $("textarea#descripcion").val();
    var textoEstatus = $("select#status").val();
    var textoModificacion = $("input#modificacion").val();
    var textoRazon = $("input#razon").val();
    var textoIdCentral = $("input#id_central").val();
    Entra = "Si";
    if (textoEstatus != "Activo") {
      if (textoRazon == "") { Entra = "No"; }
    }

    if (textoNombre == "") {
      M.toast({html: 'Ingrese un nombre para el equipo.', classes: 'rounded'});
    }else if (textoMarca == "") {
        M.toast({html: 'Ingrese una marca para el equipo.', classes: 'rounded'});
    }else if (textoModelo == "") {
        M.toast({html: 'Ingrese un modelo para el equipo.', classes: 'rounded'});
    }else if (textoDescripcion == "") {
        M.toast({html: 'Ingrese una descripcion para el equipo.', classes: 'rounded'});
    }else if (Entra == "No") {
        M.toast({html: 'Ingrese una Razon de ¿Porque '+textoEstatus+' ?', classes: 'rounded'});
    }else{
        $.post("../php/update_equipo.php" , { 
            valorNombre: textoNombre,
            valorMarca: textoMarca,
            valorModelo: textoModelo,
            valorIP: textoIP,
            valorDescripcion: textoDescripcion,
            valorEstatus: textoEstatus,
            valorRazon: textoRazon,
            valorModificacion: textoModificacion,
            valorIdEquipo: id_equipo,
            valorIdCentral: textoIdCentral
          }, function(mensaje) {
              $("#mostrar_equipos").html(mensaje);
          });  
    }    
};
</script>
<body>
<?php
$sql = "SELECT * FROM equipos WHERE id=$id_equipo";
$datos = mysqli_fetch_array(mysqli_query($conn, $sql));
?>
<div class="container">
  
<div class="row">
  <div class="col s12">
    <div class="row">
     <div id="mostrar_equipos"></div>   
<!-- ----------------------------  EDIT   ---------------------------------------->
      <div class="col s12 m12 l12">
        <h4 class="pink-text "><< Editar equipo: >></h4>
        <form class="row" name="formMensualidad"><br>
          <div class="input-field col s12 m6 l6">
            <i class="material-icons prefix">phonelink</i>
            <input id="nombre" type="text" class="validate" data-length="30" required value="<?php echo $datos['nombre']; ?>">
            <label for="nombre">Nombre :</label>
          </div>
          <div class="input-field col s12 m6 l6">
            <i class="material-icons prefix">local_offer</i>
            <input id="marca" type="text" class="validate" data-length="20" required value="<?php echo $datos['marca']; ?>">
            <label for="marca">Marca :</label>
          </div>
          <div class="input-field col s12 m6 l6">
            <i class="material-icons prefix">confirmation_number</i>
            <input id="modelo" type="text" class="validate" data-length="30" required value="<?php echo $datos['modelo']; ?>">
            <label for="modelo">Modelo :</label>
          </div>
          <div class="input-field col s12 m6 l6">
            <i class="material-icons prefix">settings_ethernet</i>
            <input id="ip" type="text" class="validate" data-length="14" required value="<?php echo $datos['ip']; ?>">
            <label for="ip">IP :</label>
          </div>
          <div class="input-field col s12 m6 l6">
            <i class="material-icons prefix">comment</i>
            <textarea id="descripcion" class="
           materialize-textarea validate" data-length="250" required ><?php echo $datos['descripcion']; ?></textarea>
            <label for="descripcion">Descripción: (ej: Puerto 1 conectada a la antena 3, La torre tiene paneles solares de 25v con regulador, etc.)</label>
          </div>
            <div class="col s6 l3 m3">
            <label> Estatus:</label>
            <select id="status" class="browser-default" required>
              <option value="<?php echo $datos['status']; ?>" selected><?php echo $datos['status']; ?></option>
              <option value="Dañado">Dañado</option>
              <option value="Almacen">Almacen</option>
              <option value="Activo">Activo</option>
            </select>
          </div>
            <div class="col s6 l3 m3">
                <label for="modificacion">Ultima Modificación:</label>
                <input id="modificacion" type="date" value="<?php echo $datos['modificacion']; ?>">    
          </div>
          <div class="input-field col s12 m6 l6">
            <i class="material-icons prefix">help</i>
            <input id="razon" type="text" class="validate" data-length="100" required value="<?php echo $datos['razon']; ?>">
            <label for="razon">Razon (¿Porque en Almacen?):</label>
          </div>
          <input id="id_central" value="<?php echo htmlentities($datos['id_central']);?>" type="hidden">
        </form>
        <a onclick="update_equipo(<?php echo $id_equipo; ?>);" class="waves-effect waves-light btn pink right "><i class="material-icons right">send</i>Guardar equipo</a><br>
      </div>
    </div>
  </div>
</div>

</div><!----------------CONTAINER----------------->
</body>
<?php } ?>
</html>