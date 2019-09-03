<!--Inicia Script de dispositivos-->
<script>
   function crear_ruta(){
    var textoTecnicos = $("input#tecnicos").val();
    if (textoTecnicos == "") {
      M.toast({html:"El campo Tecnico(s) no puede ir vacío.", classes: "rounded"});
    }else{
    $.post("../php/crear_ruta.php", {
          valorTecnicos: textoTecnicos,
        }, function(mensaje) {
            $("#resultado_ruta").html(mensaje);
    });}
  };
  function PulsarTeclaFolio(){
    tecla = event.keyCode;
 
    if(tecla==32){
      buscar_folio()
    }else if(tecla==65){
      buscar_folio()
    }else if(tecla==69){
      buscar_folio()
    }else if(tecla==73){
      buscar_folio()
    }else if(tecla==79){
      buscar_folio()
    }else if(tecla==85){
      buscar_folio()
    }else if(tecla>=48 && tecla<=57){
      buscar_folio()
    }else if(tecla>=96 && tecla<=105){
      buscar_folio();
    }  
}

  function buscar_folio() {
    var textoBusqueda = $("input#buscar_dispositivo").val();
    if (textoBusqueda != "") {
        $.post("../php/buscar_dispositivo.php", {valorBusqueda: textoBusqueda}, function(mensaje) {
            $("#resultado_dispositivo").html(mensaje);
        }); 
    } else { 
        ("#resultado_dispositivo").html('No se encontraron dispositivos.');
  };
};
</script>
<!--Termina script dispositivos-->

<!--Script Buscar clientes redes-->
<script>

function PulsarTeclaRedes(){
    tecla = event.keyCode;
 
    if(tecla==32){
      buscar_clientes_redes();
    }else if(tecla==65){
      buscar_clientes_redes();
    }else if(tecla==69){
      buscar_clientes_redes();
    }else if(tecla==73){
      buscar_clientes_redes();
    }else if(tecla==79){
      buscar_clientes_redes();
    }else if(tecla==85){
      buscar_clientes_redes();
    }else if(tecla>=48 && tecla<=57){
      buscar_clientes_redes();
    }else if(tecla>=96 && tecla<=105){
      buscar_clientes_redes();
    }  
}

function buscar_clientes_redes() {
    var textoBusqueda = $("input#buscar_cliente_redes").val();
    if (textoBusqueda != "") {
        $.post("../php/buscar_cliente_redes.php", {valorBusqueda: textoBusqueda}, function(mensaje) {
            $("#resultado_clientes_redes").html(mensaje);
        }); 
    } else { 
        ("#resultado_clientes_redes").html('No se encontraron clientes.');
  };
};
</script>
<!--Termina Script Buscar clientes redes-->

<!--Script Buscar clientes-->
<script>
function PulsarTecla(){
    tecla = event.keyCode;
 
    if(tecla==32){
      buscar_clientes();
    }else if(tecla==65){
      buscar_clientes();
    }else if(tecla==69){
      buscar_clientes();
    }else if(tecla==73){
      buscar_clientes();
    }else if(tecla==79){
      buscar_clientes();
    }else if(tecla==85){
      buscar_clientes();
    }else if(tecla>=48 && tecla<=57){
      buscar_clientes();
    }else if(tecla>=96 && tecla<=105){
      buscar_clientes();
    }
}

function buscar_clientes() {
    var textoBusqueda = $("input#buscar_cliente").val();
    if (textoBusqueda != "") {
        $.post("../php/buscar_cliente.php", {valorBusqueda: textoBusqueda}, function(mensaje) {
            $("#resultado_clientes").html(mensaje);
        }); 
    } else { 
        ("#resultado_clientes").html('No se encontraron clientes.');
  };
};

function recargar() {
    setTimeout("location.href='instalaciones.php'", 5000);
  }
function recargar2() {
    setTimeout("location.href='admin_clientes.php'", 5000);
  }
function recargar3() {
    setTimeout("location.href='../views/tel.php'", 5000);
  }

  function recargar_corte() {
    var textoClave = $("input#clave").val();
    if (textoClave == "dinero$ic") {
      setTimeout("location.href='cortes_pagos.php'", 5000);
      var a = document.createElement("a");
        a.target = "_blank";
        a.href = "../php/corte_pago.php";
        a.click();
    }else{
      M.toast({html:"Clave Incorrecta.", classes: "rounded"});
    }
    
  }

  function admin() {
    setTimeout("location.href='home.php'", 5000);
  }
</script>
<!--Termina Script Buscar clientes-->


