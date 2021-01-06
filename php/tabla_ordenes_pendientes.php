    <p><div>
        <table class="bordered  highlight responsive-table">
          <thead>
            <tr>
                <th>Dias</th>
                <th>#Orden</th>
                <th>Cliente</th>
                <th>Descripción</th>
                <th>Fecha</th>
                <th>Comunidad</th>
                <th>Registró</th>
                <th>Estatus</th>
                <th>Realizo</th>
                <th>Atender</th>
                <th>+Ruta</th>
            </tr>
          </thead>
          <tbody>
          <?php                   
          //Obtiene la cantidad de filas que hay en la sql_orden
          $filas = mysqli_num_rows($sql_orden);
          //Si no existe ninguna fila que sea igual a $sql_ordenBusqueda, entonces mostramos el siguiente mensaje
          if ($filas <= 0) {
            echo '<h4>No se encontraron ordenes de servico.</h4>';
          } else {
            while($resultados = mysqli_fetch_array($sql_orden)) {
              $id_cliente = $resultados['id_cliente'];
              $id_usuario=$resultados['registro'];

              $users = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM users WHERE user_id=$id_usuario"));
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
              $Descripción = $resultados['trabajo'];

              $Realizo = $resultados['tecnicos_r'];
              if ($Realizo == '') {
                $Realizo = 'SIN';
              }
              if ($resultados['estatus'] == 'Cotizar') {
                $color_e = 'red darken-4';
                if ($resultados['trabajo'] == '') {
                  $Descripción = $resultados['solicitud'];
                }
              }else if($resultados['estatus'] == 'Cotizado') {
                $color_e = 'orange darken-4';
                $user_id = $resultados['cotizo'];
                if ($user_id == '') {
                  $Realizo = 'SIN';
                }else {  
                  $usuario = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM users WHERE user_id=$user_id"));
                  $Realizo = $usuario['firstname'];
                }
                
              }else if($resultados['estatus'] == 'Pedir') {
                $color_e = 'purple darken-2';
                $user_id = $resultados['confirmo'];
                if ($user_id == '') {
                  $Realizo = 'SIN';
                }else {  
                  $usuario = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM users WHERE user_id=$user_id"));
                  $Realizo = $usuario['firstname'];
                }
              }else if($resultados['estatus'] == 'Ejecutar') {
                $color_e = 'green darken-3';
                $user_id = $resultados['compro'];
                if ($user_id == '') {
                  $Realizo = 'SIN';
                }else {  
                  $usuario = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM users WHERE user_id=$user_id"));
                  $Realizo = $usuario['firstname'];
                }
              }else if($resultados['estatus'] == 'PorConfirmar') {
                $color_e = 'black';
                $Realizo = 'SIN';
              }else if($resultados['estatus'] == 'Autorizado') {
                $color_e = 'yellow darken-2';
                $user_id = $resultados['confirmo'];
                if ($user_id == '') {
                  $Realizo = 'SIN';
                }else {  
                  $usuario = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM users WHERE user_id=$user_id"));
                  $Realizo = $usuario['firstname'];
                }
              }else{
                $Descripción = $resultados['solicitud'];
                $color_e = 'blue darken-3';
              }
              ?>
                  <tr>
                    <td><span class="new badge <?php echo $color; ?>" data-badge-caption=""><?php echo $Dias; ?></span></td>
                    <td><b><?php echo $resultados['id']; ?></b></td>
                    <td><?php echo $cliente['nombre']; ?></td>
                    <td><?php echo $Descripción; ?></td>
                    <td><?php echo $resultados['fecha']; ?></td>
                    <td><?php echo $comunidad2['nombre']; ?></td>
                    <td><?php echo $users['firstname']; ?></td>
                    <td><span class="new badge <?php echo $color_e; ?>" data-badge-caption=""><b><?php echo $resultados['estatus']; ?></b></span> <?php echo $resultados['modificacion']; ?></td>
                    <td><?php echo $Realizo; ?></td>
                    <td><a href="atender_orden.php?id_orden=<?php echo $resultados['id']; ?>" class="btn btn-floating pink waves-effect waves-light"><i class="material-icons">send</i></a></td>
                    <td><a onclick="ruta(<?php echo $resultados['id']; ?>);" class="btn btn-floating pink waves-effect waves-light"><i class="material-icons">add</i></a></td>
                  </tr>
            <?php
            }
          }
          ?>
          </tbody>
        </table>
      </div></p>