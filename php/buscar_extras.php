<?php
include('../php/conexion.php');
$ValorDe = $conn->real_escape_string($_POST['valorDe']);
$ValorA = $conn->real_escape_string($_POST['valorA']);
?>
<br><br>
<table class="bordered highlight responsive-table">
    <thead>
      <tr>
        <th>Ingeniero</th>
        <th>Lunes</th>
        <th>Martes</th>
        <th>Miercoles</th>
        <th>Jueves</th>
        <th>Viernes</th>
        <th>Sabado</th>
        <th>Domingo</th>
        <th>Total</th>
      </tr>
    </thead>
    <tbody>
      <?php
      $Filas = '';// VARIABLE QUE ALMACENA LA FILAS DE LA TABLA
      $sql_tmp = mysqli_query($conn, "SELECT * FROM users");
      #RECORREMOS TODOS LOS USUARIOS DEL SISTEMA
      while($user = mysqli_fetch_array($sql_tmp)){
        $id = $user['user_id'];
        $sql_extras = mysqli_query($conn, "SELECT * FROM horas_extras WHERE fecha >= '$ValorDe' AND fecha <= '$ValorA' AND (usuario = $id OR apoyo = $id)");
        if (mysqli_num_rows($sql_extras)) {
          //CREAMOS UNA FILA CON LAS EXTRAS DEL  INGENIERO
          $Filas .= '<tr><td><b>'.$user['firstname'].'</b></td>';// PRIMER DATO EL NOMBRE
          $dias = array(0,0,0,0,0,0,0);// CREAMOS UN ARRAY EN EL CUAL SE ASIGNARAN POR DIA LAS HORAS SEGUN SU ORDEN
          #SUMAMOS EL TIEMPO DE LAS HORAS EXTRAS PARA UN TOTAL DESDE EL SELECT
          $total = mysqli_fetch_array(mysqli_query($conn, "SELECT SEC_TO_TIME(SUM(TIME_TO_SEC(tiempo))) suma FROM horas_extras WHERE fecha >= '$ValorDe' AND fecha <= '$ValorA' AND (usuario = $id OR apoyo = $id)"));
          #RECORREMOS CADA REGISTRO DE HORA EXTRA PARA SABER A QUE DIA PERTENECE
          while($extra = mysqli_fetch_array($sql_extras)){
            $Fecha_saber = $extra['fecha'];
            #$dias = array('Lunes','Martes','Miercoles','Jueves','Viernes','Sabado','Domingo');
            $Dia_num = date('N', strtotime($Fecha_saber));//Domingo(7), Lunes(1), Martes(2), Miercoles(3), Jueves(4) ,Viernes(4) ,Sabado(6)
            for ($i=0; $i < 7; $i++) { 
              #$Nombre_dia = $dias[$i];
              if ($Dia_num == ($i+1)) {
                $dias[$i] = $extra['tiempo'];
              }
            }
          }
          #VACIAMOS EL ARREGLO EN CADA CELDA PARA CERAR LA FILA
          $Filas .= '<td>'.$dias[0].'</td>
                    <td>'.$dias[1].'</td>
                    <td>'.$dias[2].'</td>
                    <td>'.$dias[3].'</td>
                    <td>'.$dias[4].'</td>
                    <td>'.$dias[5].'</td>
                    <td>'.$dias[6].'</td>
                    <td>'.$total['suma'].'</td></tr>';
        }
      }
      echo $Filas;
      mysqli_close($conn);
      ?>        
    </tbody>
</table><br><br><br>