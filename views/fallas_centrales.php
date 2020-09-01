<!DOCTYPE html>
<html>
<head>
  <title>SIC | Falla de Centrales</title>
<?php
#INCLUIMOS EL ARCHIVO DONDE ESTA LA BARRA DE NAVEGACION DEL SISTEMA
include('fredyNav.php');
?>
</head>
<body>
  <div class="container">
    <div class="row">
      <h3 class="hide-on-med-and-down"> FALLAS DE CENTRALES:</h3>
        <h5 class="hide-on-large-only"> FALLAS DE CENTRALES:</h5>
    </div>
    <table class="bordered highlight responsive-table" width="100%">
        <thead>
          <tr>
            <th>#</th>
            <th>Comunidad</th>
            <th>Descripicion (Antena)</th>
            <th>Ip</th>
            <th>Fecha Error</th>
            <th>Hora Error</th>  
            <th>Tiempo (m)</th>
          </tr>
        </thead>
        <tbody>
        <?php
        $sql = mysqli_query($conn, "SELECT * FROM errores_pings WHERE estatus = 'Pendiente'");
        $filas =  mysqli_num_rows($sql);
        if ($filas <= 0) {
          echo "<center><b><h3>No se encontraron fallas en la red</h3></b></center>";
        }else{
          while ( $resultados = mysqli_fetch_array($sql)) {
            $IP = $resultados['ip'];          
            $cosnulta = mysqli_query($conn,"SELECT * FROM centrales_pings WHERE ip = '$IP'");
            $Serv = 0;
            if (mysqli_num_rows($cosnulta)<=0) {
                $cosnulta = mysqli_query($conn,"SELECT * FROM servidores WHERE ip = '$IP'");
                $Serv = 1;
            } 
            $Central = mysqli_fetch_array($cosnulta);
            if ($Serv == 1) {
              $Comunidad = $Central['nombre'];
              $Descripicion = $Central['nombre'];
            }else{
              $id_comunidad = $Central['comunidad'];
              $sql_Comunidad = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM comunidades WHERE id_comunidad = $id_comunidad")); 
              $Comunidad = $sql_Comunidad['nombre'];
              $Descripicion = $Central ['descripcion'];
            }
          ?>
            <tr>
              <td><?php echo $resultados['id']; ?></td>
              <td><?php echo $Comunidad; ?></td>    
              <td><?php echo $Descripicion; ?></td>      
              <td><?php echo $IP; ?></td>    
              <td><?php echo $resultados['fecha_e']; ?></td>
              <td><?php echo $resultados['hora_e']; ?></td>
              <td><?php echo $resultados['contador']; ?> min</td>
            </tr>
          <?php
          }
        }
        ?>
        </tbody>        
    </table><br>
  </div>
</body>
</html>