<!-- Modal Buscar clientes redes-->
  <div id="buscar_clientes_redes" class="modal modal-fixed-footer">
    <div class="modal-content">
      <nav>
        <div class="nav-wrapper">
          <form>
            <div class="input-field pink lighten-4">
              <input id="buscar_cliente_redes" type="search" placeholder="Buscar Cliente" maxlength="30" value="" autocomplete="off" onKeyUp="PulsarTeclaRedes();" autofocus="true" required>
              <label class="label-icon" for="search"><i class="material-icons">search</i></label>
              <i class="material-icons">close</i>
            </div>
          </form>
        </div>
      </nav>
      <p><div id="resultado_clientes_redes"></div></p>
    </div>
    <div class="modal-footer container">
      <a href="#" class="modal-action modal-close waves-effect waves-green btn-flat">Cerrar<i class="material-icons right">close</i></a>
    </div>
  </div>
<!--.....Termina Modal Buscar clientes redes-->

<!-- Modal Buscar clientes -->
  <div id="buscar_clientes" class="modal modal-fixed-footer">
    <div class="modal-content">
      <nav>
        <div class="nav-wrapper">
          <form>
            <div class="input-field pink lighten-4">
              <input id="buscar_cliente" type="search" placeholder="Buscar Cliente" maxlength="30" value="" autocomplete="off" onKeyUp="PulsarTecla();" autofocus="true" required>
              <label class="label-icon" for="search"><i class="material-icons">search</i></label>
              <i class="material-icons">close</i>
            </div>
          </form>
        </div>
      </nav>
      <p><div id="resultado_clientes"></div></p>
    </div>
    <div class="modal-footer container">
      <a href="#" class="modal-action modal-close waves-effect waves-green btn-flat">Cerrar<i class="material-icons right">close</i></a>
    </div>
  </div>
<!--.....Termina Modal Buscar clientes-->

<!--Ventana modal para la creación de la ruta-->
<div id="rutamodal" class="modal modal-fixed-footer">
    <div class="modal-content">
        <h3 id="resultado_ruta">¿Estás seguro de crear la ruta?</h3><br>
      <p class="center"><b>Al crear la ruta se mostrará un PDF en una nueva pestaña y se crear la ruta.</b></p><br><br>
     <h5>Tecnico(s) que ira(n) a la ruta:</h5> 
      <form>
      <div class="input-field col s12 m8 l8">
          <i class="material-icons prefix">people</i>
          <input id="tecnicos" type="text" class="validate" data-length="30" required>
          <label for="tecnicos">Tecnico(s): Ej. (Marcos, Ulix980, Muro)</label>
      </div>
      </form>
    </div>
    <div class="modal-footer container">
    <a class="modal-action modal-close waves-effect waves-green btn-flat" onclick="crear_ruta();recargar();">Crear<i class="material-icons right">done</i></a>
      <a href="#" class="modal-action modal-close waves-effect waves-green btn-flat">Cancelar<i class="material-icons right">close</i></a>
    </div>
  </div>
<!--.....Cierre de ventana modal para la creacion de ruta-->

<!-- Modal Buscar dispositivos-->
  <div id="buscar_dispositivo" class="modal modal-fixed-footer">
    <div class="modal-content">
      <nav>
        <div class="nav-wrapper">
          <form>
            <div class="input-field pink lighten-4">
              <input id="buscar_dispositivo" type="search" placeholder="Folio o nombre del cliente" maxlength="30" value="" autocomplete="off" onKeyUp="PulsarTeclaFolio();" autofocus="true" required>
              <label class="label-icon" for="search"><i class="material-icons">search</i></label>
              <i class="material-icons">close</i>
            </div>
          </form>
        </div>
      </nav>
      <p><div id="resultado_dispositivo"></div></p>
    </div>
    <div class="modal-footer container">
      <a href="#" class="modal-action modal-close waves-effect waves-green btn-flat">Cerrar<i class="material-icons right">close</i></a>
    </div>
  </div>
<!--.....Termina Modal Buscar dispositivos-->

<!--Modal cortes-->
<div id="corte" class="modal">
  <div class="modal-content">
    <h4 class="red-text center">! Advertencia !</h4><br>
    <h6 ><b>Una vez generado el corte se comenzara una nueva lista de pagos para el siguinete corte. </b></h6><br>
    <h5 class="red-text darken-2">¿DESEA CONTINUAR?</h5>
    <div class="row">
    <div class="input-field col s6">
        <i class="material-icons prefix">lock</i>
        <input type="password" name="clave" id="clave">
        <label for="clave">Ingresar Clave</label>
    </div>
    </div>
  </div>
  <div class="modal-footer">
      <a onclick="recargar_corte()" class="modal-action modal-close waves-effect waves-green btn-flat">Aceptar</a>
      <a href="#" class="modal-action modal-close waves-effect waves-red btn-flat">Cerrar<i class="material-icons right">close</i></a>
  </div>
</div>
<!--Cierre modal Cortes-->