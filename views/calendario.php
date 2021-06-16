<!DOCTYPE html>
<html lang="en">
<head>
<?php
  date_default_timezone_set('America/Mexico_City');
  include('fredyNav.php');
  $current_day = date("N");//NUMERO DEL DIA DEL 1 AL 7 DE LUNES A DOMINGO RESPECTIVAMENTE
  $days_from_lunes = $current_day - 1;//DIAS QUE HAN PASADOS DESDE EL LUNES PUDEN PSAR DE 0 A 6 DIAS HASTA EL DOMINGO
  $days_to_domingo = 7 - $current_day;//DIAS QUE FALTAN PARA LLEGAR AL DOMINGO PUEDEN FALTAR de 6 A 0 DIAS
  $lunes = date("Y-m-d", strtotime("- {$days_from_lunes} Days"));// A LA FECHA DE HOY LE RESTAMOS LOS DIAS QUE PASARON DESDE EL NUNES DE 0 A 6 DIAS
  $sql_fecha = mysqli_query($conn,"SELECT * FROM actividades_calendario WHERE semana = '$lunes'");
  #VERIFICAMOS SI SE ENCUENTRA LA FECHA DE LA SEMANA REGISTRADA EN ALMENOS ALGUNA ACTIVIDAD
  if (mysqli_num_rows($sql_fecha)>0) {
    #SI SE ENCUENTRA LA FECHA SIGNIFICA QUE ESA SEMANA YA SE DIO POR TERMINADA PONER LA FECHA MAS DELANTE UNA SEMANA
    $days_from_lunes = 8 - $current_day;//ADELANTAMOS LA FECHA POR UNA SEMANA
    $days_to_domingo = $days_from_lunes+6;//SOLO SE LE SUMAN LOS & DIAS QUE PSAN DESDE EL LUNES PARA LLEGAR AL DOMINGO
    echo $days_from_lunes;
    $lunes = date("Y-m-d", strtotime("+ {$days_from_lunes} Days"));// A LA FECHA DE HOY LE RESTAMOS LOS DIAS QUE PASARON DESDE EL NUNES DE 0 A 6 DIAS
  }
  $domingo = date("Y-m-d", strtotime("+ {$days_to_domingo} Days"));// A ÑA FECHA DE HOY LE SUMAMOS LOS DIAS QUE FALTAN PARA DOMINGO  DE 6 A 0 DIAS
?>
<title>SIC | Calendario Semanal</title>
<script>
  function terminar(){
    $.post("../php/terminar_calendario.php", {
      valorBoton: 'Si'  
    }, function(mensaje) {    
    $("#resultado_actividad").html(mensaje);
    }); 
  };
  function borrar(Id){
    $.post("../php/borrar_actividad.php", { 
      valorId: Id,
    }, function(mensaje) {
    $("#resultado_actividad").html(mensaje);
    }); 
  };
  function insert_actividad() {
    var textoDia = $("select#dia").val();
    var textoActividad = $("input#actividad").val();
    var textoIng = $("select#ing").val();
    var textoApoyo = $("select#apoyo").val();
  
    if(textoDia == 0){
      M.toast({html :"Seleccione una Dia...", classes: "rounded"});
    }else if(textoActividad == ""){
      M.toast({html :"Ingrese una Actividad...", classes: "rounded"});
    }else if(textoIng == ""){
      M.toast({html :"Seleccione un ingeniero...", classes: "rounded"});
    }else{
      $.post("../php/insert_actividad.php", {
          valorDia: textoDia,
          valorActividad: textoActividad,
          valorIng: textoIng,
          valorApoyo: textoApoyo
      }, function(mensaje) {
            $("#resultado_actividad").html(mensaje);
      }); 
    }
  };
</script>
</head>
<main>
<body>
  <div class="container">
  <?php 
  $btn = 'disable';
  if($area['area']=="Administrador" and ($area['user_id'] == 10 OR $area['user_id']==49  OR $area['user_id']==28 OR $area['user_id']==25)){
    $btn = '';
  ?>
    <div class="row" >
      <h3 class="hide-on-med-and-down">Registrar Actividad</h3>
      <h5 class="hide-on-large-only">Registrar Actividad</h5>
    </div>
    <div class="row"><br>
      <div class="input-field col s12 m3 l3">
        <select id="dia" class="browser-default">
          <option value="0" selected>Seleccione una dia</option>
          <option value="LUNES">LUNES</option>
          <option value="MARTES">MARTES</option>
          <option value="MIERCOLES">MIERCOLES</option>
          <option value="JUEVES">JUEVES</option>
          <option value="VIERNES">VIERNES</option>
          <option value="SABADO">SABADO</option>
          <option value="DOMINGO">DOMINGO</option>
        </select>
      </div>
      <div class="input-field col s6 m2 l2">
        <select id="ing" class="browser-default">
          <option value="0" selected>Ingeniero:</option>
          <?php
          $sql_users = mysqli_query($conn, "SELECT * FROM users WHERE area = 'REDES' OR user_id in (25,49,28)");
          while($user= mysqli_fetch_array($sql_users)){
            ?>
            <option value="<?php echo $user['user_id'];?>"><?php echo $user['firstname'];?></option>
            <?php
          }
          ?>
        </select>
      </div>
      <div class="input-field col s6 m2 l2">
        <select id="apoyo" class="browser-default">
          <option value="0" selected>Apoyo:</option>
          <?php
          $sql_users2 = mysqli_query($conn, "SELECT * FROM users WHERE area = 'REDES' OR user_id in (25,49,28)");
          while($user2= mysqli_fetch_array($sql_users2)){
            ?>
            <option value="<?php echo $user2['user_id'];?>"><?php echo $user2['firstname'];?></option>
            <?php
          }
          ?>
        </select>
      </div>
      <div class="input-field col s12 m5 l5">
         <i class="material-icons prefix">edit</i>
        <input type="text" id="actividad">
        <label for="actividad">Actividad(ej: Ruta JIMENEZ-CHALCHIHUITES):</label>
      </div>
      <div class="input-field">
        <a onclick="insert_actividad();" class="waves-effect waves-light btn pink left right"><i class="material-icons center">send</i></a>
      </div>
    </div>
    <?php } ?>
    <div class="row">
      <div class="row"><br>
        <h3 class="hide-on-med-and-down col s12 m12 l12">Calendario Semanal Del <?php echo "$lunes Al $domingo"; ?></h3>
        <h5 class="hide-on-large-only col s12 m12 l12">Calendario SemanalDel <?php echo "$lunes Al $domingo"; ?></h5>
      </div>
      <div class="row" id="resultado_actividad">
        <?php include ('../php/tabla_A.php'); ?>
      </div>
      <a class="waves-effect waves-light btn pink right <?php echo $btn;?>" onclick="terminar();">Terminar Semana<i class="material-icons right">send</i></a><br><br>
    </div>
  </div>
</body>
</main>
</html>