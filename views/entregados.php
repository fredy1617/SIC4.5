<html>
<head>
	<title>SIC | En taller</title>
<?php 
include('fredyNav.php');
include('../php/conexion.php');
include('../php/cobrador.php');
?>
<!--Inicia Script de dispositivos-->
<script>
  function buscar_folio_pendientes() {
    var textoBusqueda = $("input#buscar_dispositivo_pendiente").val();
  
    if (textoBusqueda != "") {
        $.post("../php/buscar_dispositivo_pendiente.php", {valorBusqueda: textoBusqueda}, function(mensaje) {
            $("#resultado_dispositivo_pendiente").html(mensaje);
        }); 
    } else { 
        ("#resultado_dispositivo_pendiente").html('No se encontraron dispositivos.');
  };
};
</script>
<!--Termina script dispositivos-->
</head>
<main>
<body>
<div class="container">
  <h2 class="hide-on-med-and-down">Entregados</h2>
  <h4 class="hide-on-large-only">Entregados</h4>
  <div id="resultado_dispositivo_pendiente">

  <table class="bordered centered highlight responsive-table">
    <thead>
      <tr>
          <th>Folio</th>
          <th>Nombre</th>
          <th>Telefono</th>
          <th>Falla</th>
          <th>Cables</th>
          <th>Fecha Entrada</th>
          <th>Técnico</th>
          <th>Precio</th>
      </tr>
    </thead>
    <tbody>
    <?php
    $sql = "SELECT * FROM dispositivos WHERE estatus = 'Listo (Entregado)'";
    $consulta = mysqli_query($conn, $sql);
  //Obtiene la cantidad de filas que hay en la consulta
  $filas = mysqli_num_rows($consulta);

  //Si no existe ninguna fila que sea igual a $consultaBusqueda, entonces mostramos el siguiente mensaje
  if ($filas == 0) {
    echo '<script type="text/javascript">Materialize.toast("No se encontraron dispositivos.", 4000, "rounded")</script>';
  } else {
    //La variable $resultado contiene el array que se genera en la consulta, así que obtenemos los datos y los mostramos en un bucle
    while($resultados = mysqli_fetch_array($consulta)) {
      $id_dispositivo = $resultados['id_dispositivo'];
      $nombre = $resultados['nombre'];
      $telefono = $resultados['telefono'];
      $falla = $resultados['falla'];
      $cables = $resultados['cables'];
      $fecha = $resultados['fecha'];
      $id_tecnico = $resultados['tecnico'];
      $precio = $resultados['precio'];
      if($id_tecnico==''){
          $tecnico[0] = 'Sin tecnico';
        }else{
          $tecnico = mysqli_fetch_array(mysqli_query($conn, "SELECT user_name, user_id FROM users WHERE user_id=$id_tecnico"));  
        }
      ?>      
                  <tr>
                    <td><b><?php echo $id_dispositivo;?></b></td>
                    <td><?php echo $nombre;?></td>
                    <td><?php echo $telefono;?></td>
                    <td><?php echo $falla;?></td>
                    <td><?php echo $cables;?></td>
                    <td><?php echo $fecha;?></td>
                    <td><?php echo $tecnico[0];?></td>
                    <td><?php echo '$'. $precio. '.00';?></td>
                  </tr>               
<?php          
    }//Fin while $resultados
  } //Fin else $filas
    ?>
                </tbody>
            </table>
    </div>
</div><br>
</body>
</main>
</html>
