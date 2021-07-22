<?php 
#INCLUIMOS EL ARCHIVO CON LA CONEXION A LA BASE DE DATOS
include('../php/conexion.php');

#RECIBIMOS LOS VALORES Codigo y Cantidad QUE SE NOS ENVIA DESDE EL FORMULARIO DE LA VISTA INVENTARIO (PARA VERIFICAR)
$Codigo = $conn->real_escape_string($_POST['valorCodigo']);
$Cantidad = $conn->real_escape_string($_POST['valorCantidad']);
#RECIBIMOS LOS VALORES QUE SE NOS ENVIA DESDE EL FORMULARIO DE LA VISTA INVENTARIO (PARA INSERTAR/ACTUALIZAR)
$Nombre = $conn->real_escape_string($_POST['valorNombre']);
$Unidad = $conn->real_escape_string($_POST['valorUnidad']);
$Marca = $conn->real_escape_string($_POST['valorMarca']);
$Estatus = $conn->real_escape_string($_POST['valorEstatus']);
$Responsable = $conn->real_escape_string($_POST['valorResponsable']);

//VERIFICAR SI EL CODIGO ES NUEVO O YA EXISTE (PRODUCTO/MATERIAL)
if (mysqli_num_rows(mysqli_query($conn, "SELECT * FROM inventario WHERE codigo = $Codigo"))>0) {
    #SI EL CODIGO YA EXISTE SOLO SE ACTUALIZARA LA CANTIDAD DEL PRODUCTO DE LA TABLA inventario
    $query = "UPDATE inventario SET cantidad = cantidad+$Cantidad, nombre = '$Nombre', unidad = '$Unidad', marca = '$Marca', estatus = '$Estatus', responsable = '$Responsable' WHERE codigo = $Codigo";
    #VERIFICAMOS SI SE REALIZA LA ACTUALIZACION..
    if(mysqli_query($conn, $query)){
        echo '<script>M.toast({html :"Se actualizo correctamente...", classes: "rounded"})</script>';
    }
}else{
    #SI EL CODIGO ES NUEVO SE AGREGARA UN PRODUCTO NUEVO A LA TABLA inventario
    $insert = "INSERT INTO inventario (codigo, nombre, cantidad, unidad, marca, estatus, responsable) VALUES ('$Codigo', '$Nombre', '$Cantidad', '$Unidad', '$Marca', '$Estatus', '$Responsable')";
    #VERIFICAMOS SI SE REALIZA LA INSERCION DEL NUEVO PRODUCTO/MATERIAL
    if(mysqli_query($conn, $insert)){
        echo '<script>M.toast({html:"Producto agregado correctamente al inventario.", classes: "rounded"})</script>';
    }   
}
?>
<script>
    setTimeout("location.href='../views/inventario.php'", 1000);
</script>
