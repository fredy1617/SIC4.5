<!DOCTYPE html>
<html>
<head>
	<title>SIC | Trabajos</title>
<?php
include('fredyNav.php');
include('../php/admin.php');
include('../php/cobrador.php');
?>
<script>
	function reporte_x_fecha(){
		var textoDia = $("input#fecha_dia").val();
        var textoUsuario = $("select#usuario").val();
        if (textoUsuario == "") {
                M.toast({html:"Selecciona un usuario.", classes: "rounded"});
        }else{
          	$.post("../php/buscar_x_fecha.php", {
              valorDia: textoDia,
              valorUsuario: textoUsuario
            }, function(mensaje) {
                $("#mostrar_resultado").html(mensaje);
            }); 
        }
	};
</script>
</head>
<body>
	<div class="container">
		<br>
    	<h3 class="hide-on-med-and-down">Se Realizo</h3>
      	<h5 class="hide-on-large-only">Se Realizo</h5>
        <br>
        <div class="row">
            <div class="col s12 l1 m1">
                <br>   
            </div>
            <div class="col s12 l4 m4">
                <label for="fecha_dia">Dia:</label>
                <input id="fecha_dia" type="date" >    
            </div><br>
             <div class="col s12 l4 m4">
                <select id="usuario" class="browser-default"><br>
                    <option value="" selected>Seleccione un usuario</option>
                    <option value="0">Todos</option>
                    <option value="25">Ulises</option>
                    <option value="28">Luis</option>
                    <option value="49">Alfredo</option>
                    <?php 
                    $sql_tecnico = mysqli_query($conn,"SELECT * FROM users WHERE area = 'Redes'");
                    while($tecnico = mysqli_fetch_array($sql_tecnico)){
                    ?>
                        <option value="<?php echo $tecnico['user_id'];?>"><?php echo $tecnico['firstname'];?></option>
                      <?php
                    }
                    ?>
                </select>
            </div>
            <div>
                <button class="btn waves-light waves-effect right pink" onclick="reporte_x_fecha();"><i class="material-icons prefix">send</i></button>
            </div>
        </div>
    	<div id="mostrar_resultado"></div>	
	</div>
</body>
</html>