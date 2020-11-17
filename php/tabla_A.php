      <table class="bordered highlight responsive-table">
        <thead>
          <tr>            
            <th>Ing.</th>
            <th class="blue-text">LUNES</th>
            <th class="blue-text">MARTES</th>
            <th class="blue-text">MIERCOLES</th>
            <th class="blue-text">JUEVES</th>
            <th class="blue-text">VIERNES</th>
            <th class="blue-text">SABADO</th>
            <th class="blue-text">DOMINGO</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $body = '';//CREAMOS EL BODY(CUERPO) DE LA TABLA VACIO
          #SELECCIONAMOS TODOS LOS USUARIOS QUE PUEDEN REALIZAR UNA ACTIVIDAD EN EL CALENDARIO
          $sql_users3 = mysqli_query($conn, "SELECT * FROM users WHERE area = 'REDES' OR user_id in (25,49,28)");
          #RECORREMOS UNO POR UNO LOS SUARIOS(INGENIEROS)
          while($user3= mysqli_fetch_array($sql_users3)){
            $id_user = $user3['user_id'];//TOMAMOS EL ID DEL USUARIO EN TURNO
            #SELECIONAMOS SI TIENE ACTIVIDADES REGISTRADAS SIN FECHA EN LA SEMANA
            $sql_actividades = mysqli_query($conn, "SELECT * FROM actividades_calendario WHERE (tecnico = $id_user OR apoyo = $id_user) AND  semana = '0000-00-00'");
            #VERIFICAMOS SI HAY ACTIVIDADES DEL USUARIO EN TURNO
            #INICIAMOS EN VACIO QUE ACTIVIDAD O ACTIVIDADES HAY EN CADA DIA POR USUARIO
            $Lunes = ''; $Martes = ''; $Miercoles = ''; $Jueves = ''; $Viernes = ''; $Sabado = ''; $Domingo = '';
            if (mysqli_num_rows($sql_actividades)>0) {
              $body.='<tr><td class="blue-text"><b>'.$user3['firstname'].'</b></td>';//INICIAMOS UN RENGLO EN LA TABLA POR USUARIO
              #RECORREMOS LAS ACTIVIDADES UNA POR UNA PARA SABE A QUE DIA PERTENECEN
              while ($actividad = mysqli_fetch_array($sql_actividades)) {
                $Es=($actividad['tecnico'] == $id_user)?'':'Apoyo: ';  //DEFINIMOS SI ES DE APOYO  PORQUE SE REPITE EL TRABAJO            
                #SEPARAMOS LAS ACTIVIDADES POR DIA
                $Borra = ' <a onclick="borrar('.$actividad['id'].')" class="btn-floating btn-small waves-effect waves-light red"><i class="tiny material-icons">delete</i></a>';
                if($actividad['dia'] == 'LUNES'){
                  $Lunes.= $Es.$actividad['actividad'].$Borra.'<br>';
                }else if($actividad['dia'] == 'MARTES'){
                  $Martes.= $Es.$actividad['actividad'].$Borra.'<br>';
                }else if($actividad['dia'] == 'MIERCOLES'){
                  $Miercoles.= $Es.$actividad['actividad'].$Borra.'<br>';
                }else if($actividad['dia'] == 'JUEVES'){
                  $Jueves.= $Es.$actividad['actividad'].$Borra.'<br>';
                }else if($actividad['dia'] == 'VIERNES'){
                  $Viernes.= $Es.$actividad['actividad'].$Borra.'<br>';
                }else if($actividad['dia'] == 'SABADO'){
                  $Sabado.= $Es.$actividad['actividad'].$Borra.'<br>';
                }else if($actividad['dia'] == 'DOMINGO'){
                  $Domingo.= $Es.$actividad['actividad'].$Borra.'<br>';
                }
              }
              #RELLENAMOS EL BODY CON LAS ACTIVIDADES DE CADA DIA DE UN USUARIO
              $body .= "<td>$Lunes</td><td>$Martes</td><td>$Miercoles</td><td>$Jueves</td><td>$Viernes</td><td>$Sabado</td><td>$Domingo</td>";
            }
            $body .= '</tr>';//CERRAMOS EL BODY(cuerpo) DE LA TABLA            
          }
          #UNA VEZ LLENADO EL BODY LO IMPRIMIMOS
          echo $body;
          ?>
        </tbody>
      </table><br>
