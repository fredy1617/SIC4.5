<?php
#INCLUIMOS EL ARCHIVO CON LA CONEXION A LA BASE DE DATOS
include('../php/conexion.php');
#INCLUIMOS EL PHP DONDE VIENE LA INFORMACION DEL INICIO DE SESSION
include('is_logged.php');

$IDServidor = $conn->real_escape_string($_POST['id']);
#ELIMINAMOS LOS QUEUES DE LA TABLA tmp_mikrotik PERTENECIENTES AL SERVIDOR
if(mysqli_query($conn, "DELETE FROM tmp_mikrotik WHERE servidor = '$IDServidor'")){
	#SI SON ELIMINADOS, MANDAR MSJ CON ALERTA
	echo '<script >M.toast({html:"Queues eliminados...", classes: "rounded"})</script>';
	?>
    <script>
      //REFRESCAMOS LA PAGINA PRINCIPAL PERO ENVIAMOS EL ID DEL SERVIDOR PARA PODER MOSTRAR EL BOTON COMPARAR
      id = <?php echo $IDServidor; ?>;
      var a = document.createElement("a");
        a.href = "../views/sistema_mikrotik.php?id="+id;
        a.click();
    </script>
	<?php
}else{
	echo '<script >M.toast({html:"Ocurrio un error...", classes: "rounded"})</script>';
}