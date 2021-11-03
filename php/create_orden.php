<?php
session_start();
include('../php/conexion.php');
date_default_timezone_set('America/Mexico_City');
$Nuevo = $conn->real_escape_string($_POST['valorNuevo']);
$Nombre = $conn->real_escape_string($_POST['valorNombres']);
$Telefono = $conn->real_escape_string($_POST['valorTelefono']);
$Comunidad = $conn->real_escape_string($_POST['valorComunidad']);
$Estatus = $conn->real_escape_string($_POST['valorEstatus']);
$Precio = $conn->real_escape_string($_POST['valorCosto']);
$Dpto = $conn->real_escape_string($_POST['valorDpto']);
$Referencia = $conn->real_escape_string($_POST['valorReferencia']);
$id_user = $_SESSION['user_id'];

            
if ($Nuevo == 'Si') {
  $sql_check1 =  mysqli_query($conn, "SELECT id_cliente FROM especiales WHERE nombre = '$Nombre' AND telefono = '$Telefono' AND referencia = '$Referencia' AND lugar = '$Comunidad'");            
  if (mysqli_num_rows($sql_check1)) {
    echo '<script>M.toast({html:"Ya se encuentra un cliente registrado con la misma info.", classes: "rounded"})</script>';
  }else{
    $sql = "INSERT INTO especiales (nombre, telefono, referencia, lugar, usuario) VALUES ('$Nombre', '$Telefono', '$Referencia', '$Comunidad', $id_user)";

    if(mysqli_query($conn, $sql)){
      echo '<script>M.toast({html:"Cliente registrado.", classes: "rounded"})</script>';
      $ultimo =  mysqli_fetch_array(mysqli_query($conn, "SELECT MAX(id_cliente) AS id FROM especiales"));            
      $IdCliente = $ultimo['id'];
      $Solicitud = $conn->real_escape_string($_POST['valorSolicitud']);
      $Fecha_hoy = date('Y-m-d');
      $Hora = date('H:i:s');

      $sql_check2 =  mysqli_query($conn, "SELECT id FROM orden_servicios WHERE id_cliente = '$IdCliente'AND solicitud = '$Solicitud'AND fecha = '$Fecha_hoy'");            
      
      if (mysqli_num_rows($sql_check2)) {
        echo '<script>M.toast({html:"Ya se encuentra una orden registrada con la misma info.", classes: "rounded"})</script>';
      }else{

        if ($Estatus == 'Cotizado') {
          $es = 'trabajo';
        }else{
          $es = 'solicitud';
        }
        $sql = "INSERT INTO orden_servicios (id_cliente, ".$es.", fecha, hora, registro, estatus, dpto, precio) VALUES ($IdCliente, '$Solicitud', '$Fecha_hoy', '$Hora', $id_user, '$Estatus', '$Dpto', '$Precio')";
        if(mysqli_query($conn, $sql)){
          echo  '<script>M.toast({html:"Orden de servicio creada.", classes: "rounded"})</script>'; 
          $ultimo =  mysqli_fetch_array(mysqli_query($conn, "SELECT MAX(id) AS id FROM orden_servicios"));            
          $id = $ultimo['id'];
           #CREAR TICKET
          ?>
          <script>
          id = <?php echo $id; ?>;
          var a = document.createElement("a");
              a.target = "_blank";
              a.href = "../php/entrada_orden.php?Id="+id;
              a.click();
          </script>
          <?php  
        }else{
          echo  '<script>M.toast({html:"Ha ocurrido un error.", classes: "rounded"})</script>';  
        }
      }
    }else{
      echo  '<script>M.toast({html:"Ha ocurrido un error.", classes: "rounded"})</script>';  
    }

    ?>
    <script>
      var a = document.createElement("a");
        a.href = "../views/ordenes_servicio.php";
        a.click();
    </script>
    <?php
  }
} else {
    $IdCliente = $conn->real_escape_string($_POST['id']);
    $sql = "UPDATE especiales SET nombre ='$Nombre', telefono = '$Telefono', referencia = '$Referencia', lugar = '$Comunidad' WHERE id_cliente='$IdCliente'";
    if(mysqli_query($conn, $sql)){
      echo '<script>M.toast({html:"El cliente se actualizó correctamente.", classes: "rounded"})</script>';
    }else{
      echo '<script>M.toast({html:"Ha ocurrido un error.", classes: "rounded"})</script>';  
    }
    $Solicitud = $conn->real_escape_string($_POST['valorSolicitud']);
    $Fecha_hoy = date('Y-m-d');
    $Hora = date('H:i:s');

    $sql_check2 =  mysqli_query($conn, "SELECT id FROM orden_servicios WHERE id_cliente = '$IdCliente'AND solicitud = '$Solicitud'AND fecha = '$Fecha_hoy'");            
    
    if (mysqli_num_rows($sql_check2)) {
      echo '<script>M.toast({html:"Ya se encuentra una orden registrada con la misma info.", classes: "rounded"})</script>';
    }else{
      if ($Estatus == 'Cotizado') {
        $es = 'trabajo';
      }else{
        $es = 'solicitud';
      }
      $sql = "INSERT INTO orden_servicios (id_cliente, ".$es.", fecha, hora, registro, estatus, dpto, precio) VALUES ($IdCliente, '$Solicitud', '$Fecha_hoy', '$Hora', $id_user, '$Estatus', '$Dpto', '$Precio')";
      if(mysqli_query($conn, $sql)){
        echo  '<script>M.toast({html:"Orden de servicio creada.", classes: "rounded"})</script>'; 
        $ultimo =  mysqli_fetch_array(mysqli_query($conn, "SELECT MAX(id) AS id FROM orden_servicios"));            
        $id = $ultimo['id'];
         #CREAR TICKET
        ?>
        <script>
        id = <?php echo $id; ?>;
        var a = document.createElement("a");
            a.target = "_blank";
            a.href = "../php/entrada_orden.php?Id="+id;
            a.click();
        </script>
        <?php  
      }else{
        echo  '<script>M.toast({html:"Ha ocurrido un error.", classes: "rounded"})</script>';  
      }
      ?>
    <script>
      var a = document.createElement("a");
        a.href = "../views/ordenes_servicio.php";
        a.click();
    </script>
    <?php
    }
}
mysqli_close($conn);
?>