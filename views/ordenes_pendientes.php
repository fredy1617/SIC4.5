<html>
<head>
	<title>SIC | Ordenes de Servicio</title>
<?php 
include('fredyNav.php');
include('../php/conexion.php');
include ('../php/cobrador.php');
$id_user = $_SESSION['user_id'];
?>
</head>
<script>
function regresar(id){
  textoEstatus = 'Revisar';
  $.post("../php/cambiar_orden.php", { 
          valorIdOrden: id,
          valorEstatus: textoEstatus
  }, function(mensaje) {
  $("#cambiar").html(mensaje);
  }); 
}
function cancelar(id){
  textoEstatus = 'Cancelar';
  $.post("../php/cambiar_orden.php", { 
          valorIdOrden: id,
          valorEstatus: textoEstatus
  }, function(mensaje) {
  $("#cambiar").html(mensaje);
  }); 
}
</script>
<main>
<body onload="buscar_rep();">
<div id="borrar_inst"></div>
<div id="reporte_borrar"></div>
<div class="container">
  <div class="row">
      <br><br>
      <h3 class="hide-on-med-and-down col l9">Ordenes de Servicio Pendientes</h3>
      <h5 class="hide-on-large-only col s12 m9 l9">Ordenes de Servicio Pendientes</h5>       
    </div>
  <div id="cambiar"></div>
  <div class="row">
      <p><div id="resultado_reporte_pendiente">
        <table class="bordered  highlight responsive-table">
          <thead>
            <tr>
                <th>Dias</th>
                <th>#Orden</th>
                <th>Cliente</th>
                <th>Solicitud</th>
                <th>Fecha</th>
                <th>Comunidad</th>
                <th>Registró</th>
                <th>Regresar</th>
                <th>Cancelar</th>
            </tr>
          </thead>
          <tbody>
          <?php
          $sql_orden = mysqli_query($conn,"SELECT * FROM orden_servicios  WHERE  estatus = 'Pendiente' ORDER BY fecha");
          //Obtiene la cantidad de filas que hay en la sql_orden
          $filas = mysqli_num_rows($sql_orden);
          //Si no existe ninguna fila que sea igual a $sql_ordenBusqueda, entonces mostramos el siguiente mensaje
          if ($filas <= 0) {
            echo '<script>M.toast({html:"No se encontraron ordenes de servico.", classes: "rounded"})</script>';
          } else {
            while($resultados = mysqli_fetch_array($sql_orden)) {
              $id_cliente = $resultados['id_cliente'];
              $id_user=$resultados['registro'];

              $users = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM users WHERE user_id=$id_user"));
              $cliente = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM especiales WHERE id_cliente=$id_cliente"));
              $id_comunidad = $cliente['lugar'];
              $comunidad2 = mysqli_fetch_array(mysqli_query($conn, "SELECT nombre FROM comunidades WHERE id_comunidad=$id_comunidad"));
              $Dias= 0;
              if ($resultados['fecha']<$Hoy) {
                $date1 = new DateTime($Hoy);
                $date2 = new DateTime($resultados['fecha']);
                //Le restamos a la fecha date1-date2
                $diff = $date1->diff($date2);
                $Dias= $diff->days;
              }
              $color = "green";
              if ($Dias>= 2 AND $Dias < 4) { $color = "yellow darken-2";
              }elseif ($Dias == 4 OR $Dias == 5) { $color = "orange darken-4";
              }elseif ($Dias >= 6) { $color = "red accent-4"; }


              
              echo '
                  <tr>
                    <td><span class="new badge '.$color.'" data-badge-caption="">'.$Dias.'</span></td>
                    <td><b>'.$resultados['id'].'</b></td>
                    <td>'.$cliente['nombre'].'</td>
                    <td>'.$resultados['solicitud'].'</td>
                    <td>'.$resultados['fecha'].'</td>
                    <td>'.$comunidad2['nombre'].'</td>
                    <td>'.$users['firstname'].'</td>
                    <td><a onclick="regresar('.$resultados['id'].');" class="btn btn-floating pink waves-effect waves-light"><i class="material-icons">reply</i></a></td>
                    <td><a onclick="cancelar('.$resultados['id'].');" class="btn btn-floating pink waves-effect waves-light"><i class="material-icons">do_not_disturb</i></a></td>
                  </tr>';

            }
          }
          ?>
          </tbody>
        </table>
      </div></p>
<br><br>
</div>
<br>
</body>
</main>
</html>