<!DOCTYPE html>
<html>
  <head>
  	<title>SIC | Caja Chica</title>
    <?php
    include('fredyNav.php');
    include('../php/conexion.php');
    include('../php/superAdmin.php');
    ?>
    <script>
      function showContent() {
        element2 = document.getElementById("content");
        var textoOp = $("select#opcion").val();

        if (textoOp == 'Egreso') {
          element2.style.display='block';
        }
        else {
          element2.style.display='none';
        }
      };
      function imprimir(id){
        var a = document.createElement("a");
            a.target = "_blank";
            a.href = "../php/ticket_caja_ch.php?Id="+id;
            a.click();
      };
      function insert_caja(){ 
        var textoCantidad = $("input#cantidad").val();  
        var textoOp = $("select#opcion").val();
        if (textoOp == 'Egreso') {
          var textoDescripcion = $("input#ingreso_desc").val();
        }
        else {
          textoDescripcion = 'Ingreso';
        }

        if (textoOp == "" || textoOp ==0) {
          M.toast({html:"Seleccione una opcion.", classes: "rounded"});
        }else if (textoCantidad == "" || textoCantidad ==0) {
          M.toast({html:"El campo Cantidad se encuentra vacío o en 0.", classes: "rounded"});
        }else {
          $.post("../php/insert_caja.php", { 
              valorCantidad: textoCantidad,
              valorDescripcion: textoDescripcion,
              valorTipo: textoOp,
            }, function(mensaje) {
                $("#mostrar_resultados").html(mensaje);
                  
            });
        }
      };
      function borrar_caja(Id){
        $.post("../php/borrar_caja.php", { 
                valorId: Id
        }, function(mensaje) {
        $("#mostrar_resultados").html(mensaje);
        }); 
      };
      function buscar() {
        var textoDe = $("input#fecha_de").val();
        var textoA = $("input#fecha_a").val();
        $.post("../php/buscar_caja_ch.php", {
              valorDe: textoDe,
              valorA: textoA,
        }, function(mensaje) {
                $("#datos").html(mensaje);
        }); 
      };
    </script>
  </head>
  <body onload="buscar();">
  	<div class="container" id="mostrar_resultados">
      <?php       
      // SACAMOS LA SUMA DE TODOS LOS EGRESOSO E INGRESOSO DE LA CAJA CHICA
      $Suma_Ingresos = mysqli_fetch_array(mysqli_query($conn, "SELECT SUM(cantidad) AS suma FROM historila_caja_ch WHERE tipo = 'Ingreso'"));
      $Suma_Egresoso = mysqli_fetch_array(mysqli_query($conn, "SELECT SUM(cantidad) AS suma FROM historila_caja_ch WHERE tipo = 'Egreso'"));
      //SE HACE EL CALCULO DEL TOTAL DE LA CAJA CHICA
      $Total = $Suma_Ingresos['suma']-$Suma_Egresoso['suma'];
      ?>
      <br><hr><hr><br>
  		<div class="row">
   			<h3 class="col s12 m7 l7">>> Caja Chica:</h3>
        <h4 class="col s11 m4 l4">TOTAL = <span class="new badge green" data-badge-caption="">$<?php echo $Total; ?></h4>
      </div>
      <hr><hr><br>
      <div class="row">
        <h3>Accion:</h3>
      </div>
      <div class="row">
        <div class="input-field row">
          <i class="col s1"> <br></i>
          <select id="opcion" class="browser-default col s12 m3 l3" required onchange="javascript:showContent()">
            <option value="0" selected >Opciones:</option>
            <option value="Ingreso" >Ingreso</option>
            <option value="Egreso" >Egreso</option>
          </select>
          <div class="row col s12 m3 l3">
            <div class="input-field">
              <i class="material-icons prefix">payment</i>
              <input id="cantidad" type="number" class="validate" data-length="6" required>
              <label for="cantidad">Cantidad: </label>
            </div>
          </div>
          <div class="input-field col s12 m4 l4" id="content" style="display: none;">
            <i class="material-icons prefix">edit</i>
            <input id="ingreso_desc" type="text" class="validate" data-length="100" required>
            <label for="ingreso_desc">Descripcion:</label>
          </div>
        <a onclick="insert_caja();" class="waves-effect waves-light btn pink right"><i class="material-icons right">send</i>Registrar</a>
        <br>
      </div>
      <br><hr><hr><br>
      <div class="row">
        <div class="col s12 l4 m4">
          <label for="fecha_de">De:</label>
          <input id="fecha_de" type="date" >    
        </div>
        <div class="col s12 l4 m4">
          <label for="fecha_a">A:</label>
          <input id="fecha_a" type="date" >
        </div><br>
        <div>
          <button class="btn waves-light waves-effect right pink" onclick="buscar();"><i class="material-icons prefix right">send</i> FILTRAR</button>
        </div>
      </div>
      <div id="datos"></div>
  	</div>
  </body>
</html>