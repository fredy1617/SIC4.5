<html>
<head>
	<title>SIC | Tel</title>
<?php 
include('fredyNav.php');
include('../php/conexion.php');
include('../php/cobrador.php');
?>
<!--Inicia Script de reportes tmp-->
<!--Termina script dispositivos-->
</head>
<main>
<body>
<div class="container">
  <div class="row" >
    <h3 class="hide-on-med-and-down">Teléfono</h3>
    <h5 class="hide-on-large-only">Teléfono</h5>
  </div>
  <p><div id="resultado_reporte_pendiente">
  <table class="bordered centered highlight">
    <thead>
      <tr>
          <th>Cliente</th>
          <th>Fecha</th>
          <th>Atender</th>
      </tr>
    </thead>
    <tbody>
    <?php
    $sql = "SELECT * FROM pagos WHERE Cotejado =1";
    $consulta = mysqli_query($conn, $sql);
  //Obtiene la cantidad de filas que hay en la consulta
  $filas = mysqli_num_rows($consulta);

  //Si no existe ninguna fila que sea igual a $consultaBusqueda, entonces mostramos el siguiente mensaje
  if ($filas == 0) {
    echo '<script type="text/javascript">M.toast({html:"No se encontraron pagos por cotejar.", classes: "rounded"})</script>';
  } else {

    //La variable $resultado contiene el array que se genera en la consulta, así que obtenemos los datos y los mostramos en un bucle
    while($resultados = mysqli_fetch_array($consulta)) {
      $id_cliente = $resultados['id_cliente'];
      $cliente = mysqli_fetch_array(mysqli_query($conn, "SELECT nombre, lugar, telefono FROM clientes WHERE id_cliente=$id_cliente"));
      $id_comunidad = $cliente['lugar'];
      $comunidad = mysqli_fetch_array(mysqli_query($conn, "SELECT nombre FROM comunidades WHERE id_comunidad=$id_comunidad"));
      $descripcion = $resultados['descripcion'];
      $fecha = $resultados['fecha'];
      if($resultados['tipo'] =='Mes-Tel' ){
        $pagina = 'cotejo_tel.php';
      }else{
        $pagina = 'cotejo_telm.php';
      }
      ?> 
                  <tr>
                    <td><a class="tooltipped" data-position="top" data-tooltip="<?php echo 'Telefono: '. $cliente['telefono']; echo '  Comunidad: '.$comunidad['nombre'];?>"><?php echo $cliente['nombre'];?></a></td>
                    <td><?php echo $fecha;?></td>
                    <td><br><form action="<?php echo $pagina;?>" method="post"><input type="hidden" name="id_pago" value="<?php echo $resultados['id_pago']?>"><button type="submit" class="btn-floating btn-tiny waves-effect waves-light pink"><i class="material-icons">send</i></button></form></td>
                  </tr>
                  
<?php          
    }//Fin while $resultados
  } //Fin else $filas
    ?>
                </tbody>
            </table>
  </div></p>
</div><br>
</body>
</main>
</html>