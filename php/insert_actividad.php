<?php
#INCLUIMOS EL ARCHIVO EL CUAL TIENE LA INFORMACION DE CONEXION A BASE DE DATOS
include('../php/conexion.php'); 
#RECIBIMOS LOS VALORES A REGISTRAR DE LA ACTIVIDAD MEDIANTE METODO POST DEL ARCHIVO calendario.php
$Dia = $conn->real_escape_string($_POST['valorDia']);
$Actividad = $conn->real_escape_string($_POST['valorActividad']);
$Ing = $conn->real_escape_string($_POST['valorIng']);
$Apoyo = $conn->real_escape_string($_POST['valorApoyo']);
#VERIFICAMOS QUE LOS DATOS RECIBIDOS NO HAYAN SIDO YA REGISTRADOS PARA EVITAR DUPLICIDAD
if(mysqli_num_rows(mysqli_query($conn, "SELECT * FROM actividades_calendario WHERE dia = '$Dia' AND actividad = '$Actividad' AND tecnico = '$Ing' AND apoyo = '$Apoyo' AND semana = '0000-00-00'"))>0){
	#SI YA SE ENCUANTRAN ESTOS DATOS REGISTRADOIS EN LA TABLA MOSTRAR MENSAJE
    echo '<script>M.toast({html:"Ya se encuentra una actividad registrada con los mismos valores.", classes: "rounded"})</script>';
}else{
  //INSERTAMOS LA NUEVA ACTIVIDAD EN LA TABLA actividades_calendario CON LOS DATOS QUE RECIBIMOS
  if(mysqli_query($conn, "INSERT INTO actividades_calendario (dia, actividad, tecnico, apoyo) VALUES ('$Dia', '$Actividad', '$Ing', '$Apoyo')")){
  	#SI SE INSERTA LA ACTIVIDADA MOSTRAR MENSAJE
    echo '<script>M.toast({html:"Actividad creada correctamente...", classes: "rounded"})</script>';
  }else{
  	#SI NO SE ONSERTA LA ACTIVIDAD MANDAR MSJ DE ALERTA
    echo '<script>M.toast({html:"Ocurrio un error al insertar la actividad...", classes: "rounded"})</script>';
  }
}  
include ('../php/tabla_A.php');
?>
