<?php
include('../php/conexion.php');

$Id_Comunidad = $conn->real_escape_string($_POST['comunidad']);

$comunidad = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM comunidades WHERE  id_comunidad = '$Id_Comunidad'"));
$Nombre = 'SIC-'.$comunidad['nombre'];
$sql_cliente = mysqli_query($conn, "SELECT * FROM especiales WHERE nombre = '$Nombre' AND lugar = '$Id_Comunidad'");
if (mysqli_num_rows($sql_cliente)>0) {
  $cliente = mysqli_fetch_array($sql_cliente);
  $referencia = $cliente['referencia'];
}else{
  $referencia = '';
}
?> 
<div class="input-field" id="refe">
    <input id="referencia" type="text" class="validate" data-length="20" value="<?php echo $referencia; ?>" required>
    <label for="referencia">Breve descripcion:</label>
</div>