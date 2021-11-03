<html>
<head>
  <title>SIC | Formulario Vehiculo</title>
<?php 
include('fredyNav.php');
include('../php/cobrador.php');
?>
<script>
function insert_vehiculo() {
    var textoNombre = $("input#nombre").val();
    var textoResponsable = $("input#responsablev").val();
    var textoDescripcion = $("textarea#descripcion").val();

    if (textoNombre == "") {
      M.toast({html: 'El campo Nombre(s) se encuentra vacío.', classes: 'rounded'});
    }else if(textoResponsable == ''){
      M.toast({html: 'Incluya un responsable de vehiculo.', classes: 'rounded'});
    }else if(textoDescripcion == ""){
      M.toast({html: 'El campo Dirección se encuentra vacío.', classes: 'rounded'});
    }else{
      $.post("../php/insert_vehiculo.php", {
          valorNombre: textoNombre,
          valorResponsable: textoResponsable,
          valorDescripcion: textoDescripcion,
        }, function(mensaje) {
            $("#resultado_v").html(mensaje);
        }); 
    }
};
</script>
</head>
<main>
<body>
<div class="container">
  <div class="row" >
      <h3 class="hide-on-med-and-down">Registrar Vehiculo</h3>
      <h5 class="hide-on-large-only">Registrar Vehiculo</h5>
  </div>
  <div id="resultado_v">
  </div>
   <div class="row">
    <form class="col s12">
      <div class="col s12 m6 l6">
        <br>
        <div class="input-field">
          <i class="material-icons prefix">directions_car</i>
          <input id="nombre" type="text" class="validate" data-length="30" required>
          <label for="nombre">Nombre:</label>
        </div>        
        <div class="input-field">
          <i class="material-icons prefix">account_circle</i>
          <input id="responsablev" type="text" class="validate" data-length="30" required>
          <label for="responsablev">Responsable:</label>
        </div>    
      </div>
         <!-- AQUI SE ENCUENTRA LA DOBLE COLUMNA EN ESCRITORIO.-->
      <div class="col s12 m6 l6">
        <br>
        <div class="input-field">
          <i class="material-icons prefix">edit</i>
          <textarea id="descripcion" class="materialize-textarea validate" data-length="100" required></textarea>
          <label for="descripcion">Descripcion General :</label>
        </div>
      </div>
    </form>
    <a onclick="insert_vehiculo();" class="waves-effect waves-light btn pink right"><i class="material-icons right">send</i>GUARDAR</a>
  </div> 
</div><br>
</body>
</main>
</html>
