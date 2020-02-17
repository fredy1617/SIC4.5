<!DOCTYPE html>
<html lang="en">
<head>
<?php
  include('fredyNav.php');
  include('../php/conexion.php');
?>
<title>SIC | Cortes Pagos</title>
</head>
<main>
<body>
    <?php
    $id_user = $_SESSION['user_id'];
    $usuario = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM users WHERE user_id=$id_user"));
    ?>
	<div class="container">
    <h3 class="hide-on-med-and-down">Pagos realizados por: <?php echo $usuario['user_name'];?></h3>
    <h5 class="hide-on-large-only">Pagos realizados por: <?php echo $usuario['user_name'];?></h5><br><br>
    <div>
      <h4 class="row"><b><< Internet >></b></h4>
      <div class="row">
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
        $sql_pagos = mysqli_query($conn, "SELECT * FROM pagos WHERE id_user=$id_user AND corte = 0 AND tipo_cambio='Efectivo' AND tipo != 'Dispositivo'");
        $sql_total = mysqli_query($conn, "SELECT SUM(cantidad) AS precio FROM pagos WHERE id_user=$id_user AND corte = 0 AND tipo_cambio='Efectivo' AND tipo != 'Dispositivo'");
        $total = mysqli_fetch_array($sql_total);
          $aux = 0;
         while($pagos = mysqli_fetch_array($sql_pagos)){
          $aux ++;
          $id_cliente = $pagos['id_cliente'];
          $sql = mysqli_query($conn, "SELECT nombre FROM clientes WHERE id_cliente = $id_cliente");
          $filas = mysqli_num_rows($sql);
          if ($filas == 0) {
            $sql = mysqli_query($conn, "SELECT nombre FROM dispositivos WHERE id_dispositivo = $id_cliente"); 
          }
          $cliente= mysqli_fetch_array($sql);
            ?>
            <tr>
              <th><?php echo $aux; ?></th> 
              <td><?php echo $id_cliente; ?></td>
              <td><?php echo $cliente['nombre']; ?></td>
              <td><?php echo $pagos['descripcion']; ?></td>
              <td><?php echo $pagos['tipo']; ?></td>
              <td><?php echo $pagos['fecha']; ?></td>
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
        $sql_banco = mysqli_query($conn, "SELECT * FROM pagos WHERE id_user=$id_user AND corte = 0 AND tipo_cambio='Banco'  AND tipo != 'Dispositivo'");
        $filas = mysqli_num_rows($sql_banco);
        if ($filas > 0) {
          $total = mysqli_fetch_array(mysqli_query($conn, "SELECT SUM(cantidad) AS precio FROM pagos WHERE id_user=$id_user AND corte = 0 AND tipo_cambio='Banco' AND tipo != 'Dispositivo'"));
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
          $sql = mysqli_query($conn, "SELECT nombre FROM clientes WHERE id_cliente = $id_cliente");
          $filas = mysqli_num_rows($sql);
          if ($filas == 0) {
            $sql = mysqli_query($conn, "SELECT nombre FROM dispositivos WHERE id_dispositivo = $id_cliente"); 
          }
          $cliente= mysqli_fetch_array($sql);
          
            ?>
            <tr>
              <th><?php echo $aux; ?></th> 
              <td><?php echo $id_cliente; ?></td>
              <td><?php echo $cliente['nombre']; ?></td>
              <td><?php echo $pagos['descripcion']; ?></td>
              <td><?php echo $pagos['tipo']; ?></td>
              <td><?php echo $pagos['fecha']; ?></td>
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
        $sql_Credito = mysqli_query($conn, "SELECT * FROM pagos WHERE id_user=$id_user AND corte = 0 AND tipo_cambio='Credito'  AND tipo != 'Dispositivo'");
        $filas = mysqli_num_rows($sql_Credito);
        if ($filas > 0) {
          $total = mysqli_fetch_array(mysqli_query($conn, "SELECT SUM(cantidad) AS precio FROM pagos WHERE id_user=$id_user AND corte = 0 AND tipo_cambio='Credito' AND tipo != 'Dispositivo'"));
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
          $sql = mysqli_query($conn, "SELECT nombre FROM clientes WHERE id_cliente = $id_cliente");
          $filas = mysqli_num_rows($sql);
          if ($filas == 0) {
            $sql = mysqli_query($conn, "SELECT nombre FROM dispositivos WHERE id_dispositivo = $id_cliente"); 
          }
          $cliente= mysqli_fetch_array($sql);
          
            ?>
            <tr>
              <th><?php echo $aux; ?></th> 
              <td><?php echo $id_cliente; ?></td>
              <td><?php echo $cliente['nombre']; ?></td>
              <td><?php echo $pagos['descripcion']; ?></td>
              <td><?php echo $pagos['tipo']; ?></td>
              <td><?php echo $pagos['fecha']; ?></td>
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
      <h4 class="row"><b><< Servicio Tecnico >></b></h4>
      <div class="row">
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
        $sql_pagos = mysqli_query($conn, "SELECT * FROM pagos WHERE id_user=$id_user AND corte = 0 AND tipo_cambio='Efectivo' AND tipo = 'Dispositivo'");
        $sql_total = mysqli_query($conn, "SELECT SUM(cantidad) AS precio FROM pagos WHERE id_user=$id_user AND corte = 0 AND tipo_cambio='Efectivo' AND tipo = 'Dispositivo'");
        $total = mysqli_fetch_array($sql_total);
          $aux = 0;
         while($pagos = mysqli_fetch_array($sql_pagos)){
          $aux ++;
          $id_cliente = $pagos['id_cliente'];
          $sql = mysqli_query($conn, "SELECT nombre FROM clientes WHERE id_cliente = $id_cliente");
          $filas = mysqli_num_rows($sql);
          if ($filas == 0) {
            $sql = mysqli_query($conn, "SELECT nombre FROM dispositivos WHERE id_dispositivo = $id_cliente"); 
          }
          $cliente= mysqli_fetch_array($sql);
            ?>
            <tr>
              <th><?php echo $aux; ?></th> 
              <td><?php echo $id_cliente; ?></td>
              <td><?php echo $cliente['nombre']; ?></td>
              <td><?php echo $pagos['descripcion']; ?></td>
              <td><?php echo $pagos['tipo']; ?></td>
              <td><?php echo $pagos['fecha']; ?></td>
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
        $sql_banco = mysqli_query($conn, "SELECT * FROM pagos WHERE id_user=$id_user AND corte = 0 AND tipo_cambio='Banco'  AND tipo = 'Dispositivo'");
        $filas = mysqli_num_rows($sql_banco);
        if ($filas > 0) {
          $total = mysqli_fetch_array(mysqli_query($conn, "SELECT SUM(cantidad) AS precio FROM pagos WHERE id_user=$id_user AND corte = 0 AND tipo_cambio='Banco' AND tipo = 'Dispositivo'"));
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
          $sql = mysqli_query($conn, "SELECT nombre FROM clientes WHERE id_cliente = $id_cliente");
          $filas = mysqli_num_rows($sql);
          if ($filas == 0) {
            $sql = mysqli_query($conn, "SELECT nombre FROM dispositivos WHERE id_dispositivo = $id_cliente"); 
          }
          $cliente= mysqli_fetch_array($sql);
          
            ?>
            <tr>
              <th><?php echo $aux; ?></th> 
              <td><?php echo $id_cliente; ?></td>
              <td><?php echo $cliente['nombre']; ?></td>
              <td><?php echo $pagos['descripcion']; ?></td>
              <td><?php echo $pagos['tipo']; ?></td>
              <td><?php echo $pagos['fecha']; ?></td>
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
        <a class="waves-effect waves-light btn pink right modal-trigger" href="#corte">CORTE<i class="material-icons right">content_cut</i></a>
        </div>
  </div><br><br>
<?php mysqli_close($conn);?>
</body>
</main>
</html>