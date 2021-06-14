<html>
<head>
	<title>SIC | Tel</title>
<?php 
include('fredyNav.php');
include('../php/conexion.php');
include('../php/cobrador.php');
date_default_timezone_set('America/Mexico_City');
$Fecha_hoy = date('Y-m-d');
$tel = mysqli_fetch_array(mysqli_query($conn,"SELECT count(*) FROM clientes WHERE servicio IN ('Telefonia', 'Internet y Telefonia') AND tel_cortado = 0 AND corte_tel < '$Fecha_hoy'"));
?>
</head>
<main>
<body>
<div class="container">
  <div class="row" >
    <h3 class="hide-on-med-and-down">Teléfono</h3>
    <h5 class="hide-on-large-only">Teléfono</h5>
    <a class="waves-effect waves-light btn pink right" href="cortes_telefono.php">Cortes Telefono: <b class="black-text"><?php echo $tel['count(*)'];?></b><i class="material-icons left">phone_locked</i></a>
  </div>
  <div id="resultado_reporte_pendiente">
    <table class="bordered centered highlight">
      <thead>
        <tr>
            <th>Estatus</th>
            <th>#</th>
            <th>Cliente</th>
            <th>Tipo</th>
            <th>Cotejar</th>            
            <th>Atender</th>
        </tr>
      </thead>
      <tbody>
      <?php
      $sql = "SELECT * FROM pagos WHERE Cotejado =1 Order by id_cliente";
      $consulta = mysqli_query($conn, $sql);
      //Obtiene la cantidad de filas que hay en la consulta
      $filas = mysqli_num_rows($consulta);
      //Si no existe ninguna fila que sea igual a $consultaBusqueda, entonces mostramos el siguiente mensaje
      if ($filas == 0) {
        echo '<script>M.toast({html:"No se encontraron pagos por cotejar.", classes: "rounded"})</script>';
      }else {
        //La variable $resultado contiene el array que se genera en la consulta, así que obtenemos los datos y los mostramos en un bucle
        while($resultados = mysqli_fetch_array($consulta)) {
          $id_cliente = $resultados['id_cliente'];
          $cliente = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM clientes WHERE id_cliente=$id_cliente"));
          $id_comunidad = $cliente['lugar'];
          $comunidad = mysqli_fetch_array(mysqli_query($conn, "SELECT nombre FROM comunidades WHERE id_comunidad=$id_comunidad"));
          ?> 
            <tr>
              <?php 
              $ver = explode(" ", $resultados['descripcion']);
              if ($ver[1] >= 2019 ) {
                $fechaEntero = strtotime('-1 day', strtotime($cliente['fecha_instalacion']));
                $dia =  date("d", $fechaEntero);
                $array =  array('ENERO' => '01','FEBRERO' => '02', 'MARZO' => '03','ABRIL' => '04', 'MAYO' => '05', 'JUNIO' => '06', 'JULIO' => '07', 'AGOSTO' => '08', 'SEPTIEMBRE' => '09', 'OCTUBRE' => '10', 'NOVIEMBRE' => '11',  'DICIEMBRE' => '12');
                if (strlen($ver[0]) >= 4) {
                  $Mes = $array[$ver[0]];
                  $año = $ver[1];
                  $FechaCotejo = date($año.'-'.$Mes.'-'.$dia);
                }else{
                  $FechaCotejo = 'N / A';
                }
              }else{
                $FechaCotejo = 'N / A';
              }
              if ($resultados['tipo'] == 'Mes-Tel' AND $FechaCotejo == $Fecha_hoy) {
                $color = 'green';
              }else if ($resultados['tipo'] == 'Mes-Tel' AND $FechaCotejo < $Fecha_hoy) {
                $color = 'red darken-2';
              }else{
                $color = 'indigo';
              }
              ?>
              <td><span class="new badge <?php echo $color; ?>" data-badge-caption=""></span></td>
              <td><?php echo $cliente['id_cliente'];?></td>
              <td><?php echo $cliente['nombre'];?></td>
              <?php 
              if ($resultados['tipo'] == 'Mes-Tel'){
                $tipo_tel = 'Mensualidad';
              }else if ($resultados['tipo'] == 'Min-extra') {
                $tipo_tel = 'Min-Extra';
              }
              ?>
              <td><?php echo $tipo_tel;?></td>
              <td><?php echo $FechaCotejo;?></td>
              <td><br><form action="cotejo_tel.php" method="post"><input type="hidden" name="id_pago" value="<?php echo $resultados['id_pago']?>"><button type="submit" class="btn-floating btn-tiny waves-effect waves-light pink"><i class="material-icons">send</i></button></form></td>
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