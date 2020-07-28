<?php 
include('../php/conexion.php');
date_default_timezone_set('America/Mexico_City');

$Comunidad = $conn->real_escape_string($_POST['valorComunidad']);
$IP = $conn->real_escape_string($_POST['valorIP']);
$Descripcion = $conn->real_escape_string($_POST['valorDescripcion']);

if (filter_var($IP, FILTER_VALIDATE_IP)) {
    $sql_central = "SELECT * FROM centrales_pings WHERE ip='$IP'";
    if(mysqli_num_rows(mysqli_query($conn, $sql_central))>0){
        echo '<script>M.toast({html :"Ya se encuentra una central con la misma IP.", classes: "rounded"})</script>';
    }else{
        //o $consultaBusqueda sea igual a nombre + (espacio) + apellido
        $sql = "INSERT INTO centrales_pings (comunidad, ip, descripcion) VALUES($Comunidad, '$IP', '$Descripcion')";
        if(mysqli_query($conn, $sql)){
            echo '<script>M.toast({html :"La central se registró satisfactoriamente.", classes: "rounded"})</script>';
        }else{
            echo '<script>M.toast({html :"Ha ocurrido un error.", classes: "rounded"})</script>'; 
        }
    }
}else{
    echo '<script>M.toast({html:"Formato de IP incorrecto, por favor escriba una IP válida.", classes: "rounded"})</script>';
}
?>
<div id="resultado_central">
    <div class="row">
      <br><br>
      <h3 class="hide-on-med-and-down col s12 m6 l6">Central Pings</h3>
        <h5 class="hide-on-large-only col s12 m6 l6">Central Pings</h5>

        <form class="col s12 m6 l6">
          <div class="row">
            <div class="input-field col s12">
              <i class="material-icons prefix">search</i>
              <input id="busqueda" name="busqueda" type="text" class="validate" onkeyup="buscar_central();">
              <label for="busqueda">Buscar(#Central, IP)</label>
            </div>
        </div>
        </form>
    </div>
      <table class="bordered highlight responsive-table">
        <thead>
          <tr>
            <th>No. Cental</th>
            <th>Comunidad</th>
            <th>IP</th>
            <th>Servidor</th>
            <th>Editar</th>
          </tr>
        </thead>
        <tbody id="CentalALL">
        <?php
        $sql_tmp = mysqli_query($conn,"SELECT * FROM centrales_pings");
        $columnas = mysqli_num_rows($sql_tmp);

        if($columnas == 0){
            echo '<h5 class="center">No hay centrales</h5>';
        }else{
            while($tmp = mysqli_fetch_array($sql_tmp)){            
                $id_comunidad = $tmp['comunidad'];
                $comunidad = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM comunidades WHERE id_comunidad = '$id_comunidad'"));
                $id_servidor = $comunidad['servidor'];
                $servidor = mysqli_fetch_array(mysqli_query($conn, "SELECT nombre FROM servidores WHERE id_servidor = '$id_servidor'"));
        ?>
            <tr>
              <td><?php echo $tmp['id']; ?></td>
              <td><?php echo $comunidad['nombre']; ?></td>
              <td><?php echo $tmp['ip']; ?></td>
              <td><?php echo $servidor['nombre']; ?></td>
              <td><form method="post" action="../views/editar_comunidad.php"><input name="id" type="hidden" value="<?php echo $tmp['id']; ?>"><button type="submit" class="btn-floating btn-tiny waves-effect waves-light pink"><i class="material-icons">edit</i></button></form></td>
            </tr>
        <?php
            }
        }
        ?>
    </tbody>
</table><br><br><br>
<?php
mysqli_close($conn);
?>