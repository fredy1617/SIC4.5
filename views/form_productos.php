<!DOCTYPE html>
<html>
<head>
	<title>Ventas SIC | Agregar Producto</title>
<?php
include('fredyNav.php');
?>
</head>
<body>
	<div class="container">
		<div class="row">
			<h3 class="hide-on-med-and-down">Agregar Producto:</h3>	
			<h5 class="hide-on-large-only">Agregar Producto:</h5>		
		</div>
	<form action="insert_producto.php" method="POST" enctype="multipart/form-data">
		<div class="row">
	    <div class="file-field input-field col s6 m6 l6">
	      <div class="btn">
	        <span>Imagen</span>
	        <input type="file" name="Imagen" required>
	      </div>
	      <div class="file-path-wrapper">
	        <input class="file-path validate" type="text">
	      </div>
	    </div>

        <div class="input-field col s12 m6 l6" >
          <i class="material-icons prefix">account_circle</i>
          <input id="precio" type="number" class="validate" data-length="6" name="precio" required>
          <label for="precio">Precio:</label>
        </div> 
        <div class="input-field col s12 m6 l6" >
          <i class="material-icons prefix">account_circle</i>
          <input id="cantidad" type="number" class="validate" data-length="30" name="cantidad" required>
          <label for="cantidad ">Cantidad:</label>
        </div> 
        <div class="input-field col s12 m6 l6" >
          <i class="material-icons prefix">location_on</i>
          <textarea id="descripcion" name="descripcion" class="
         materialize-textarea validate" data-length="100" required></textarea>
          <label for="descripcion">Descripcion:</label>
        </div> 
	</div>
	<button class="btn waves-effect waves-light right" type="submit" name="action">Guardar
    <i class="material-icons right">send</i>
  </button>
	  </form>	
	</div>
</body>
</html>