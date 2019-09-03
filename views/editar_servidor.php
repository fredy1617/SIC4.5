<!DOCTYPE html>
<html>
<head>
<?php
	include('fredyNav.php');
	include('../php/admin.php');
?>
	<title>SIC | Editar Servidor</title>
<script>
	function update_Servidor(){
		var textoIdServidor = $('input#id_servidor').val();
		var textoIp = $('input#ip').val();
		var textoUser = $('input#user').val();
		var textoPass = $('input#pass').val();
		var textoNombre = $('input#nombre').val();
		var textoPort = $('input#port').val();
		if (textoIp == "") {
	        M.toast({html :"El campo IP no puede estar vacío.", classes: "rounded"});
	      }else if(textoUser == ""){
	        M.toast({html :"El campo Usuario no puede estar vacío.", classes: "rounded"});
	      }else if(textoPass == ""){
	        M.toast({html :"El campo Contraseña no puede estar vacío.", classes: "rounded"});
	      }else if(textoPort == ""){
	        M.toast({html :"El campo Puerto no puede estar vacío.", classes: "rounded"});
	      }else if(textoNombre == ""){
	        M.toast({html :"El campo Nombre no puede estar vacío.", classes: "rounded"});
	      }else{
	      	$.post("../php/update_servidor.php", {
	      		valorIdServior: textoIdServidor,
	      		valorIp: textoIp,
	      		valorUser: textoUser,
	      		valorPass: textoPass,
	      		valorNombre: textoNombre,
	      		valorPort: textoPort
	      	},function(mensaje){
	      		$("#update_servidor").html(mensaje);
	      	});
	      }
	}
</script>
</head>
<?php
include('../php/conexion.php');
if (isset($_POST['no_servidor']) == false) {
	?>
	<script>
		function atras(){
			M.toast({html: "Regresando al listado...", classes: "rounded"});
			setTimeout("location.href = 'servidores.php'", 1000);
		}
		atras();
	</script>
	<?php
}else{
$id_servidor = $_POST['no_servidor'];
$servidor = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM servidores WHERE id_servidor = $id_servidor"));
?>
<body>
	<div class="container">
		<div id="update_servidor"></div>
		<div class="row">
			<h3 class="hide-on-med-and-down">Editar Servidor</h3>
		    <h5 class="hide-on-large-only">Editar Servidor</h5>
		</div>
		<div class="row">
			  <input type="hidden" id="id_servidor" value="<?php echo $servidor['id_servidor'];?>">
		      <div class="input-field col s12 m4 l4">
		        <i class="material-icons prefix">settings_ethernet</i>
		        <input type="text" id="ip" data-length="15" value="<?php echo $servidor['ip']; ?>">
		        <label for="ip">IP o dirección del Servidor:</label>
		      </div>
		      <div class="input-field col s12 m4 l4">
		        <i class="material-icons prefix">account_circle</i>
		        <input type="text" id="user" data-length="20" value="<?php echo $servidor['user']; ?>">
		        <label for="user">Usuario:</label>
		      </div>
		      <div class="input-field col s12 m4 l4">
		        <i class="material-icons prefix">lock</i>
		        <input type="text" id="pass" data-length="20" value="<?php echo $servidor['pass']; ?>">
		        <label for="pass">Contraseña:</label>
		      </div>
		      <div class="input-field col s12 m7 l7">
		        <i class="material-icons prefix">created</i>
		        <input type="text" id="nombre" data-length="50" value="<?php echo $servidor['nombre']; ?>">
		        <label for="nombre">Nombre:</label>
		      </div>
		      <div class="input-field col s12 m4 l4">
		        <i class="material-icons prefix">settings_input_hdmi</i>
		        <input type="number" id="port" data-length="6" value="<?php echo $servidor['port']; ?>">
		        <label for="port">Puerto:</label>
		      </div> 
		      <div class="input-field right">
		        <a onclick="update_Servidor();" class="waves-effect waves-light btn pink left"><i class="material-icons center">send</i></a>
		      </div>
		</div>
	</div>
</body>
<?php
}
?>
</html>