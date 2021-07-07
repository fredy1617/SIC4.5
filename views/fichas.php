<!DOCTYPE html>
<html>
<head>
	<title>SIC | Fichas</title>
<?php
include ("fredyNav.php");
?>
<script>
	function confirmar(){
		var textoNombre = $("select#perfil").val();

        if (textoNombre == 0) {
			M.toast({html:'Seleccione un perfil.', classes: 'rounded'});
        }else{
        	//Ingresamos un mensaje a mostrar
			var mensaje = confirm("La ficha generara un costo ¿Desea crear la ficha?");
			//Detectamos si el usuario acepto el mensaje
			if (mensaje) {
				M.toast({html: 'Creando ficha...', classes: 'rounded'});
				$.post("../php/crear_ficha.php", {
              		valorNombre: textoNombre
				}, function(mensaje){
					$("#res_ficha").html(mensaje);
				});
				
			}
			//Detectamos si el usuario denegó el mensaje
			else{
				M.toast({html: 'Cancelado', classes: 'rounded'});
			}
        }
	};

	function imprime(ficha){
		M.toast({html:'Imprimiendo Ficha.', classes: 'rounded'});
		var a = document.createElement("a");
	      a.target = "_blank";
	      a.href = "../php/imprimir_ficha.php?Ficha="+ficha;
	      a.click();
	};
</script>
</head>
<body>
	<div class="container">
		<div class="row"><br>
			<h2>Generar Ficha:</h2>
			<div class="hide-on-med-and-down col s2"><br></div>
			<div class="input-field col s12 m5 l5">
                <i class="material-icons col s2">note<br></i>
                <select id="perfil" class="browser-default col s10" required>
                    <option value="0" selected >Perfil:</option>
                    <?php
                    $sql = mysqli_query($conn,"SELECT * FROM perfiles");
                    while($perfil = mysqli_fetch_array($sql)){
                    ?>
                        <option value="<?php echo $perfil['nombre'];?>"><?php echo $perfil['nombre'];?> - <?php echo $perfil['descripcion'];?></option>
                    <?php
                    } 
                    ?>
                </select>
             </div>
			<a class="waves-effect waves-light btn pink right" onclick="confirmar();"><i class="material-icons left">featured_play_list</i>Generar</a>
		</div><br><br>
		<div id="res_ficha"></div>
		<div id="imprime"></div>		
	</div>
</body>
</html>