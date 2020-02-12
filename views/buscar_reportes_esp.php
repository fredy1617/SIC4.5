<?php
  include ('../php/conexion.php');
  $Texto = $conn->real_escape_string($_POST['texto']);  

  date_default_timezone_set('America/Mexico_City');
  $Hoy = date('Y-m-d');
  $sql = "SELECT * FROM reportes  WHERE (atendido != 1 OR atendido IS NULL) AND id_cliente >= 10000 ORDER BY fecha";
  if ($Texto != "") {
    $sql = "SELECT * FROM reportes  WHERE (id_reporte = '$Texto' AND atendido IS NULL) AND id_cliente >= 10000 ORDER BY fecha";
    $clientes = mysqli_query($conn, "SELECT * FROM clientes WHERE nombre LIKE '%$Texto%' limit 1");
    if ((mysqli_num_rows($clientes)) == 1) {
      $cliente = mysqli_fetch_array($clientes);
      $id_cliente = $cliente['id_cliente'];
      $sql = "SELECT * FROM reportes  WHERE  ((id_cliente = $id_cliente AND atendido != 1) OR  (id_cliente = $id_cliente AND atendido IS NULL)) AND id_cliente >= 10000 ORDER BY fecha";
    }
  }
    
  $especiales = '';
    
    $consulta = mysqli_query($conn, $sql);
    //Obtiene la cantidad de filas que hay en la consulta
    $filas = mysqli_num_rows($consulta);
    //Si no existe ninguna fila que sea igual a $consultaBusqueda, entonces mostramos el siguiente mensaje
    if ($filas <= 0) {
      echo '<script>M.toast({html:"No se encontraron reportes.", classes: "rounded"})</script>';
    } else {
    //La variable $resultado contiene el array que se genera en la consulta, así que obtenemos los datos y los mostramos en un bucle
    while($resultados = mysqli_fetch_array($consulta)) {
      $id_reporte = $resultados['id_reporte'];
      $id_cliente = $resultados['id_cliente'];
      $id_user=$resultados['registro'];
      if ($resultados['campo'] == 1) {
        $EnCampo = 'En Campo';
      }else{
        $EnCampo = '';
      }
      if ($resultados['apoyo'] != 0) {
        $id_apoyo = $resultados['apoyo'];
        $A = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM users WHERE user_id = $id_apoyo"));
        $Apoyo = ', Apoyo: '.$A['firstname'];
      }else{
        $Apoyo = '';
      }
      if ($id_user == 0) {
        $Usuario = "Sistema";
      }else{
        $users = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM users WHERE user_id=$id_user"));
        $Usuario = $users['firstname'];
      }
      $sql = mysqli_query($conn, "SELECT * FROM clientes WHERE id_cliente=$id_cliente");
      $filas = mysqli_num_rows($sql);
      if ($filas == 0) {
        $sql = mysqli_query($conn, "SELECT * FROM especiales WHERE id_cliente=$id_cliente");
      }
      $cliente = mysqli_fetch_array($sql);
      $id_comunidad = $cliente['lugar'];
      $comunidad = mysqli_fetch_array(mysqli_query($conn, "SELECT nombre FROM comunidades WHERE id_comunidad=$id_comunidad"));
      if($resultados['tecnico']==''){
        $tecnico1[0] = '';
        $tecnico1[1] = 'Sin tecnico';
      }else{
        $id_tecnico = $resultados['tecnico'];
        $tecnico1 = mysqli_fetch_array(mysqli_query($conn, "SELECT user_id, user_name FROM users WHERE user_id=$id_tecnico"));  
      }
      $Estatus2= 0;
      if ($resultados['fecha']<$Hoy) {
        $date1 = new DateTime($Hoy);
        $date2 = new DateTime($resultados['fecha']);
        //Le restamos a la fecha date1-date2
        $diff = $date1->diff($date2);
        $Estatus2= $diff->days;
      }
      $estatus=$Estatus2;
      if ($resultados['estatus']>$Estatus2) { $estatus = $resultados['estatus']; }
      $color = "green";
      if ($estatus== 1) { $color = "yellow darken-2";
      }elseif ($estatus == 2) { $color = "orange darken-4";
      }elseif ($estatus >= 3) { $color = "red accent-4"; }
      if ($cliente['mantenimiento'] == 0) {
      
      $especiales .= '
                  <tr>
                    <td><span class="new badge '.$color.'" data-badge-caption="">'.$estatus.'</span>'.$EnCampo.'</td>
                    <td><b>'.$id_reporte.'</b></td>
                    <td><a class="tooltipped" data-position="top" data-tooltip=" Telefono: '.$cliente['telefono'].'  Comunidad: '.$comunidad['nombre'].'">'.$cliente['nombre'].'</a></td>
                    <td>'.$resultados['descripcion'].'</td>
                    <td>'.$resultados['fecha'].'</td>
                    <td>'.$comunidad['nombre'].'</td>
                    <td>'.$tecnico1[1].$Apoyo.'</td>
                    <td>'.$Usuario.'</td>
                    <td><br><form action="atender_reporte.php" method="post"><input type="hidden" name="id_reporte" value="'.$id_reporte.'"><button type="submit" class="btn-floating btn-tiny waves-effect waves-light pink"><i class="material-icons">send</i></button></form></td>
                    <td><a onclick="ruta('.$id_reporte.');" class="btn btn-floating pink waves-effect waves-light"><i class="material-icons">add</i></a></td>
                    <td><br><form action="editar_reporte.php" method="post"><input type="hidden" name="id_reporte" value="'.$id_reporte.'"><button type="submit" class="btn-floating btn-tiny waves-effect waves-light pink"><i class="material-icons">edit</i></button></form></td>
                  </tr>';
      }
         
    }//Fin while $resultados
  } //Fin else $filas

echo $especiales;
mysqli_close($conn);
    ?>
              