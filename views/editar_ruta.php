<!DOCTYPE html>
<html>
<head>
	<title>SIC | Editar Ruta</title>
<?php
include('fredyNav.php');
include('../php/conexion.php');

$id = $_SESSION['user_id'];

if($id == 66 OR $id == 49){
	echo '<script>M.toast({html:"Bienvenida.", classes: "rounded"})</script>';
}else{
	echo '<script>M.toast({html:"Permiso denegado. Direccionando a la página principal.", classes: "rounded"})</script>';
  	echo '<script>admin();</script>';
	mysqli_close($conn);
	exit;
}
?>
</head>
<?php
if (isset($_POST['id_ruta']) == false) {
  ?>
  <script>    
    function atras() {
      M.toast({html: "Regresando a menu de rutas.", classes: "rounded"})
      setTimeout("location.href='menu_rutas.php'", 1000);
    }
    atras();
  </script>
  <?php
}else{
$id_ruta = $_POST['id_ruta'];
?>
<script>
  function update_ruta(){    
    var textoMat= $("textarea#mat").val();
    var textoIdRuta = $("input#id_ruta").val();
    var textoMatAnt = $("input#material_anterior").val();
    textoMaterial = textoMatAnt+'\n'+textoMat;
    if (textoMat == "") {
      M.toast({html:"El campo Material se encuentra vacío.", classes: "rounded"});
    }else{
      $.post("../php/update_ruta.php", { 
          valorMaterial: textoMaterial,
          valorIdRuta: textoIdRuta,
        }, function(mensaje) {
            $("#update").html(mensaje); 
        });
      }
  }
</script>
<body>
	<div class="container">
  <?php 
  $sql = mysqli_query($conn,"SELECT * FROM rutas WHERE id_ruta=$id_ruta");

  $datos = mysqli_fetch_array($sql);
  ?>
		<div class="row">
			<h2 class="hide-on-med-and-down">Editar Ruta:</h2>
 			<h4 class="hide-on-large-only">Editar Ruta:</h4>
		</div>
		<div class="row">
			<ul class="collection">
            <li class="collection-item avatar">
              <img src="../img/cliente.png" alt="" class="circle">
              <span class="title col s12"><b>No. Ruta: </b><?php echo $id_ruta; ?></span>
              <p class="col s12">
              	 <b>Fecha: </b><?php echo $datos['fecha']; ?><br>
                 <b>Responsable: </b><?php echo $datos['responsable']; ?><br><br>
                 <b class="col s1">Estatus: </b><?php echo ($datos['estatus'] == 1 ) ? '<span class="new badge green col s1" data-badge-caption="Terminado"></span>':'<span class="new badge red col s1" data-badge-caption="Pendiente"></span>'; ?><br>
                 <br><hr class="col s12">
                <div id="update"><b>Material: </b><?php echo $datos['material']; ?><br></div>
              </p>
            </li>
        </ul>		
		</div>
    <div class="row">
      <h4 class="hide-on-med-and-down">Agregar material:</h4>
      <h5 class="hide-on-large-only">Agregar material:</h5>
    </div>
    <form class="row">
      <div class="col s1"><br></div> 
      <div class="col s9 m6 l6">
        <div class="input-field">
          <i class="material-icons prefix">description</i>
          <textarea id="mat" class="materialize-textarea validate" data-length="100" required></textarea>
          <label for="mat">Material :</label>
        </div>
      </div>
        <input id="id_ruta" value="<?php echo htmlentities($id_ruta);?>" type="hidden">
        <input id="material_anterior" value="<?php echo htmlentities($datos['material']);?>" type="hidden">
      <div><br><br>
      	<a onclick="update_ruta();" class="waves-effect waves-light btn pink right"><i class="material-icons right">send</i>Registrar</a>
      </div>
      </form>
    <br>
    </div>
</body>
<?php 
}
?>
</html>