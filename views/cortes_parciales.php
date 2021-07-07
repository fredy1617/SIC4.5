<!DOCTYPE html>
<html lang="en">
<head>
<?php
  #INCLUIMOS EL ARCHIVO DONDE ESTA LA BARRA DE NAVEGACION DEL SISTEMA
  include('fredyNav.php');
  #INCLUIMOS EL ARCHIVO EL CUAL HACE LA CONEXION DE LA BASE DE DATOS PARA ACCEDER A LA INFORMACION DEL SISTEMA
  include('../php/conexion.php');
?>
<title>SIC | Cortes Parciales</title>
</head>
<script>
  //FUNCION QUE ENVIA LOS DATOS PARA VALIDAR DESPUES DE LLENADO DEL MODAL
  function recargar_corteP() {
    var textoClave = $("input#claveP").val(); 
    var textoCobrador = $("input#cobradorP").val();
 
    if (textoClave == "") {
        M.toast({html:"Ingrese una clave.", classes: "rounded"});
    }else if (textoCobrador == "") {
        M.toast({html:"Ingrese un cobrador.", classes: "rounded"});
    }else{
        $.post("../php/crear_corteP.php", {
              valorClave: textoClave,
              valorCobrador: textoCobrador
            }, function(mensaje) {
                $("#resultado_corte").html(mensaje);
        });
    }   
  } 
