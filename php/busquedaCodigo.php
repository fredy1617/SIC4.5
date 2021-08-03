<?php 
#INCLUIMOS EL ARCHIVO CON LA CONEXION A LA BASE DE DATOS
include('../php/conexion.php');

#RECIBIMOS EL VALOR Codigo QUE SE NOS ENVIA DESDE EL FORMULARIO DE LA VISTA INVENTARIO (PARA VERIFICAR)
$Codigo = $conn->real_escape_string($_POST['codigo']);

#DECLARAMOS LAS VARIABLES NORMARES O VACIAS LA UNICA FORMA EN QUE CAMBIEN ES SI PASA LAS DOS CONDICONES IF
$existe = 'NO';
$nombre = '';
$unidad = '<option value="0" selected>Unidad: </option>';
$marca = '';  
$estatus = '';
$responsable = '<option value="0" selected>Responsable: </option>';
#CHECAMOS SI HAY ALGO EN EL INPUT CODIGO
if ($Codigo != '') {
  //VERIFICAR SI EL CODIGO ES NUEVO O YA EXISTE (PRODUCTO/MATERIAL)
  $query = mysqli_query($conn, "SELECT * FROM inventario WHERE codigo = $Codigo");
  if (mysqli_num_rows($query)>0) {
    #SI YA EXISTE RELLENAMOS EL FORMULARIO CON LA INFO YA PUESTA
    $producto = mysqli_fetch_array($query);
    $existe = 'SI';
    $nombre = $producto['nombre'];
    $unidad = '<option value="'.$producto['unidad'].'" selected>'.$producto['unidad'].'</option>';
    $marca = $producto['marca'];
    $estatus = $producto['estatus'];
    $responsable = '<option value="'.$producto['responsable'].'" selected>'.$producto['responsable'].'</option>';
  }
}
?>
<div class="row col s12" id="resultado_codigo">
  <div class="row">
    <div class="hide-on-med-and-down col s1"><br></div>          
    <div class="input-field col s6 m3 l3">
      <i class="material-icons prefix">edit</i>
      <input type="text" id="nombre" value="<?php echo $nombre ?>">
      <label for="nombre">Nombre:</label>
    </div>
    <div class="input-field col s6 m2 l2">
      <i class="material-icons prefix">filter_2</i>
      <input type="number" id="cantidad" value="0">
      <label for="cantidad">Cantidad:</label>
    </div>
    <div class="input-field col s6 m3 l3">
      <select id="unidad" class="browser-default">
        <?php echo $unidad ?>
        <option value="pieza(s)">Pieza(s)</option>
        <option value="kilogramo(s)">Kilogramo(s)</option>
        <option value="litro(s)">Litro(s)</option>
        <option value="metro(s)">Metro(s)</option>
      </select>
    </div>
  </div>
  <div class="row">
    <div class="hide-on-med-and-down col s1"><br></div>
    <div class="input-field col s6 m3 l3">
      <i class="material-icons prefix">local_offer</i>
      <input type="text" id="marca" value="<?php echo $marca ?>">
      <label for="marca">Marca: (ej: Ubiquiti)</label>
    </div>
    <div class="input-field col s6 m3 l3">
      <i class="material-icons prefix">edit</i>
      <input type="text" id="estatus" value="<?php echo $estatus ?>">
      <label for="estatus">Estaus :</label>
    </div>
    <div class="input-field col s6 m3 l3">
      <select id="responsable1" class="browser-default">
        <?php echo $responsable ?>
        <option value="Recursos Materiales">Recursos Materiales</option>
        <option value="Almacen">Almacen</option>
      </select>
    </div>
    <input id="existe" value="<?php echo $existe ?>" type="hidden">
    <div class="input-field"><br>
      <a onclick="insert_inventario();" class="waves-effect waves-light btn pink left right">GUARDAR <i class="material-icons right">send</i></a>
    </div>
  </div>
</div> 