<!DOCTYPE html>
<html>
<head>
	<title>SIC | QUITAR PONER</title>
<?php
  include('fredyNav.php');
  include ('../php/conexion.php');
?>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<script>
	function mandar(){
		n=1;
		textoRefacciones = '';
		subtotal=0;
	  	while(n<11){
			var textoNumero = $("input#numero"+n).val();
			if (textoNumero == null) {
		      //M.toast({html :"No hay numero."+n, classes: "rounded"});
		    }else{
		     	//M.toast({html :"Es: "+textoNumero, classes: "rounded"});
				var textoDesc = $("input#Desc_"+n).val();
				var textoPrecio = $("input#precio"+n).val();
				if (textoDesc == "") {
		     	 M.toast({html :"Ingrese una Descripción En: Descripcion"+n, classes: "rounded"});
				}else if (textoPrecio == "") {
		     	 M.toast({html :"Ingrese un Precio En: Precio"+n, classes: "rounded"});					
				}else{
					var Precio = parseInt(textoPrecio);
					subtotal+= Precio;
					textoRefacciones += textoDesc+" - "+textoPrecio+", ";
				}
		    }
	  		n++;
	  	}
		M.toast({html :subtotal, classes: "rounded"});					

	}
</script>
</head>
<body>
	<div class="container">
		<form class=" row">    
	    	<div class="button">
	       		 <button type="button" id="add_Desc" class="waves-effect waves-light btn indigo right"><i class="material-icons right">send</i>add</button>
	   		</div>
		</form>
		<div>
			<a onclick="mandar();" class="waves-effect waves-light btn indigo right"><i class="material-icons right">send</i>ENVIAR</a>
		</div>
	</div>
</body>
<script>
	$(document).ready(function() {
    $("#add_Desc").click(function(){
        var contador = $("input[type='text']").length;

        $(this).before('<div class="row"><div class= "col s12 m6 l6"><div class="input-field"><i class="material-icons prefix">comment</i><input type="text" id="Desc_'+ contador +'" name="Desc[]"/><label for="Desc_'+ contador +'">Descripción No.'+ contador +' :</label></div></div><div class="col s10 m4 l4"><div class="input-field"><i class="material-icons prefix">attach_money</i><input type="number" id="precio'+ contador +'" name="precio[]"/><label for="precio'+ contador +'">Preco No.'+ contador +' :</label></div></div><input id="numero'+ contador +'" value="'+ contador +'" type="hidden"><button type="button" class="delete_Desc btn-floating btn-tiny waves-effect waves-light pink "><i class="material-icons prefix">delete</i></button></div>');
    });

    $(document).on('click', '.delete_Desc', function(){
        $(this).parent().remove();
    });
});
</script>
</html>