</script>
<main>
<body>
    <?php
    $id_user = $_SESSION['user_id'];
    $usuario = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM users WHERE user_id=$id_user"));
    ?>
	<div class="container">
    <h3 class="hide-on-med-and-down">Pagos realizados por la cuenta de: <?php echo $usuario['user_name'];?></h3>
    <h5 class="hide-on-large-only">Pagos realizados por la cuenta de: <?php echo $usuario['user_name'];?></h5><br><br>
    <div>
     <div id="resultado_corte"></div> 
      <h4 class="row"><b><< Internet >></b></h4>
      <div class="row">
        <?php
        $sql_pagos = mysqli_query($conn, "SELECT * FROM pagos WHERE id_user=$id_user AND corteP = 0 AND tipo_cambio='Efectivo' AND tipo != 'Dispositivo' AND tipo != 'Orden Servicio'");
        $filas = mysqli_num_rows($sql_pagos);
        if ($filas > 0) {
          $total = mysqli_fetch_array(mysqli_query($conn, "SELECT SUM(cantidad) AS precio FROM pagos WHERE id_user=$id_user AND corteP = 0 AND tipo_cambio='Efectivo' AND tipo != 'Dispositivo' AND tipo != 'Orden Servicio'"));
        ?>
        <h5 class="blue-text"><b>Efectivo:</b></h5>
        <table class="bordered highlight responsive-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>No. Cliente</th>
                    <th>Nombre</th>
                    <th>Descripción</th>
                    <th>Tipo</th>
                    <th>Fecha</th>
                    <th>Cantidad</th>
                </tr>
            </thead>
            <tbody>
            <?php
              $aux = 0;
             while($pagos = mysqli_fetch_array($sql_pagos)){
              $aux ++;
              $id_cliente = $pagos['id_cliente'];
              if ($pagos['tipo'] == 'Abono Corte') {
                  $cliente = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM users WHERE user_id = $id_cliente"));
              }else if ((mysqli_num_rows(mysqli_query($conn, "SELECT * FROM clientes WHERE id_cliente = $id_cliente"))) == 0) {
                $cliente = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM especiales WHERE id_cliente = $id_cliente"));
              }else{
                  $cliente = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM clientes WHERE id_cliente = $id_cliente"));
              }
                ?>
                <tr>
                  <th><?php echo $aux; ?></th> 
                  <td><?php echo $id_cliente; ?></td>
                  <td><?php echo ($pagos['tipo'] == 'Abono Corte')?'USUARIO: '.$cliente['firstname'].' '.$cliente['lastname']:$cliente['nombre']; ?></td>
                  <td><?php echo $pagos['descripcion']; ?></td>
                  <td><?php echo $pagos['tipo']; ?></td>
                  <td><?php echo $pagos['fecha'].' '.$pagos['hora']; ?></td>
                  <td>$<?php echo $pagos['cantidad'];?>.00</td>
                </tr>
                <?php
             }
            ?>
            </tbody>
        </table>
        </div>
        <div class="row">
        <h4 class="right">Total: $<?php echo $total['precio'];?></h4>
        </div>
        <?php
        }
        $sql_banco = mysqli_query($conn, "SELECT * FROM pagos WHERE id_user=$id_user AND corteP = 0 AND tipo_cambio='Banco'  AND tipo != 'Dispositivo' AND tipo != 'Orden Servicio'");
        $filas = mysqli_num_rows($sql_banco);
        if ($filas > 0) {
          $total = mysqli_fetch_array(mysqli_query($conn, "SELECT SUM(cantidad) AS precio FROM pagos WHERE id_user=$id_user AND corteP = 0 AND tipo_cambio='Banco' AND tipo != 'Dispositivo' AND tipo != 'Orden Servicio'"));
        ?>
        <h5 class="blue-text"><b>Banco:</b></h5>
        
        <div class="row">
        <table class="bordered highlight responsive-table">
          <thead>
            <th>#</th>
            <th>No. Cliente</th>
            <th>Nombre</th>
            <th>Descripción</th>
            <th>Tipo</th>
            <th>Fecha</th>
            <th>Cantidad</th>
          </thead>
          <tbody>
          <?php
          $aux = 0;
          while($pagos = mysqli_fetch_array($sql_banco)){
          $aux ++;
          $id_cliente = $pagos['id_cliente'];
          if ($pagos['tipo'] == 'Abono Corte') {
              $cliente = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM users WHERE user_id = $id_cliente"));
          }else if ((mysqli_num_rows(mysqli_query($conn, "SELECT * FROM clientes WHERE id_cliente = $id_cliente"))) == 0) {
              $cliente = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM especiales WHERE id_cliente = $id_cliente"));
          }else{
              $cliente = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM clientes WHERE id_cliente = $id_cliente"));
          }
          ?>
            <tr>
              <th><?php echo $aux; ?></th> 
              <td><?php echo $id_cliente; ?></td>
              <td><?php echo ($pagos['tipo'] == 'Abono Corte')?'USUARIO: '.$cliente['firstname'].' '.$cliente['lastname']:$cliente['nombre']; ?></td>
              <td><?php echo $pagos['descripcion']; ?></td>
              <td><?php echo $pagos['tipo']; ?></td>
              <td><?php echo $pagos['fecha'].' '.$pagos['hora']; ?></td>
              <td>$<?php echo $pagos['cantidad'];?>.00</td>
            </tr>
          <?php
          }
          ?> 
          </tbody>
        </table></div>

        <div class="row">
        <h4 class="right">Total: $<?php echo $total['precio'];?></h4>
        </div>
        <?php
        }
        //--------CREDITO---------
        $sql_Credito = mysqli_query($conn, "SELECT * FROM pagos WHERE id_user=$id_user AND corteP = 0 AND tipo_cambio='Credito'  AND tipo != 'Dispositivo'");
        $filas = mysqli_num_rows($sql_Credito);
        if ($filas > 0) {
          $total = mysqli_fetch_array(mysqli_query($conn, "SELECT SUM(cantidad) AS precio FROM pagos WHERE id_user=$id_user AND corteP = 0 AND tipo_cambio='Credito' AND tipo != 'Dispositivo'"));
        ?>
        <h5 class="blue-text"><b>Credito:</b></h5>        
        <div class="row">
        <table class="bordered highlight responsive-table">
          <thead>
            <th>#</th>
            <th>No. Cliente</th>
            <th>Nombre</th>
            <th>Descripción</th>
            <th>Tipo</th>
            <th>Fecha</th>
            <th>Cantidad</th>
          </thead>
          <tbody>
          <?php
          $aux = 0;
          while($pagos = mysqli_fetch_array($sql_Credito)){
          $aux ++;
          $id_cliente = $pagos['id_cliente'];
          if ((mysqli_num_rows(mysqli_query($conn, "SELECT * FROM clientes WHERE id_cliente = $id_cliente"))) == 0) {
            $cliente = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM especiales WHERE id_cliente = $id_cliente"));
          }else{
              $cliente = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM clientes WHERE id_cliente = $id_cliente"));
          }
            ?>
            <tr>
              <th><?php echo $aux; ?></th> 
              <td><?php echo $id_cliente; ?></td>
              <td><?php echo $cliente['nombre']; ?></td>
              <td><?php echo $pagos['descripcion']; ?></td>
              <td><?php echo $pagos['tipo']; ?></td>
              <td><?php echo $pagos['fecha'].' '.$pagos['hora']; ?></td>
              <td>$<?php echo $pagos['cantidad'];?>.00</td>
            </tr>
          <?php
          }
          ?> 
          </tbody>
        </table></div>
        <div class="row">
        <h4 class="right">Total: - $<?php echo $total['precio'];?></h4>
        </div>
        <?php
        }
        ?>
    </div>
    <div>
      <h4 class="row"><b><< Ordenes Servicio >></b></h4>
      <div class="row">
        <?php
        $sql_pagos = mysqli_query($conn, "SELECT * FROM pagos WHERE id_user=$id_user AND corteP = 0 AND tipo_cambio='Efectivo' AND tipo = 'Orden Servicio'");
        $filas = mysqli_num_rows($sql_pagos);
        if ($filas > 0) {
          $total = mysqli_fetch_array(mysqli_query($conn, "SELECT SUM(cantidad) AS precio FROM pagos WHERE id_user=$id_user AND corteP = 0 AND tipo_cambio='Efectivo' AND tipo = 'Orden Servicio'"));
        ?> 
        <h5 class="blue-text"><b>Efectivo:</b></h5>
        <table class="bordered highlight responsive-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>No. Cliente</th>
                    <th>Nombre</th>
                    <th>Descripción</th>
                    <th>Tipo</th>
                    <th>Fecha</th>
                    <th>Cantidad</th>
                </tr>
            </thead>
            <tbody>
        <?php        
          $aux = 0;
         while($pagos = mysqli_fetch_array($sql_pagos)){
          $aux ++;
          $id_cliente = $pagos['id_cliente'];
          $cliente = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM especiales WHERE id_cliente = $id_cliente"));
            ?>
            <tr>
              <th><?php echo $aux; ?></th> 
              <td><?php echo $id_cliente; ?></td>
              <td><?php echo $cliente['nombre']; ?></td>
              <td><?php echo $pagos['descripcion']; ?></td>
              <td><?php echo $pagos['tipo']; ?></td>
              <td><?php echo $pagos['fecha'].' '.$pagos['hora']; ?></td>
              <td>$<?php echo $pagos['cantidad'];?>.00</td>
            </tr>
            <?php
         }
        ?>
            </tbody>
        </table>
        </div>
        <div class="row">
        <h4 class="right">Total: $<?php echo $total['precio'];?></h4>
        </div>
        <?php
        }
        $sql_banco = mysqli_query($conn, "SELECT * FROM pagos WHERE id_user=$id_user AND corteP = 0 AND tipo_cambio='Banco'  AND tipo = 'Orden Servicio'");
        $filas = mysqli_num_rows($sql_banco);
        if ($filas > 0) {
          $total = mysqli_fetch_array(mysqli_query($conn, "SELECT SUM(cantidad) AS precio FROM pagos WHERE id_user=$id_user AND corteP = 0 AND tipo_cambio='Banco' AND tipo = 'Orden Servicio'"));
        ?>
        <h5 class="blue-text"><b>Banco:</b></h5>        
        <div class="row">
        <table class="bordered highlight responsive-table">
          <thead>
            <th>#</th>
            <th>No. Cliente</th>
            <th>Nombre</th>
            <th>Descripción</th>
            <th>Tipo</th>
            <th>Fecha</th>
            <th>Cantidad</th>
          </thead>
          <tbody>
          <?php
          $aux = 0;
          while($pagos = mysqli_fetch_array($sql_banco)){
          $aux ++;
          $id_cliente = $pagos['id_cliente'];
          $cliente = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM especiales WHERE id_cliente = $id_cliente"));
            ?>
            <tr>
              <th><?php echo $aux; ?></th> 
              <td><?php echo $id_cliente; ?></td>
              <td><?php echo $cliente['nombre']; ?></td>
              <td><?php echo $pagos['descripcion']; ?></td>
              <td><?php echo $pagos['tipo']; ?></td>
              <td><?php echo $pagos['fecha'].' '.$pagos['hora']; ?></td>
              <td>$<?php echo $pagos['cantidad'];?>.00</td>
            </tr>
          <?php
          }
          ?> 
          </tbody>
        </table>
        </div>
        <div class="row">
        <h4 class="right">Total: $<?php echo $total['precio'];?></h4>
        </div>
        <?php
        }
        ?>
    </div>
    <div>
      <h4 class="row"><b><< Servicio Tecnico >></b></h4>
      <div class="row">
        <?php
        $sql_pagos = mysqli_query($conn, "SELECT * FROM pagos WHERE id_user=$id_user AND corteP = 0 AND tipo_cambio='Efectivo' AND tipo = 'Dispositivo'");
        $filas = mysqli_num_rows($sql_pagos);
        if ($filas > 0) {
          $total = mysqli_fetch_array(mysqli_query($conn, "SELECT SUM(cantidad) AS precio FROM pagos WHERE id_user=$id_user AND corteP = 0 AND tipo_cambio='Efectivo' AND tipo = 'Dispositivo'"));
        ?>
        <h5 class="blue-text"><b>Efectivo:</b></h5>
        <table class="bordered highlight responsive-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>No. Cliente</th>
                    <th>Nombre</th>
                    <th>Descripción</th>
                    <th>Tipo</th>
                    <th>Fecha</th>
                    <th>Cantidad</th>
                </tr>
            </thead>
            <tbody>
        <?php        
          $aux = 0;
         while($pagos = mysqli_fetch_array($sql_pagos)){
          $aux ++;
          $id_cliente = $pagos['id_cliente'];
          $sql = mysqli_query($conn, "SELECT nombre FROM dispositivos WHERE id_dispositivo = $id_cliente"); 
          $cliente= mysqli_fetch_array($sql);
            ?>
            <tr>
              <th><?php echo $aux; ?></th> 
              <td><?php echo $id_cliente; ?></td>
              <td><?php echo $cliente['nombre']; ?></td>
              <td><?php echo $pagos['descripcion']; ?></td>
              <td><?php echo $pagos['tipo']; ?></td>
              <td><?php echo $pagos['fecha'].' '.$pagos['hora']; ?></td>
              <td>$<?php echo $pagos['cantidad'];?>.00</td>
            </tr>
            <?php
         }
        ?>
            </tbody>
        </table>
        </div>
        <div class="row">
        <h4 class="right">Total: <?php echo $total['precio'];?></h4>
        </div>
        <?php
         }
        $sql_banco = mysqli_query($conn, "SELECT * FROM pagos WHERE id_user=$id_user AND corteP = 0 AND tipo_cambio='Banco'  AND tipo = 'Dispositivo'");
        $filas = mysqli_num_rows($sql_banco);
        if ($filas > 0) {
          $total = mysqli_fetch_array(mysqli_query($conn, "SELECT SUM(cantidad) AS precio FROM pagos WHERE id_user=$id_user AND corteP = 0 AND tipo_cambio='Banco' AND tipo = 'Dispositivo'"));
        ?>
        <h5 class="blue-text"><b>Banco:</b></h5>        
        <div class="row">
        <table class="bordered highlight responsive-table">
          <thead>
            <th>#</th>
            <th>No. Cliente</th>
            <th>Nombre</th>
            <th>Descripción</th>
            <th>Tipo</th>
            <th>Fecha</th>
            <th>Cantidad</th>
          </thead>
          <tbody>
          <?php
          $aux = 0;
          while($pagos = mysqli_fetch_array($sql_banco)){
          $aux ++;
          $id_cliente = $pagos['id_cliente'];
          $sql = mysqli_query($conn, "SELECT nombre FROM dispositivos WHERE id_dispositivo = $id_cliente"); 
          $cliente= mysqli_fetch_array($sql);
          
            ?>
            <tr>
              <th><?php echo $aux; ?></th> 
              <td><?php echo $id_cliente; ?></td>
              <td><?php echo $cliente['nombre']; ?></td>
              <td><?php echo $pagos['descripcion']; ?></td>
              <td><?php echo $pagos['tipo']; ?></td>
              <td><?php echo $pagos['fecha'].' '.$pagos['hora']; ?></td>
              <td>$<?php echo $pagos['cantidad'];?>.00</td>
            </tr>
          <?php
          }
          ?> 
          </tbody>
        </table></div>
        <div class="row">
        <h4 class="right">Total: <?php echo $total['precio'];?></h4>
        </div>
        <?php
        }
        ?>
    </div>
        <div class="row">
        <a class="waves-effect waves-light btn pink right modal-trigger" href="#corteP">CORTE<i class="material-icons right">content_cut</i></a>
        </div>

<?php mysqli_close($conn);?>
</body>
</main>
</html>