<?php
include '../php/conexion.php';
$id = $_POST["id"]; 
function generarRandomString($length) { 
  return substr(str_shuffle("0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length); 
}
$key = generarRandomString(3);
//CREAR EL NOMBRE DEL ARCHIVO
$name_file = $id."-".$key;
//-------------Vemos si recibe un archivo de documento ------
if (is_uploaded_file($_FILES['documento']['tmp_name'])) {
    $nombrearchivo= trim ($_FILES['documento']['name']); //Eliminamos los espacios en blanco
    $nombrearchivo= str_replace (" ", "", $nombrearchivo);//Sustituye una expresión regular
    $upload= '../files/cotizaciones/'.$nombrearchivo;  

    $name_documento = $name_file.'_COTIZACION.pdf';

    //--- AQUI COPIAMOS EL ARCHIVO A LA CARPETA ---
    if(move_uploaded_file($_FILES['documento']['tmp_name'], "$upload")) {
       mysqli_query($conn, "UPDATE orden_servicios SET cotizacion_n = '$name_documento' WHERE id=$id");
       rename ($upload, "../files/cotizaciones/".$name_documento);   
       echo '<script>M.toast({html:"Documento subido con exito.", classes: "rounded"})</script>';   
    }
}
?>
<script>
    id = <?php echo $id; ?>;
    var a = document.createElement("a");
      a.href = "../views/atender_orden.php?id_orden="+id;
      a.click();
</script>
