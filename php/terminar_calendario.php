
<script>
	function irCa(){
		var a = document.createElement("a");
		a.href = "../views/calendario.php";
		a.click();
	}   
</script><?php
include('../php/is_logged.php');
include('../php/conexion.php');
include('../php/superAdmin.php');
date_default_timezone_set('America/Mexico_City');
$current_day = date("N");//NUMERO DEL DIA DEL 1 AL 7 DE LUNES A DOMINGO RESPECTIVAMENTE
$days_from_lunes = $current_day - 1;//DIAS QUE HAN PASADOS DESDE EL LUNES PUDEN PSAR DE 0 A 6 DIAS HASTA EL DOMINGO
$lunes = date("Y-m-d", strtotime("- {$days_from_lunes} Days"));// A LA FECHA DE HOY LE RESTAMOS LOS DIAS QUE PASARON DESDE EL NUNES DE 0 A 6 DIAS
#VERIFICAMOS SI EL DIA QUE SE EJECUTA EL DOCUMENTO ES DOMINGO
if ($current_day == 7) {
	#SI ES DOMINGO EJECUTA AUTOMATICAMENTE ESTE CODIGO:
	#BUSCAMOS SI LA FECHA DE INICIO DE SEMANA($Lunes) EN CURSO YA ESTA REGISTRADA
	$sql_fecha = mysqli_query($conn,"SELECT * FROM actividades_calendario WHERE semana = '$lunes'");
	#VERIFICAMOS SI SE ENCUENTRA LA FECHA DE LA SEMANA REGISTRADA EN ALMENOS ALGUNA ACTIVIDAD
	if (mysqli_num_rows($sql_fecha)>0) {
		#SI SE ENCUENTRA LA FECHA SIGNIFICA QUE ESA SEMANA YA SE DIO POR TERMINADO MOSTRAR ALARMA
   		echo '<script >M.toast({html:"Ya se encuentra la fecha de la semana registrada ya se termino...", classes: "rounded"})</script>';
		include ('../php/tabla_A.php');
	}else{
		#SI NO SE ENCEUNTRA LA FECHA PROCEDEMOS A REGISTRAR LA FECHA DE LA SEMANA EN TODAS LAS ACTIVIDADES QUE TENGAN FECHA EN 000-00-00
		if (mysqli_query($conn,"UPDATE actividades_calendario SET semana = '$lunes' WHERE semana = '0000-00-00'")) {
			#SI LAS ACTIVIDADES SE ACTUALIZAN MOSTRAR MENSAJE
   		 	echo '<script >M.toast({html:"Actividades actualizadas y semana terminada...", classes: "rounded"})</script>';
	   		echo '<script >irCa()</script>';
		}
	}
}else{
	#SI NO ES DOMINGO PREGUNTAR SI SE EJECUTO DESDE EL BOTON
	if (isset($_POST['valorBoton']) == true) {
		#BUSCAMOS SI LA FECHA DE INICIO DE SEMANA($Lunes) EN CURSO YA ESTA REGISTRADA
		$sql_fecha = mysqli_query($conn,"SELECT * FROM actividades_calendario WHERE semana = '$lunes'");
		#VERIFICAMOS SI SE ENCUENTRA LA FECHA DE LA SEMANA REGISTRADA EN ALMENOS ALGUNA ACTIVIDAD
		if (mysqli_num_rows($sql_fecha)>0) {
			#SI SE ENCUENTRA LA FECHA SIGNIFICA QUE ESA SEMANA YA SE DIO POR TERMINADO MOSTRAR ALARMA
	   		echo '<script >M.toast({html:"Ya se encuentra la fecha de la semana registrada ya se termino...", classes: "rounded"})</script>';
			include ('../php/tabla_A.php');
		}else{
			#SI NO SE ENCEUNTRA LA FECHA PROCEDEMOS A REGISTRAR LA FECHA DE LA SEMANA EN TODAS LAS ACTIVIDADES QUE TENGAN FECHA EN 000-00-00
			if (mysqli_query($conn,"UPDATE actividades_calendario SET semana = '$lunes' WHERE semana = '0000-00-00'")) {
				#SI LAS ACTIVIDADES SE ACTUALIZAN MOSTRAR MENSAJE
	   		 	echo '<script >M.toast({html:"Actividades actualizadas y semana terminada...", classes: "rounded"})</script>';
	   		 	echo '<script >irCa()</script>';
			}
		}
	}
}
?>
