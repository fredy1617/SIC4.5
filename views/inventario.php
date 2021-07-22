<!DOCTYPE html>
<html lang="en">
  <head>
    <?php
      include('fredyNav.php');
      include ('../php/cobrador.php');
    ?>
    <title>SIC | Inventario</title>
    <script>
      function borrar(Codigo){
        $.post("../php/borrar_inventario.php", { 
          valorCodigo: Codigo
        }, function(mensaje) {
        $("#resultado_codigo").html(mensaje);
        }); 
      };
      function busquedaCodigo() {
        var codigo = $("input#codigo").val();
        $.post("../php/busquedaCodigo.php", {
            codigo: codigo,
          }, function(mensaje){
              $("#resultado_codigo").html(mensaje);
        });
      }
      function buscar_inventario(){
        var texto = $("input#busqueda").val();
        $.post("../php/buscar_inventario.php", {
            texto: texto,
          }, function(mensaje){
              $("#datos").html(mensaje);
        });
      };
      function insert_inventario() {
        var textoCodigo = $("input#codigo").val();
        var textoNombre = $("input#nombre").val();
        var textoCantidad = $("input#cantidad").val();
        var textoUnidad = $("select#unidad").val();
        var textoMarca = $("input#marca").val();
        var textoEstatus = $("input#estatus").val();
        var textoResponsable = $("select#responsable1").val();
        
        if (textoCodigo == "") {
          M.toast({html :"Por favor ingrese un codigo.", classes: "rounded"});
        }else if(textoNombre == ""){
          M.toast({html :"El Nombre no puede ir vacio.", classes: "rounded"});
        }else if(textoCantidad == 0){
          M.toast({html :"La cantidad debe ser mayor a 0.", classes: "rounded"});
        }else if(textoUnidad == 0){
          M.toast({html :"Por favor seleccione un valor de unidad.", classes: "rounded"});
        }else if(textoEstatus == ''){
          M.toast({html :"Por favor ingrese un estatus.", classes: "rounded"});
        }else if(textoResponsable == 0){
          M.toast({html :"Por favor seleccione un dpto responsable.", classes: "rounded"});
        }else{
          var existe = $("input#existe").val();
          if (existe == 'SI') {
            var respuesta = confirm("Se incrementara la cantidad de "+textoNombre+" en: "+textoCantidad+" "+textoUnidad);
          }else{
            var respuesta = confirm("Se creara un nuevo producto ("+textoNombre+", "+textoMarca+", "+textoCantidad+" "+textoUnidad+")");
          }
          if (respuesta) {
            $.post("../php/insert_inventario.php", {
              valorCodigo: textoCodigo,
              valorNombre: textoNombre,
              valorCantidad: textoCantidad,
              valorUnidad: textoUnidad,
              valorMarca: textoMarca,
              valorEstatus: textoEstatus,
              valorResponsable: textoResponsable
            }, function(mensaje) {
                $("#resultado_inventario").html(mensaje);
            }); 
          }
        }
      };
    </script>
  </head>
<main>
  <body onload="buscar_inventario();">
    <div class="container">
      <div id="resultado_inventario"></div>
      <div class="row" >
        <h3 class="hide-on-med-and-down col s6 m7 l7">Registrar Material/Herramienta</h3>
        <h5 class="hide-on-large-only col s6 m7 l7">Registrar Material/Herramienta</h5><br>
        <div class="input-field col s6 m2 l2">
          <i class="material-icons prefix">featured_play_list</i>
          <input type="number" id="codigo" onkeyup="setTimeout(busquedaCodigo, 2000);">
          <label for="codigo">Codigo:</label>
        </div>
      </div>
      <div class="row col s12" id="resultado_codigo"> </div>      
      <div>
        <div class="row"><br><br>
          <h3 class="hide-on-med-and-down col s12 m6 l6">Inventario</h3>
          <h5 class="hide-on-large-only col s12 m6 l6">Inventario</h5>

          <form class="col s12 m6 l6">
            <div class="row">
              <div class="input-field col s12">
                <i class="material-icons prefix">search</i>
                <input id="busqueda" name="busqueda" type="text" class="validate" onkeyup="buscar_inventario();">
                <label for="busqueda">Buscar(#Codigo, Nombre)</label>
              </div>
            </div>
          </form>
        </div>
        <div>
          <table class="bordered highlight responsive-table">
            <thead>
              <tr>
                <th>Codigo</th>
                <th>Nombre</th>
                <th>Marca</th>
                <th>Cantidad</th>
                <th>Unidad</th>
                <th>Estatus</th>
                <th>Responsable</th>
                <th>Borrar</th>
              </tr>
            </thead>
            <tbody id="datos">
            </tbody>
          </table><br><br>
        </div>
      </div>
    </div>
  </body>
</main>
</html>