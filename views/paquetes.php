<!DOCTYPE html>
<html lang="en">
<head>
<?php
  include('fredyNav.php');
  include('../php/cobrador.php');
?>
<style type="text/css">
  .select-dropdown{
    overflow-y: auto !important;
}
</style>
<title>SIC | Paquetes</title>
<script>
function insert_paquete() {
    var textoSubida = $("select#subida").val();
    var textoBajada = $("select#bajada").val();
    var textoMensualidad = $("input#mensualidad").val();
    if (textoSubida == 0) {
      M.toast({html :"Seleccione la velocidad de subida.", classes: "rounded"});
    }else if(textoBajada == 0){
      M.toast({html :"Seleccione la velocidad de bajada.", classes: "rounded"});
    }else if(textoMensualidad == 0){
      M.toast({html :"Indique la mensualidad. No puede quedar en 0.", classes: "rounded"});
    }else{
      $.post("../php/insert_paquete.php", {
          valorSubida: textoSubida,
          valorBajada: textoBajada,
          valorMensualidad: textoMensualidad
        }, function(mensaje) {
            $("#resultado_paquetes").html(mensaje);
        }); 
    }
};
</script>
</head>
<main>
<body>
  <div class="container">
  <div class="row" >
     <h3 class="hide-on-med-and-down">Registrar Paquete</h3>
     <h5 class="hide-on-large-only">Registrar Paquete</h5>
  </div>
    <div class="row">
      <div class="input-field col s6 m4 l4">
          <select id="bajada" class="browser-default">
            <option value="0" selected disabled>Bajada</option>
            <option value="128k">128 Kilobytes</option>
            <option value="256k">256 Kilobytes</option>
            <option value="512k">512 Kilobytes</option>
            <option value="768k">768 Kilobytes</option>
            <option value="1M">1 Mega</option>
            <option value="2M">2 Megas</option>
            <option value="3M">3 Megas</option>
            <option value="4M">4 Megas</option>
            <option value="5M">5 Megas</option>
            <option value="6M">6 Megas</option>
            <option value="7M">7 Megas</option>
            <option value="8M">8 Megas</option>
            <option value="9M">9 Megas</option>
            <option value="10M">10 Megas</option>
            <option value="15M">15 Megas</option>
            <option value="20M">20 Megas</option>
          </select>
      </div>
      <div class="input-field col s6 m4 l4">
          <select id="subida" class="browser-default">
            <option value="0" selected disabled>Subida</option>
            <option value="128k">128 Kilobytes</option>
            <option value="256k">256 Kilobytes</option>
            <option value="512k">512 Kilobytes</option>
            <option value="768k">768 Kilobytes</option>
            <option value="1M">1 Mega</option>
            <option value="2M">2 Megas</option>
            <option value="3M">3 Megas</option>
            <option value="4M">4 Megas</option>
            <option value="5M">5 Megas</option>
            <option value="6M">6 Megas</option>
            <option value="7M">7 Megas</option>
            <option value="8M">8 Megas</option>
            <option value="9M">9 Megas</option>
            <option value="10M">10 Megas</option>
            <option value="15M">15 Megas</option>
            <option value="20M">20 Megas</option>
          </select>
      </div>
      <div class="input-field col s6 m4 l4">
         <i class="material-icons prefix">monetization_on</i>
        <input type="number" id="mensualidad" value="0">
        <label for="mensualidad">Mensualidad</label>
      </div>
      <div class="input-field">
        <a onclick="insert_paquete();" class="waves-effect waves-light btn pink right"><i class="material-icons center">send</i></a>
      </div>
    </div>
    <div id="resultado_paquetes">
    <div class="row" >
      <h3 class="hide-on-med-and-down">Paquetes</h3>
      <h5 class="hide-on-large-only">Paquetes</h5>
    </div>
            <table class="bordered highlight">
                <thead>
                    <tr>
                        <th>No. Paquete</th>
                        <th>Bajada</th>
                        <th>Subida</th>
                        <th>Mensualidad</th>
                        <th>Editar</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                require('../php/conexion.php');
                $sql_tmp = mysqli_query($conn,"SELECT * FROM paquetes");
                $columnas = mysqli_num_rows($sql_tmp);
                if($columnas == 0){
                    ?>
                    <h5 class="center">No hay paquetes</h5>
                    <?php
                }else{
                    while($tmp = mysqli_fetch_array($sql_tmp)){
                ?>
                    <tr>
                      <td><?php echo $tmp['id_paquete']; ?></td>
                      <td><?php echo $tmp['bajada']; ?></td>
                      <td><?php echo $tmp['subida']; ?></td>
                      <td>$<?php echo $tmp['mensualidad']; ?></td>
                      <td><form method="post" action="../views/editar_paquete.php"><input name="no_paquete" type="hidden" value="<?php echo $tmp['id_paquete']; ?>"><button type="submit" class="btn-floating btn-tiny waves-effect waves-light pink"><i class="material-icons">edit</i></button></form></td>
                    </tr>
                <?php
                    }
                }
                mysqli_close($conn);
                ?>
                </tbody>
            </table>
            <br><br>
        </div>
  </div>
</body>
</main>
</html>