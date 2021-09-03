 <?php 
  session_start();
  include('../php/conexion.php');
  date_default_timezone_set('America/Mexico_City');
  $id_user = $_SESSION['user_id'];
  $Fecha_hoy = date('Y-m-d');
  $Hora = date('H:i:s');

  $Tipo_Campio = $conn->real_escape_string($_POST['valorTipo_Campio']);
  $RegistrarCan = $conn->real_escape_string($_POST['valorCantidad']);
  $Descripcion = $conn->real_escape_string($_POST['valorDescripcion']);
  $IdCliente = $conn->real_escape_string($_POST['valorIdCliente']);
  $FechaCorte = $conn->real_escape_string($_POST['valorFechaCorte']);
  $SIG = $conn->real_escape_string($_POST['caso']);


  #--- CREAMOS EL SQL PARA LA INSERCION ---
    $sql = "INSERT INTO pagos (id_cliente, descripcion, cantidad, fecha, hora, tipo, id_user, corte, corteP, tipo_cambio, Cotejado) VALUES ($IdCliente, '$Descripcion', '$RegistrarCan', '$Fecha_hoy', '$Hora', 'Mensualidad', $id_user, 0, 0, '$Tipo_Campio', 0)";
    if ($Tipo_Campio == "Credito") {
      $mysql= "INSERT INTO deudas(id_cliente, cantidad, fecha_deuda, tipo, descripcion, usuario) VALUES ($IdCliente, '$RegistrarCan', '$Fecha_hoy', 'Mensualidad', '$Descripcion', $id_user)";
      mysqli_query($conn,$mysql);
      $ultimo =  mysqli_fetch_array(mysqli_query($conn, "SELECT MAX(id_deuda) AS id FROM deudas WHERE id_cliente = $IdCliente"));            
      $id_deuda = $ultimo['id'];
      $sql = "INSERT INTO pagos (id_cliente, descripcion, cantidad, fecha, hora, tipo, id_user, corte, corteP, tipo_cambio, id_deuda, Cotejado) VALUES ($IdCliente, '$Descripcion', '$RegistrarCan', '$Fecha_hoy', '$Hora', 'Mensualidad', $id_user, 0, 0, '$Tipo_Campio', $id_deuda, 0)";
    }
     
  #--- SE INSERTA EL PAGO -----------
    if(mysqli_query($conn, $sql)){
      echo '<script>M.toast({html:"El pago se dió de alta satisfcatoriamente.", classes: "rounded"})</script>';
      // Si el pago es de banco guardar la referencia....
      if (($Tipo_Campio == 'Banco' OR $Tipo_Campio == 'SAN') AND $ReferenciaB != '') {
        $ultimoPago =  mysqli_fetch_array(mysqli_query($conn, "SELECT MAX(id_pago) AS id FROM pagos WHERE id_cliente = $IdCliente"));            
        $id_pago = $ultimoPago['id'];
        mysqli_query($conn,  "INSERT INTO referencias (id_pago, descripcion) VALUES ('$id_pago', '$ReferenciaB')");
      }
      if ($SIG == 2) {
        #---- INSERTAR EL SIGUIENTE MES
        $Cantidad = $conn->real_escape_string($_POST['valorCantidad2']);
        $Descripcion2 = $conn->real_escape_string($_POST['valorDescripcion2']);

        #--- CREAMOS EL SQL PARA LA INSERCION ---
          $sql = "INSERT INTO pagos (id_cliente, descripcion, cantidad, fecha, hora, tipo, id_user, corte, corteP, tipo_cambio, Cotejado) VALUES ($IdCliente, '$Descripcion2', '$Cantidad', '$Fecha_hoy', '$Hora', 'Mensualidad', $id_user, 0, 0, '$Tipo_Campio', 0)";
          if ($Tipo_Campio == "Credito") {
            $mysql= "INSERT INTO deudas(id_cliente, cantidad, fecha_deuda, tipo, descripcion, usuario) VALUES ($IdCliente, '$Cantidad', '$Fecha_hoy', 'Mensualidad', '$Descripcion2', $id_user)";
            mysqli_query($conn,$mysql);
            $ultimo =  mysqli_fetch_array(mysqli_query($conn, "SELECT MAX(id_deuda) AS id FROM deudas WHERE id_cliente = $IdCliente"));            
            $id_deuda = $ultimo['id'];
            $sql = "INSERT INTO pagos (id_cliente, descripcion, cantidad, fecha, hora, tipo, id_user, corte, corteP, tipo_cambio, id_deuda, Cotejado) VALUES ($IdCliente, '$Descripcion2', '$Cantidad', '$Fecha_hoy', '$Hora', 'Mensualidad', $id_user, 0, 0, '$Tipo_Campio', $id_deuda, 0)";
          }
        #--- SE INSERTA EL PAGO -----------
          if(mysqli_query($conn, $sql)){
            echo '<script>M.toast({html:"El pago se dió de alta satisfcatoriamente.", classes: "rounded"})</script>';
            // Si el pago es de banco guardar la referencia....
            if (($Tipo_Campio == 'Banco' OR $Tipo_Campio == 'SAN') AND $ReferenciaB != '') {
              $ultimoPago =  mysqli_fetch_array(mysqli_query($conn, "SELECT MAX(id_pago) AS id FROM pagos WHERE id_cliente = $IdCliente"));            
              $id_pago = $ultimoPago['id'];
              mysqli_query($conn,  "INSERT INTO referencias (id_pago, descripcion) VALUES ('$id_pago', '$ReferenciaB')");
            }
          }
      }
      #ACTUALIZAMOS LA FECHA DE CORTE   --- IMPORTANTE----
      mysqli_query($conn, "UPDATE clientes SET fecha_corte='$FechaCorte' WHERE id_cliente='$IdCliente'");   
      ?>
      <script>
        var a = document.createElement("a");
            a.target = "_blank";
            a.href = "../php/activar_pago.php?id=<?php echo $IdCliente; ?>";
            a.click();
      </script>
      <?php   
    }else{
      echo '<script>M.toast({html:"Ha ocurrido un error.", classes: "rounded"})</script>';  
    }

  ?>
  <script>
    var a = document.createElement("a");
        a.href = "../views/pagos_internet.php?cliente=<?php echo $IdCliente; ?>";
        a.click();
  </script>