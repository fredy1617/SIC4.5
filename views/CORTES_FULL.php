<!DOCTYPE html>
<html lang="en">
<head>
<?php
  #INCLUIMOS EL ARCHIVO DONDE ESTA LA BARRA DE NAVEGACION DEL SISTEMA
  include('fredyNav.php');
  #INCLUIMOS EL ARCHIVO EL CUAL HACE QUE SOLOS LOS USUARIOS QUE SEAN ADMINISTRADORES PUEDAN ACCEDER A ESTA LISTA
  include('../php/admin.php');
?>
<title>SIC | Cortando...</title>
<script>
  function buscar_cortes_full() {
    M.toast({html: 'Cortando Servicios...', classes: 'rounded'});
    var textoServidor = $("select#servidor").val();
    if (textoServidor == 0) {
      M.toast({html:"Seleccione un servidor.", classes: "rounded"});
    }else{
      M.toast({html: 'Esto puede tardar unos minutos espera...', classes: 'rounded'});
      //HACEMOS VISIBLE EL DIV QUE TIENE LE ID content
      element = document.getElementById("content");
      element.style.display='block'; 

      $.post("../php/buscar_cortes_full.php", {
          valorServidor: textoServidor,
          }, function(mensaje) {
              $("#cortes").html(mensaje);
          }); 
    }
  };
  //FUNCION QUE CREA EL CORTE EN UNA TABLA CON FECHA PARA ASI IR TENIENDO UN REGISTRO
  function crear_corte(){
    $.post("../php/crear_corteInt.php", {
    }, function(mensaje){
        $("#Ver").html(mensaje);
    });
  }
</script>
</head>
<body>
	<div class="container"><br><br>
    <div id="Ver"></div>
    <div class="row">
      <div class="col l4 m4 s12"> 
      <h3>Cortar Servicio</h3>
      </div> 
      <div class="input-field col l4 m4 s12"><br>
        <select id="servidor" class="browser-default">
          <option value="0" selected>Seleccione un servidor</option>
          <?php 
          $sql = mysqli_query($conn,"SELECT * FROM servidores ");
          while($Servidor = mysqli_fetch_array($sql)){
          ?>
            <option value="<?php echo $Servidor['id_servidor'];?>"><?php echo $Servidor['nombre'];?></option>
          <?php
          }
          ?>
        </select>
      </div> 
      <div class="col l1 m1 s12"><br><br>
        <button class="btn waves-light waves-effect right pink" onclick="buscar_cortes_full();"><i class="material-icons prefix">send</i></button>
      </div> 
      <div class="col l2 m2 s12"><br><br>
        <a href="#!" class="waves-effect waves-light pink btn" onclick="crear_corte()"><i class="material-icons left">content_cut</i>Crear</a>
      </div> 
    </div>
   
  </a>
   <div class="row" id="cortes">
     
   </div> <br><br>
    <div class="progress" id="content" style="display: none;"><br><br>
      <div class="indeterminate indigo darken-4"></div>
    </div>
  </div>
</body>
</html>