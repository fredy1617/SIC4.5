<?php
include('../php/conexion.php');
$ValorDe = $conn->real_escape_string($_POST['valorDe']);
$ValorA = $conn->real_escape_string($_POST['valorA']);

$DIA  = $ValorDe;
$User = $conn->real_escape_string($_POST['valorUsuario']);

if ($User ==  0) {
  $usuarios = mysqli_query($conn, "SELECT * FROM users WHERE area='Redes' OR user_id = 49 OR user_id = 28 OR user_id = 25");
}else {
  $usuarios = mysqli_query($conn, "SELECT * FROM users WHERE user_id = '$User' ");
}

while($usuario = mysqli_fetch_array($usuarios)){
  $user=$usuario['user_name'];
  $id_user = $usuario['user_id'];
  $instalaciones = mysqli_fetch_array(mysqli_query($conn,"SELECT count(*) FROM clientes WHERE  fecha_instalacion >= '$ValorDe' AND fecha_instalacion <= '$ValorA' AND  tecnico LIKE '%$user%'"));
  $Reportes_Oficina = mysqli_fetch_array(mysqli_query($conn,"SELECT count(*) FROM reportes WHERE (fecha_solucion >= '$ValorDe' AND fecha_solucion <= '$ValorA'  AND ((campo = 0 AND atendido = 1) OR (atendido = 2 AND campo = 1)) AND (tecnico = '$id_user' OR apoyo = '$id_user'))"));
  $Reportes_Campo = mysqli_fetch_array(mysqli_query($conn,"SELECT count(*) FROM reportes WHERE (fecha_solucion >= '$ValorDe' AND fecha_solucion <= '$ValorA'  AND (campo = 1 AND atendido = 1) AND (tecnico = '$id_user' OR apoyo = '$id_user'))"));

?>
<br><br>
<h3 class="center">TECNICO: <?php echo $usuario['firstname']; ?></h3>
<h4>Trabajo: </h4>
<h5 class="indigo-text">Instalaciones (<?php echo $instalaciones['count(*)']; ?>) <--> Reportes Ofician (<?php echo $Reportes_Oficina['count(*)']; ?>) <--> Reportes Campo (<?php echo $Reportes_Campo['count(*)']; ?>) </h5>
<table class="bordered highlight responsive-table">
    <thead>
      <tr>
        <th>#</th>
        <th>Nombre</th>
        <th>Tipo</th>
        <th>Comunidad</th>
        <th>Hora Registro</th>
        <th>Fecha</th>
        <th>Hora Termino</th>
        <th>Falla</th>
        <th>Solucion</th>
        <th>Técnicos</th>
        <th>Zona</th>
      </tr>
    </thead>
    <tbody>
    <?php
    while ($DIA <= $ValorA) {
      $resultado_instalaciones = mysqli_query($conn,"SELECT * FROM clientes WHERE fecha_instalacion = '$DIA' AND  tecnico LIKE '%$user%' ORDER BY hora_alta");
      $aux = mysqli_num_rows($resultado_instalaciones);
      if($aux > 0){
      $iniciar = 0;
      while($instalaciones = mysqli_fetch_array($resultado_instalaciones)){
        $aux --;
        $hora_alta = $instalaciones['hora_alta'];
        #BUSACAR E IMPRIMIR REPORTES DE MISMO O MENOR FECHA Y MENOR O MISMA HORA
        $sql = mysqli_query($conn, "SELECT * FROM reportes WHERE  (fecha_solucion = '$DIA'  AND (atendido = 1 OR (atendido = 2 AND campo = 1)) AND (tecnico = '$id_user' OR apoyo = '$id_user')) AND hora_atendido < '$hora_alta' ORDER BY hora_atendido LIMIT $iniciar, 100");
        if(mysqli_num_rows($sql) > 0){ 
        $iniciar = $iniciar+mysqli_num_rows($sql);
        while ($info = mysqli_fetch_array($sql)) {
          $id_cliente = $info['id_cliente'];
          $tecnico = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM users WHERE user_id = $id_user"));
          $sql2 = mysqli_query($conn, "SELECT * FROM clientes WHERE id_cliente=$id_cliente");
          if (mysqli_num_rows($sql2) == 0) {
            $sql2 = mysqli_query($conn, "SELECT * FROM especiales WHERE id_cliente=$id_cliente");
          }
          $cliente = mysqli_fetch_array($sql2);
          $id_comunidad = $cliente['lugar'];
          $comunidad = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM comunidades WHERE id_comunidad=$id_comunidad"));
          if ($info['apoyo'] != 0) {
            $id_apoyo = $info['apoyo'];
            $A = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM users WHERE user_id = $id_apoyo"));
            $Apoyo = ', Apoyo: '.$A['firstname'];
          }else{ $Apoyo = ''; }
          ?>
          <tr>
            <td><?php echo $info['id_cliente']; ?></td>
            <td><?php echo $cliente['nombre']; ?></td>            
            <td><b>Reporte</b></td>            
            <td><?php echo $comunidad['nombre']; ?></td>            
            <td><?php echo $info['hora_registro']; ?></td>
            <td><?php echo $info['fecha_solucion']; ?></td>
            <td><?php echo $info['hora_atendido']; ?></td>
            <td><?php echo $info['falla']; ?></td>
            <td><?php echo ($info['atendido'] == 2) ? "Ser reviso en oficina y se envio a campo":$info['solucion']; ?></td>
            <td><?php echo $tecnico['firstname'].$Apoyo; ?></td>
            <td><?php echo ($info['campo'] == 1 AND $info['atendido'] == 1) ? "Campo":"Oficina"; ?></td>
          </tr>
        <?php
        }
        }
        #IMPRIMIR LA INSTALACION
        $id_comunidad = $instalaciones['lugar'];        
        $comunidad = mysqli_fetch_array(mysqli_query($conn,"SELECT * FROM comunidades WHERE id_comunidad = '$id_comunidad'"));
        ?>
        <tr>
          <td><?php echo $instalaciones['id_cliente'];?></td>
          <td><?php echo $instalaciones['nombre'];?></td>
          <td><b>Instalacion</b></td>
          <td><?php echo $comunidad['nombre'];?></td>
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
          #BUSACAR E IMPRIMIR REPORTES MAYOR FECHA Y MAYOR HORA QUE LA ULTIMA INSTALACION
          $sql = mysqli_query($conn, "SELECT * FROM reportes WHERE  (fecha_solucion = '$DIA'  AND (atendido = 1 OR (atendido = 2 AND campo = 1)) AND (tecnico = '$id_user' OR apoyo = '$id_user'))  AND hora_atendido > '$hora_alta' ORDER BY hora_atendido");
          if(mysqli_num_rows($sql) > 0){ 
          while ($info = mysqli_fetch_array($sql)) {
            $id_cliente = $info['id_cliente'];

            $tecnico = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM users WHERE user_id = $id_user"));
            $sql2 = mysqli_query($conn, "SELECT * FROM clientes WHERE id_cliente=$id_cliente");
            if (mysqli_num_rows($sql2) == 0) {
              $sql2 = mysqli_query($conn, "SELECT * FROM especiales WHERE id_cliente=$id_cliente");
            }
            $cliente = mysqli_fetch_array($sql2);
            $id_comunidad = $cliente['lugar'];
            $comunidad = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM comunidades WHERE id_comunidad=$id_comunidad"));
            if ($info['apoyo'] != 0) {
              $id_apoyo = $info['apoyo'];
              $A = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM users WHERE user_id = $id_apoyo"));
              $Apoyo = ', Apoyo: '.$A['firstname'];
            }else{ $Apoyo = ''; }
            ?>
            <tr>
              <td><?php echo $info['id_cliente']; ?></td>
              <td><?php echo $cliente['nombre']; ?></td>            
              <td><b>Reporte</b></td>            
              <td><?php echo $comunidad['nombre']; ?></td>            
              <td><?php echo $info['hora_registro']; ?></td>
              <td><?php echo $info['fecha_solucion']; ?></td>
              <td><?php echo $info['hora_atendido']; ?></td>
              <td><?php echo $info['falla']; ?></td>
              <td><?php echo ($info['atendido'] == 2) ? "Ser reviso en oficina y se envio a campo":$info['solucion']; ?></td>
              <td><?php echo $tecnico['firstname'].$Apoyo; ?></td>
              <td><?php echo ($info['campo'] == 1 AND $info['atendido'] == 1) ? "Campo":"Oficina"; ?></td>
            </tr>
          <?php
          }
          }
        }
      }
      }else{
        #SI NO HAY INSTALACIONES BUSCAR REPORTES
        $sql = mysqli_query($conn, "SELECT * FROM reportes WHERE  fecha_solucion = '$DIA'  AND (atendido = 1 OR (atendido = 2 AND campo = 1)) AND (tecnico = '$id_user' OR apoyo = '$id_user') ORDER BY hora_atendido");
        if(mysqli_num_rows($sql) > 0){ 
        while ($info = mysqli_fetch_array($sql)) {
          $id_cliente = $info['id_cliente'];
          $tecnico = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM users WHERE user_id = $id_user"));
          $sql2 = mysqli_query($conn, "SELECT * FROM clientes WHERE id_cliente=$id_cliente");
          if (mysqli_num_rows($sql2) == 0) {
            $sql2 = mysqli_query($conn, "SELECT * FROM especiales WHERE id_cliente=$id_cliente");
          }
          $cliente = mysqli_fetch_array($sql2);
          $id_comunidad = $cliente['lugar'];
          $comunidad = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM comunidades WHERE id_comunidad=$id_comunidad"));
          if ($info['apoyo'] != 0) {
            $id_apoyo = $info['apoyo'];
            $A = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM users WHERE user_id = $id_apoyo"));
            $Apoyo = ', Apoyo: '.$A['firstname'];
          }else{ $Apoyo = ''; }
          ?>
          <tr>
            <td><?php echo $info['id_cliente']; ?></td>
            <td><?php echo $cliente['nombre']; ?></td>            
            <td><b>Reporte</b></td>            
            <td><?php echo $comunidad['nombre']; ?></td>            
            <td><?php echo $info['hora_registro']; ?></td>
            <td><?php echo $info['fecha_solucion']; ?></td>
            <td><?php echo $info['hora_atendido']; ?></td>
            <td><?php echo $info['falla']; ?></td>
            <td><?php echo ($info['atendido'] == 2) ? "Ser reviso en oficina y se envio a campo":$info['solucion']; ?></td>
            <td><?php echo $tecnico['firstname'].$Apoyo; ?></td>
            <td><?php echo ($info['campo'] == 1 AND $info['atendido'] == 1) ? "Campo":"Oficina"; ?></td>
          </tr>
        <?php
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
    #CHECAMOS SI HAY REPORTES EN ESTA COMUNIDAD
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