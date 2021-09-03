<?php
include('../php/conexion.php');
$User = $conn->real_escape_string($_POST['valorUsuario']);

if ($User ==  0) {
  $usuarios = mysqli_query($conn, "SELECT * FROM users WHERE area='Redes' OR user_id = 49 OR user_id = 28 OR user_id = 25");
}else {
  $usuarios = mysqli_query($conn, "SELECT * FROM users WHERE user_id = '$User' ");
}

while($usuario = mysqli_fetch_array($usuarios)){
  $ValorDe = $conn->real_escape_string($_POST['valorDe']);
  $ValorA = $conn->real_escape_string($_POST['valorA']);
  $DIA  = $ValorDe;
  $user=$usuario['user_name'];
  $id_user = $usuario['user_id'];
  $instalaciones = mysqli_fetch_array(mysqli_query($conn,"SELECT count(*) FROM clientes WHERE  fecha_instalacion >= '$ValorDe' AND fecha_instalacion <= '$ValorA' AND  tecnico LIKE '%$user%'")); 
  $Ordenes = mysqli_fetch_array(mysqli_query($conn,"SELECT count(*) FROM orden_servicios WHERE (fecha_s >= '$ValorDe' AND fecha_s <= '$ValorA' AND  tecnicos_s LIKE '%$user%') OR (fecha_r >= '$ValorDe' AND fecha_r <= '$ValorA' AND  tecnicos_r LIKE '%$user%')")); 
  $Reportes_Oficina = mysqli_fetch_array(mysqli_query($conn,"SELECT count(*) FROM reportes WHERE (fecha_solucion >= '$ValorDe' AND fecha_solucion <= '$ValorA'  AND campo = 0 AND atendido = 1 AND (tecnico = '$id_user' OR apoyo = '$id_user')) OR (fecha_d >= '$ValorDe' AND fecha_d <= '$ValorA' AND tecnico_d = '$id_user')"));
  $Reportes_Campo = mysqli_fetch_array(mysqli_query($conn,"SELECT count(*) FROM reportes WHERE fecha_solucion >= '$ValorDe' AND fecha_solucion <= '$ValorA'  AND campo = 1 AND atendido = 1 AND (tecnico = '$id_user' OR apoyo = '$id_user')"));
?>
<br><br>
<h3 class="center">TECNICO: <?php echo $usuario['firstname']; ?></h3>
<h4>Trabajo: </h4>
<h5 class="indigo-text">Instalaciones (<?php echo $instalaciones['count(*)']; ?>) <--> Reportes Ofician (<?php echo $Reportes_Oficina['count(*)']; ?>) <--> Reportes Campo (<?php echo $Reportes_Campo['count(*)']; ?>) <--> Ordenes (<?php echo $Ordenes['count(*)']; ?>)</h5>
<table class="bordered highlight responsive-table">
    <thead>
      <tr>
        <th>#</th>
        <th>Nombre</th>
        <th>Tipo</th>
        <th>Comunidad</th>
        <th>Fecha Registro</th>
        <th>Hora Registro</th>
        <th>Fecha Termino</th>
        <th>Hora Termino</th>
        <th>Diagnostico</th>
        <th>Solucion</th>
        <th>Técnicos</th>
        <th>Zona</th>
      </tr>
    </thead>
    <tbody>
    <?php
    while ($DIA <= $ValorA) {
      if ($instalaciones['count(*)'] == 0 AND $Reportes_Campo['count(*)'] == 0 AND $Reportes_Oficina['count(*)'] == 0 AND $Ordenes['count(*)'] > 0) {
          $sql_orden1 = mysqli_query($conn,"SELECT * FROM orden_servicios  WHERE (fecha_r = '$DIA' AND tecnicos_r LIKE '%$user%') OR (fecha_s = '$DIA' AND tecnicos_s LIKE '%$user%')");
          if(mysqli_num_rows($sql_orden1) > 0){ 
              #IMPRIMIR ORDEN --- --- 11 +++++++++++++++++++++++++++++++++++++++++
              while($orden = mysqli_fetch_array($sql_orden1)){
                $id_cliente_o = $orden['id_cliente'];
                $cliente_o = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM especiales WHERE id_cliente=$id_cliente_o"));
                $id_comunidad_o = $cliente_o['lugar'];
                $comunidad_o = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM comunidades WHERE id_comunidad=$id_comunidad_o"));
                
                $cadena_de_texto = $orden['tecnicos_r'];
                $cadena_buscada   = $user;
                $posicion_coincidencia = strrpos($cadena_de_texto, $cadena_buscada);
                   
                  //se puede hacer la comparacion con 'false' o 'true' y los comparadores '===' o '!=='
                  if ($posicion_coincidencia === false){
                      $tecnicos = $orden['tecnicos_s'];
                      $fecha = $orden['fecha_s'];
                      $hora = $orden['hora_s'];
                  } else {
                      $tecnicos = $orden['tecnicos_r'];
                      $fecha = $orden['fecha_r'];
                      $hora = $orden['hora_r'];
                  }
                  ?>
                  <tr>
                    <td><?php echo $id_cliente_o; ?></td>
                    <td><?php echo $cliente_o['nombre']; ?></td>            
                    <td><b>Orden de Servicio</b></td>            
                    <td><?php echo $comunidad_o['nombre']; ?></td>            
                    <td><?php echo $orden['fecha']; ?></td>
                    <td><?php echo $orden['hora']; ?></td>
                    <td><?php echo $fecha ?></td>
                    <td><?php echo $hora ?></td>
                    <td><?php echo $orden['solicitud']; ?></td>
                    <td><?php echo $orden['trabajo'] ?></td>
                    <td><?php echo $tecnicos ?></td>
                    <td>Campo</td>
                  </tr>
            <?php
              }
          }
      }
      $resultado_instalaciones = mysqli_query($conn,"SELECT * FROM clientes WHERE fecha_instalacion = '$DIA' AND  tecnico LIKE '%$user%' ORDER BY hora_alta");
      $aux = mysqli_num_rows($resultado_instalaciones);
      if($aux > 0){
      $iniciar = 0;
      $iniciar_orden = 0;
      while($instalaciones = mysqli_fetch_array($resultado_instalaciones)){
        $aux --;
        $hora_alta = $instalaciones['hora_alta'];
        #BUSACAR E IMPRIMIR REPORTES DE MISMO O MENOR HORA Y MISMA  FECHA QUE LA INSTALACION
        $sql_r = mysqli_query($conn, "SELECT * FROM reportes WHERE  (fecha_solucion = '$DIA'  AND atendido = 1 AND hora_atendido < '$hora_alta' AND (tecnico = '$id_user' OR apoyo = '$id_user' )) OR (fecha_d = '$DIA' AND tecnico_d = '$id_user'  AND hora_atendido < '$hora_alta') ORDER BY hora_atendido LIMIT $iniciar, 100");
        if(mysqli_num_rows($sql_r) > 0){ 
        $iniciar = $iniciar+mysqli_num_rows($sql_r);
        while ($info = mysqli_fetch_array($sql_r)) {
          #HORA DE REPORTE Y SQL ORDEN E IF SI HAY contador de ordenes $iniciar_orden
          $hora_reporte_men = $info['hora_atendido'];
          $sql_orden1 = mysqli_query($conn,"SELECT * FROM orden_servicios WHERE (fecha_r = '$DIA' AND  hora_r < '$hora_reporte_men' AND tecnicos_r LIKE '%$user%') OR (fecha_s = '$DIA' AND  hora_s < '$hora_reporte_men' AND tecnicos_s LIKE '%$user%' ) LIMIT $iniciar_orden, 20");
          if(mysqli_num_rows($sql_orden1) > 0){ 
            #IMPRIMIR ORDEN ---1  +++++++++++++++++++++++++++++++++++++++++++++++
            $iniciar_orden = $iniciar_orden+mysqli_num_rows($sql_orden1);
            while($orden = mysqli_fetch_array($sql_orden1)){
              $id_cliente_o = $orden['id_cliente'];
              $cliente_o = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM especiales WHERE id_cliente=$id_cliente_o"));
              $id_comunidad_o = $cliente_o['lugar'];
              $comunidad_o = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM comunidades WHERE id_comunidad=$id_comunidad_o"));
            
              $cadena_de_texto = $orden['tecnicos_r'];
              $cadena_buscada   = $user;
              $posicion_coincidencia = strrpos($cadena_de_texto, $cadena_buscada);
                   
              //se puede hacer la comparacion con 'false' o 'true' y los comparadores '===' o '!=='
              if ($posicion_coincidencia === false) {
                  $tecnicos = $orden['tecnicos_s'];
                  $fecha = $orden['fecha_s'];
                  $hora = $orden['hora_s'];
              } else {
                  $tecnicos = $orden['tecnicos_r'];
                  $fecha = $orden['fecha_r'];
                  $hora = $orden['hora_r'];
              }
              ?>
              <tr>
                <td><?php echo $id_cliente_o; ?></td>
                <td><?php echo $cliente_o['nombre']; ?></td>            
                <td><b>Orden de Servicio</b></td>            
                <td><?php echo $comunidad_o['nombre']; ?></td>  
                <td><?php echo $orden['fecha']; ?></td>
                <td><?php echo $orden['hora']; ?></td>
                <td><?php echo $fecha ?></td>
                <td><?php echo $hora ?></td>
                <td><?php echo $orden['solicitud']; ?></td>
                <td><?php echo $orden['trabajo'] ?></td>
                <td><?php echo $tecnicos ?></td>
                <td>Campo</td>
              </tr>
          <?php
          }
          }
          
          #IMPRIMIR REPOERTE ---- 2 ******************************************
          $id_cliente = $info['id_cliente'];            
          $sql2 = mysqli_query($conn, "SELECT * FROM clientes WHERE id_cliente=$id_cliente");
          if (mysqli_num_rows($sql2) == 0) {
            $sql2 = mysqli_query($conn, "SELECT * FROM especiales WHERE id_cliente=$id_cliente");
          }
          $cliente = mysqli_fetch_array($sql2);
          if ($info['descripcion'] == 'Actividad') {
            $id_actividad =$info['id_reporte'];
            $sql_lugar = mysqli_query($conn, "SELECT * FROM lugar_actividades WHERE id_actividad=$id_actividad");
            if (mysqli_num_rows($sql_lugar)) {
              $lugar = mysqli_fetch_array($sql_lugar);
              $id_comunidad = $lugar['lugar'];
            }else{
              $id_comunidad = $cliente['lugar']; 
            }
          }else{
            $id_comunidad = $cliente['lugar'];
          }
          $comunidad = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM comunidades WHERE id_comunidad=$id_comunidad"));
          #VEMOS SI ES SOLO UN DIAGNOSTICO DE REPORTE
          if ($info['fecha_d']==$DIA AND $info['tecnico_d'] == $id_user) {
            $id_d =$info['tecnico_d'];
            if ($id_d != '') {
              $tecnico_d = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM users WHERE user_id = $id_d"));
            }
            ?>
            <tr>
              <td><?php echo $id_cliente; ?></td>            
              <td><?php echo $cliente['nombre']; ?></td>            
              <td><b>Reporte</b></td>            
              <td><?php echo $comunidad['nombre']; ?></td>            
              <td><?php echo $info['fecha']; ?></td>
              <td><?php echo $info['hora_registro']; ?></td>
              <td><?php echo $info['fecha_d']; ?></td>
              <td><?php echo $info['hora_d']; ?></td>
              <td><b>Reporte: </b> <?php echo $info['descripcion']; ?>.<br><b>Diagnostico: </b><?php echo $info['falla']; ?>.</td>
              <td><b>Es Un Diagnostico: </b>Ser reviso en oficina y se envio a campo</td>
              <td><?php echo $tecnico_d['firstname']; ?></td>
              <td><?php echo "Oficina"; ?></td>
            </tr>
            <?php
          }
          #VEMOS SI ES UN TERMINO DE REPORTE (SOLUCION)
          if ($info['fecha_solucion']==$DIA AND $info['atendido'] == 1 AND ($info['tecnico'] == $id_user OR $info['apoyo'] == $id_user)) {
            $id_tec = $info['tecnico'];
            $tecnico = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM users WHERE user_id = $id_tec"));
            if ($info['apoyo'] != 0) {
              $id_apoyo = $info['apoyo'];
              $A = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM users WHERE user_id = $id_apoyo"));
              $Apoyo = ', Apoyo: '.$A['firstname'];
            }else{ $Apoyo = ''; }
            ?>
            <tr>
              <td><?php echo $id_cliente; ?></td>            
              <td><?php echo $cliente['nombre']; ?></td>            
              <td><b>Reporte</b></td>            
              <td><?php echo $comunidad['nombre']; ?></td>            
              <td><?php echo $info['fecha']; ?></td>
              <td><?php echo $info['hora_registro']; ?></td>
              <td><?php echo $info['fecha_solucion']; ?></td>
              <td><?php echo $info['hora_atendido']; ?></td>
              <td><b>Reporte: </b> <?php echo $info['descripcion']; ?>.<br><b>Diagnostico: </b><?php echo $info['falla']; ?>.</td>
              <td><?php echo $info['solucion']; ?></td>
              <td><?php echo $tecnico['firstname'].$Apoyo; ?></td>
              <td><?php echo ($info['campo'] == 1 ) ? "Campo":"Oficina"; ?></td>
            </tr>
            <?php
          }
          }
        }
          #SQL ORDEN COMPARANDO CON HORA DE INSTALACION e $iniciar_orden E IF SI HAY 
          $sql_orden2 = mysqli_query($conn,"SELECT * FROM orden_servicios WHERE (fecha_r = '$DIA' AND  hora_r < '$hora_alta' AND tecnicos_r LIKE '%$user%') OR (fecha_s = '$DIA' AND  hora_s < '$hora_alta' AND tecnicos_s LIKE '%$user%') LIMIT $iniciar_orden, 20");
          if(mysqli_num_rows($sql_orden2) > 0){ 
            #IMPRIMIR ORDEN --- 3 +++++++++++++++++++++++++++++++++++++++++++++++++++++++++
            $iniciar_orden = $iniciar_orden+mysqli_num_rows($sql_orden2);
            while($orden2 = mysqli_fetch_array($sql_orden2)){
              $id_cliente_o2 = $orden2['id_cliente'];
              $cliente_o2 = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM especiales WHERE id_cliente=$id_cliente_o2"));
              $id_comunidad_o2 = $cliente_o2['lugar'];
              $comunidad_o2 = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM comunidades WHERE id_comunidad=$id_comunidad_o2"));
              
              $cadena_de_texto = $orden2['tecnicos_r'];
              $cadena_buscada   = $user;
              $posicion_coincidencia = strrpos($cadena_de_texto, $cadena_buscada);
                   
              //se puede hacer la comparacion con 'false' o 'true' y los comparadores '===' o '!=='
              if ($posicion_coincidencia === false) {
                  $tecnicos = $orden2['tecnicos_s'];
                  $fecha = $orden2['fecha_s'];
                  $hora = $orden2['hora_s'];
              } else {
                  $tecnicos = $orden2['tecnicos_r'];
                  $fecha = $orden2['fecha_r'];
                  $hora = $orden2['hora_r'];
              }
              ?>
              <tr>
                <td><?php echo $id_cliente_o2; ?></td>
                <td><?php echo $cliente_o2['nombre']; ?></td>            
                <td><b>Orden de Servicio</b></td>            
                <td><?php echo $comunidad_o2['nombre']; ?></td>            
                <td><?php echo $orden2['fecha']; ?></td>
                <td><?php echo $orden2['hora']; ?></td>
                <td><?php echo $fecha ?></td>
                <td><?php echo $hora ?></td>
                <td><?php echo $orden2['solicitud']; ?></td>
                <td><?php echo $orden2['trabajo'] ?></td>
                <td><?php echo $tecnicos ?></td>
                <td>Campo</td>
              </tr>
          <?php
            }
          }
        #IMPRIMIR LA INSTALACION ----- 4 <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<
        $id_comunidad = $instalaciones['lugar'];        
        $comunidad = mysqli_fetch_array(mysqli_query($conn,"SELECT * FROM comunidades WHERE id_comunidad = '$id_comunidad'"));
        ?>
        <tr>
          <td><?php echo $instalaciones['id_cliente'];?></td>
          <td><?php echo $instalaciones['nombre'];?></td>
          <td><b>Instalacion</b></td>
          <td><?php echo $comunidad['nombre'];?></td>
          <td><?php echo $instalaciones['fecha_registro']; ?></td>
          <td><?php echo $instalaciones['hora_registro']; ?></td>
          <td><?php echo $instalaciones['fecha_instalacion'];?></td>
          <td><?php echo $instalaciones['hora_alta']; ?></td>
          <td>Instalacion</td>
          <td>Instalacion</td>
          <td><?php echo $instalaciones['tecnico'];?></td>
          <td>Campo</td>
        </tr>
        <?php
        if ($aux == 0) {
          #BUSACAR E IMPRIMIR REPORTES MAYOR HORA y MISMA FECHA QUE LA ULTIMA INSTALACION
          $sql_r2 = mysqli_query($conn, "SELECT * FROM reportes WHERE  (fecha_solucion = '$DIA'  AND atendido = 1 AND hora_atendido > '$hora_alta' AND (tecnico = '$id_user' OR apoyo = '$id_user')) OR (fecha_d = '$DIA' AND tecnico_d = '$id_user' AND hora_atendido > '$hora_alta')  ORDER BY hora_atendido");
          #Hora de reporte y contador de reportes $cont_r
          $cont_r = mysqli_num_rows($sql_r2);
          if($cont_r > 0){ 
          while ($info = mysqli_fetch_array($sql_r2)) {
            $cont_r --;
            #HORA DE REPORTE Y SQL ORDEN E IF SI HAY 
            $hora_reporte_mayor = $info['hora_atendido'];
            $sql_orden1 = mysqli_query($conn,"SELECT * FROM orden_servicios WHERE (fecha_r = '$DIA' AND  hora_r < '$hora_reporte_mayor' AND tecnicos_r LIKE '%$user%') OR (fecha_s = '$DIA' AND  hora_s < '$hora_reporte_mayor' AND tecnicos_s LIKE '%$user%' ) LIMIT $iniciar_orden, 20");
            if(mysqli_num_rows($sql_orden1) > 0){ 
              #IMPRIMIR ORDEN --- --- 5 +++++++++++++++++++++++++++++++++++++++++++++++++++++
              $iniciar_orden = $iniciar_orden+mysqli_num_rows($sql_orden1);
              while($orden = mysqli_fetch_array($sql_orden1)){
                $id_cliente_o = $orden['id_cliente'];
                $cliente_o = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM especiales WHERE id_cliente=$id_cliente_o"));
                $id_comunidad_o = $cliente_o['lugar'];
                $comunidad_o = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM comunidades WHERE id_comunidad=$id_comunidad_o"));
                
                $cadena_de_texto = $orden['tecnicos_r'];
                $cadena_buscada   = $user;
                $posicion_coincidencia = strrpos($cadena_de_texto, $cadena_buscada);
                   
                  //se puede hacer la comparacion con 'false' o 'true' y los comparadores '===' o '!=='
                  if ($posicion_coincidencia === false){
                      $tecnicos = $orden['tecnicos_s'];
                      $fecha = $orden['fecha_s'];
                      $hora = $orden['hora_s'];
                  } else {
                      $tecnicos = $orden['tecnicos_r'];
                      $fecha = $orden['fecha_r'];
                      $hora = $orden['hora_r'];
                  }
                  ?>
                  <tr>
                    <td><?php echo $id_cliente_o; ?></td>
                    <td><?php echo $cliente_o['nombre']; ?></td>            
                    <td><b>Orden de Servicio</b></td>            
                    <td><?php echo $comunidad_o['nombre']; ?></td>            
                    <td><?php echo $orden['fecha']; ?></td>
                    <td><?php echo $orden['hora']; ?></td>
                    <td><?php echo $fecha ?></td>
                    <td><?php echo $hora ?></td>
                    <td><?php echo $orden['solicitud']; ?></td>
                    <td><?php echo $orden['trabajo'] ?></td>
                    <td><?php echo $tecnicos ?></td>
                    <td>Campo</td>
                  </tr>
            <?php
              }
            }
          #IMPRIMIR REPOERTE ---- 6 ********************************************************
          $id_cliente = $info['id_cliente'];            
          $sql2 = mysqli_query($conn, "SELECT * FROM clientes WHERE id_cliente=$id_cliente");
          if (mysqli_num_rows($sql2) == 0) {
            $sql2 = mysqli_query($conn, "SELECT * FROM especiales WHERE id_cliente=$id_cliente");
          }
          $cliente = mysqli_fetch_array($sql2);
          if ($info['descripcion'] == 'Actividad') {
            $id_actividad =$info['id_reporte'];
            $sql_lugar = mysqli_query($conn, "SELECT * FROM lugar_actividades WHERE id_actividad=$id_actividad");
            if (mysqli_num_rows($sql_lugar)) {
              $lugar = mysqli_fetch_array($sql_lugar);
              $id_comunidad = $lugar['lugar'];
            }else{
              $id_comunidad = $cliente['lugar']; 
            }
          }else{
            $id_comunidad = $cliente['lugar'];
          }
          $comunidad = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM comunidades WHERE id_comunidad=$id_comunidad"));
          #VEMOS SI ES SOLO UN DIAGNOSTICO DE REPORTE
          if ($info['fecha_d']==$DIA AND $info['tecnico_d'] == $id_user) {
            $id_d =$info['tecnico_d'];
            if ($id_d != '') {
              $tecnico_d = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM users WHERE user_id = $id_d"));
            }
            ?>
            <tr>
              <td><?php echo $id_cliente; ?></td>            
              <td><?php echo $cliente['nombre']; ?></td>            
              <td><b>Reporte</b></td>            
              <td><?php echo $comunidad['nombre']; ?></td>            
              <td><?php echo $info['fecha']; ?></td>
              <td><?php echo $info['hora_registro']; ?></td>
              <td><?php echo $info['fecha_d']; ?></td>
              <td><?php echo $info['hora_d']; ?></td>
              <td><b>Reporte: </b> <?php echo $info['descripcion']; ?>.<br><b>Diagnostico: </b><?php echo $info['falla']; ?>.</td>
              <td><b>Es Un Diagnostico: </b>Ser reviso en oficina y se envio a campo</td>
              <td><?php echo $tecnico_d['firstname']; ?></td>
              <td><?php echo "Oficina"; ?></td>
            </tr>
            <?php
          }
          #VEMOS SI ES UN TERMINO DE REPORTE (SOLUCION)
          if ($info['fecha_solucion']==$DIA AND $info['atendido'] == 1 AND ($info['tecnico'] == $id_user OR $info['apoyo'] == $id_user )) {
            $id_tec = $info['tecnico'];
            $tecnico = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM users WHERE user_id = $id_tec"));
            if ($info['apoyo'] != 0) {
              $id_apoyo = $info['apoyo'];
              $A = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM users WHERE user_id = $id_apoyo"));
              $Apoyo = ', Apoyo: '.$A['firstname'];
            }else{ $Apoyo = ''; }
            ?>
            <tr>
              <td><?php echo $id_cliente; ?></td>            
              <td><?php echo $cliente['nombre']; ?></td>            
              <td><b>Reporte</b></td>            
              <td><?php echo $comunidad['nombre']; ?></td>            
              <td><?php echo $info['fecha']; ?></td>
              <td><?php echo $info['hora_registro']; ?></td>
              <td><?php echo $info['fecha_solucion']; ?></td>
              <td><?php echo $info['hora_atendido']; ?></td>
              <td><b>Reporte: </b> <?php echo $info['descripcion']; ?>.<br><b>Diagnostico: </b><?php echo $info['falla']; ?>.</td>
              <td><?php echo $info['solucion']; ?></td>
              <td><?php echo $tecnico['firstname'].$Apoyo; ?></td>
              <td><?php echo ($info['campo'] == 1 ) ? "Campo":"Oficina"; ?></td>
            </tr>
            <?php
          }
            #SQL ORDEN COMPARANDO CON HORA DE REPORTE Y EL CONTADOR DE REPORTES EN CERO 
            if ($cont_r == 0) {
               $sql_orden1 = mysqli_query($conn,"SELECT * FROM orden_servicios WHERE (fecha_r = '$DIA' AND  hora_r > '$hora_reporte_mayor' AND tecnicos_r LIKE '%$user%') OR (fecha_s = '$DIA' AND  hora_s > '$hora_reporte_mayor' AND tecnicos_s LIKE '%$user%')");
                if(mysqli_num_rows($sql_orden1) > 0){ 
                #IMPRIMIR ORDEN --- --- 7 +++++++++++++++++++++++++++++++++++++++++++++
                while($orden = mysqli_fetch_array($sql_orden1)){
                  $id_cliente_o = $orden['id_cliente'];
                  $cliente_o = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM especiales WHERE id_cliente=$id_cliente_o"));
                  $id_comunidad_o = $cliente_o['lugar'];
                  $comunidad_o = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM comunidades WHERE id_comunidad=$id_comunidad_o"));
                  
                  $cadena_de_texto = $orden['tecnicos_r'];
                  $cadena_buscada   = $user;
                  $posicion_coincidencia = strrpos($cadena_de_texto, $cadena_buscada);
                   
                  //se puede hacer la comparacion con 'false' o 'true' y los comparadores '===' o '!=='
                  if ($posicion_coincidencia === false) {
                      $tecnicos = $orden['tecnicos_s'];
                      $fecha = $orden['fecha_s'];
                      $hora = $orden['hora_s'];
                  } else {
                      $tecnicos = $orden['tecnicos_r'];
                      $fecha = $orden['fecha_r'];
                      $hora = $orden['hora_r'];
                  }
                  ?>
                  <tr>
                    <td><?php echo $id_cliente_o; ?></td>
                    <td><?php echo $cliente_o['nombre']; ?></td>            
                    <td><b>Orden de Servicio</b></td>            
                    <td><?php echo $comunidad_o['nombre']; ?></td>            
                    <td><?php echo $orden['fecha']; ?></td>
                    <td><?php echo $orden['hora']; ?></td>
                    <td><?php echo $fecha ?></td>
                    <td><?php echo $hora ?></td>
                    <td><?php echo $orden['solicitud']; ?></td>
                    <td><?php echo $orden['trabajo'] ?></td>
                    <td><?php echo $tecnicos ?></td>
                    <td>Campo</td>
                  </tr>
              <?php
                }
              }
            }
          }
          }else{
              $sql_orden1 = mysqli_query($conn,"SELECT * FROM orden_servicios WHERE (fecha_r = '$DIA' AND  hora_r > '$hora_alta' AND tecnicos_r LIKE '%$user%') OR (fecha_s = '$DIA' AND  hora_s > '$hora_alta' AND tecnicos_s LIKE '%$user%')");
                if(mysqli_num_rows($sql_orden1) > 0){ 
                #IMPRIMIR ORDEN --- --- 12 ++++++++++++++++++++++++++++++++++++++++++++++++
                while($orden = mysqli_fetch_array($sql_orden1)){
                  $id_cliente_o = $orden['id_cliente'];
                  $cliente_o = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM especiales WHERE id_cliente=$id_cliente_o"));
                  $id_comunidad_o = $cliente_o['lugar'];
                  $comunidad_o = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM comunidades WHERE id_comunidad=$id_comunidad_o"));
                  
                  $cadena_de_texto = $orden['tecnicos_r'];
                  $cadena_buscada   = $user;
                  $posicion_coincidencia = strrpos($cadena_de_texto, $cadena_buscada);
                   
                  //se puede hacer la comparacion con 'false' o 'true' y los comparadores '===' o '!=='
                  if ($posicion_coincidencia === false) {
                      $tecnicos = $orden['tecnicos_s'];
                      $fecha = $orden['fecha_s'];
                      $hora = $orden['hora_s'];
                  } else {
                      $tecnicos = $orden['tecnicos_r'];
                      $fecha = $orden['fecha_r'];
                      $hora = $orden['hora_r'];
                  }
                  ?>
                  <tr>
                    <td><?php echo $id_cliente_o; ?></td>
                    <td><?php echo $cliente_o['nombre']; ?></td>            
                    <td><b>Orden de Servicio</b></td>            
                    <td><?php echo $comunidad_o['nombre']; ?></td>            
                    <td><?php echo $orden['fecha']; ?></td>
                    <td><?php echo $orden['hora']; ?></td>
                    <td><?php echo $fecha ?></td>
                    <td><?php echo $hora ?></td>
                    <td><?php echo $orden['solicitud']; ?></td>
                    <td><?php echo $orden['trabajo'] ?></td>
                    <td><?php echo $tecnicos ?></td>
                    <td>Campo</td>
                  </tr>
              <?php
                }
              }
          }
        }
      }
      }else{
        #SI NO HAY INSTALACIONES BUSCAR REPORTES
        $sql_Sin_I = mysqli_query($conn, "SELECT * FROM reportes WHERE  (fecha_solucion = '$DIA'  AND atendido = 1  AND (tecnico = '$id_user' OR apoyo = '$id_user')) OR (fecha_d = '$DIA' AND tecnico_d = '$id_user') ORDER BY hora_atendido");
        $cont_r2 = mysqli_num_rows($sql_Sin_I);
        if($cont_r2> 0){ 
          $iniciar_orden = 0;
          while ($info = mysqli_fetch_array($sql_Sin_I)) {
            $cont_r2 --;
            #HORA DE REPORTE Y SQL ORDEN E IF SI HAY 
            $hora_reporte_mayor = $info['hora_atendido'];
            $sql_orden1 = mysqli_query($conn,"SELECT * FROM orden_servicios WHERE (fecha_r = '$DIA' AND  hora_r < '$hora_reporte_mayor' AND tecnicos_r LIKE '%$user%') OR (fecha_s = '$DIA' AND  hora_s < '$hora_reporte_mayor' AND tecnicos_s LIKE '%$user%' ) LIMIT $iniciar_orden, 20");
            if(mysqli_num_rows($sql_orden1) > 0){ 
              #IMPRIMIR ORDEN --- --- 8 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
              $iniciar_orden = $iniciar_orden+mysqli_num_rows($sql_orden1);
              while($orden = mysqli_fetch_array($sql_orden1)){
                $id_cliente_o = $orden['id_cliente'];
                $cliente_o = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM especiales WHERE id_cliente=$id_cliente_o"));
                $id_comunidad_o = $cliente_o['lugar'];
                $comunidad_o = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM comunidades WHERE id_comunidad=$id_comunidad_o"));
                
                $cadena_de_texto = $orden['tecnicos_r'];
                $cadena_buscada   = $user;
                $posicion_coincidencia = strrpos($cadena_de_texto, $cadena_buscada);
                   
                  //se puede hacer la comparacion con 'false' o 'true' y los comparadores '===' o '!=='
                  if ($posicion_coincidencia === false){
                      $tecnicos = $orden['tecnicos_s'];
                      $fecha = $orden['fecha_s'];
                      $hora = $orden['hora_s'];
                  } else {
                      $tecnicos = $orden['tecnicos_r'];
                      $fecha = $orden['fecha_r'];
                      $hora = $orden['hora_r'];
                  }
                  ?>
                  <tr>
                    <td><?php echo $id_cliente_o; ?></td>
                    <td><?php echo $cliente_o['nombre']; ?></td>            
                    <td><b>Orden de Servicio</b></td>            
                    <td><?php echo $comunidad_o['nombre']; ?></td>            
                    <td><?php echo $orden['fecha']; ?></td>
                    <td><?php echo $orden['hora']; ?></td>
                    <td><?php echo $fecha ?></td>
                    <td><?php echo $hora ?></td>
                    <td><?php echo $orden['solicitud']; ?></td>
                    <td><?php echo $orden['trabajo'] ?></td>
                    <td><?php echo $tecnicos ?></td>
                    <td>Campo</td>
                  </tr>
            <?php
              }
            }
            #IMPRIMIR REPORTES ---- 9 **************************************************
            $id_cliente = $info['id_cliente'];            
            $sql2 = mysqli_query($conn, "SELECT * FROM clientes WHERE id_cliente=$id_cliente");
            if (mysqli_num_rows($sql2) == 0) {
              $sql2 = mysqli_query($conn, "SELECT * FROM especiales WHERE id_cliente=$id_cliente");
            }
            $cliente = mysqli_fetch_array($sql2);
            if ($info['descripcion'] == 'Actividad') {
              $id_actividad =$info['id_reporte'];
              $sql_lugar = mysqli_query($conn, "SELECT * FROM lugar_actividades WHERE id_actividad=$id_actividad");
              if (mysqli_num_rows($sql_lugar)) {
                $lugar = mysqli_fetch_array($sql_lugar);
                $id_comunidad = $lugar['lugar'];
              }else{
                $id_comunidad = $cliente['lugar']; 
              }
            }else{
              $id_comunidad = $cliente['lugar'];
            }
            $comunidad = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM comunidades WHERE id_comunidad=$id_comunidad"));
            #VEMOS SI ES SOLO UN DIAGNOSTICO DE REPORTE
            if ($info['fecha_d']==$DIA AND $info['tecnico_d'] == $id_user) {
              $id_d =$info['tecnico_d'];
              if ($id_d != '') {
                $tecnico_d = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM users WHERE user_id = $id_d"));
              }
              ?>
              <tr>
                <td><?php echo $id_cliente; ?></td>            
                <td><?php echo $cliente['nombre']; ?></td>            
                <td><b>Reporte</b></td>            
                <td><?php echo $comunidad['nombre']; ?></td>            
                <td><?php echo $info['fecha']; ?></td>
                <td><?php echo $info['hora_registro']; ?></td>
                <td><?php echo $info['fecha_d']; ?></td>
                <td><?php echo $info['hora_d']; ?></td>
                <td><b>Reporte: </b> <?php echo $info['descripcion']; ?>.<br><b>Diagnostico: </b><?php echo $info['falla']; ?>.</td>
                <td><b>Es Un Diagnostico: </b>Ser reviso en oficina y se envio a campo</td>
                <td><?php echo $tecnico_d['firstname']; ?></td>
                <td><?php echo "Oficina"; ?></td>
              </tr>
              <?php
            }
            #VEMOS SI ES UN TERMINO DE REPORTE (SOLUCION)
            if ($info['fecha_solucion']==$DIA AND $info['atendido'] == 1 AND ($info['tecnico'] == $id_user OR $info['apoyo'] == $id_user)) {
              $id_tec = $info['tecnico'];
              $tecnico = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM users WHERE user_id = $id_tec"));
              if ($info['apoyo'] != 0) {
                $id_apoyo = $info['apoyo'];
                $A = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM users WHERE user_id = $id_apoyo"));
                $Apoyo = ', Apoyo: '.$A['firstname'];
              }else{ $Apoyo = ''; }
              ?>
              <tr>
                <td><?php echo $id_cliente; ?></td>            
                <td><?php echo $cliente['nombre']; ?></td>            
                <td><b>Reporte</b></td>            
                <td><?php echo $comunidad['nombre']; ?></td>            
                <td><?php echo $info['fecha']; ?></td>
                <td><?php echo $info['hora_registro']; ?></td>
                <td><?php echo $info['fecha_solucion']; ?></td>
                <td><?php echo $info['hora_atendido']; ?></td>
                <td><b>Reporte: </b> <?php echo $info['descripcion']; ?>.<br><b>Diagnostico: </b><?php echo $info['falla']; ?>.</td>
                <td><?php echo $info['solucion']; ?></td>
                <td><?php echo $tecnico['firstname'].$Apoyo; ?></td>
                <td><?php echo ($info['campo'] == 1 ) ? "Campo":"Oficina"; ?></td>
              </tr>
              <?php
            }
            #SQL ORDEN COMPARANDO CON HORA DE REPORTE Y EL CONTADOR DE REPORTES EN CERO 
            if ($cont_r2 == 0) {
              $sql_orden1 = mysqli_query($conn,"SELECT * FROM orden_servicios WHERE (fecha_r = '$DIA' AND  hora_r > '$hora_reporte_mayor' AND tecnicos_r LIKE '%$user%') OR (fecha_s = '$DIA' AND  hora_s > '$hora_reporte_mayor' AND tecnicos_s LIKE '%$user%')");
                if(mysqli_num_rows($sql_orden1) > 0){ 
                #IMPRIMIR ORDEN --- --- 10  ++++++++++++++++++++++++++++++++++++++++++
                while($orden = mysqli_fetch_array($sql_orden1)){
                  $id_cliente_o = $orden['id_cliente'];
                  $cliente_o = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM especiales WHERE id_cliente=$id_cliente_o"));
                  $id_comunidad_o = $cliente_o['lugar'];
                  $comunidad_o = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM comunidades WHERE id_comunidad=$id_comunidad_o"));

                  $cadena_de_texto = $orden['tecnicos_r'];
                  $cadena_buscada   = $user;
                  $posicion_coincidencia = strrpos($cadena_de_texto, $cadena_buscada);
                   
                  //se puede hacer la comparacion con 'false' o 'true' y los comparadores '===' o '!=='
                  if ($posicion_coincidencia === false) {
                      $tecnicos = $orden['tecnicos_s'];
                      $fecha = $orden['fecha_s'];
                      $hora = $orden['hora_s'];
                  } else {
                      $tecnicos = $orden['tecnicos_r'];
                      $fecha = $orden['fecha_r'];
                      $hora = $orden['hora_r'];
                  }
                  ?>
                  <tr>
                    <td><?php echo $id_cliente_o; ?></td>
                    <td><?php echo $cliente_o['nombre']; ?></td>            
                    <td><b>Orden de Servicio</b></td>            
                    <td><?php echo $comunidad_o['nombre']; ?></td>            
                    <td><?php echo $orden['fecha']; ?></td>
                    <td><?php echo $orden['hora']; ?></td>
                    <td><?php echo $fecha ?></td>
                    <td><?php echo $hora ?></td>
                    <td><?php echo $orden['solicitud']; ?></td>
                    <td><?php echo $orden['trabajo'] ?></td>
                    <td><?php echo $tecnicos ?></td>
                    <td>Campo</td>
                  </tr>
              <?php
                }
              }
            }
          }
        }
      }
      $nuevafecha = strtotime('+1 day', strtotime($DIA));
      $DIA = date('Y-m-d', $nuevafecha);
      }
      ?>
    </tbody>
</table>
<?php
    #CHECAMOS SI HUBO COTEJOS DE TELEFONO
    $Cotejos = mysqli_query($conn, "SELECT * FROM pagos INNER JOIN fecha_cotejo ON pagos.id_pago = fecha_cotejo.id_pago WHERE pagos.Cotejado = 2 AND fecha_cotejo.fecha >= '$ValorDe' AND fecha_cotejo.fecha <= '$ValorA' AND fecha_cotejo.usuario = '$id_user' ORDER BY fecha_cotejo.fecha, fecha_cotejo.hora");
    if(mysqli_num_rows($Cotejos) > 0){
      ?>
      <h5>Cotejos: </h5>
      <table class="bordered highlight responsive-table">
        <thead>
          <tr>
            <th>#</th>
            <th>Nombre</th>
            <th>Tipo</th>
            <th>Fecha</th>
            <th>Hora</th>
            <th>Descripcion</th>
            <th>Registo</th>
            <th>Zona</th>
          </tr>
        </thead>
        <tbody>
          <?php
          while ($info = mysqli_fetch_array($Cotejos)) {
          $id_cliente = $info['id_cliente'];
          $usuario = $info['usuario'];
          $sql2 = mysqli_query($conn, "SELECT * FROM clientes WHERE id_cliente=$id_cliente");
          $cliente = mysqli_fetch_array($sql2);
          $user = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM users WHERE user_id=$usuario"));
          ?>
          <tr>
            <td><?php echo $info['id_cliente']; ?></td>
            <td><?php echo $cliente['nombre']; ?></td>            
            <td><b>Cotejo Telefono</b></td>                       
            <td><?php echo $info['fecha']; ?></td>
            <td><?php echo $info['hora']; ?></td>
            <td><?php echo $info['tipo']; ?> (<?php echo $info['descripcion']; ?>)</td>
            <td><?php echo $user['firstname']; ?></td>
            <td>Oficina</td>
          </tr>
        <?php
        }
        ?> 
        </tbody>
      </table>
      <?php
    }
}
mysqli_close($conn);
?>