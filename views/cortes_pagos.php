<!DOCTYPE html>
<html lang="en">
<head>
<?php
  #INCLUIMOS EL ARCHIVO DONDE ESTA LA BARRA DE NAVEGACION DEL SISTEMA
  include('fredyNav.php');
  #INCLUIMOS EL ARCHIVO EL CUAL HACE LA CONEXION DE LA BASE DE DATOS PARA ACCEDER A LA INFORMACION DEL SISTEMA
  include('../php/conexion.php');
?>
<title>SIC | Cortes Pagos</title>
</head>
<script>
  //FUNCION QUE ENVIA LOS DATOS PARA VALIDAR DESPUES DE LLENADO DEL MODAL
  function recargar_corte() {
    var textoClave = $("input#clave").val(); 
    var textoCantidad = $("input#cantidadD").val(); 
    var textoDescripcion = $("input#descripcionD").val();

    entra = "Si";
    if (textoCantidad != 0 || textoDescripcion != "") {
      if (textoCantidad <= 0) {
        entra = "No";
        texto = "Ingrese una cantidad correcta";
      }
      if (textoDescripcion == "") {
        entra = "No";
        texto = "Ingrese una descripcion correcta";
      }
    } 
    if (textoClave == "") {
        M.toast({html:"El campo clave no puede ir vacío.", classes: "rounded"});
    }else if (entra == "No") {
        M.toast({html:texto, classes: "rounded"});
    }else{
        $.post("../php/crear_corte.php", {
              valorClave: textoClave,
              valorCantidad: textoCantidad,
              valorDescripcion: textoDescripcion
            }, function(mensaje) {
                $("#resultado_corte").html(mensaje);
        });
    }   
  } 
  //FUNCION QUE ENVIA LA INFORMACION PARA CONFIRMAR EL CORTE Y CHECAR SI EL COBRADOR ENTREGO TODO O QUEDO A DEBER EFECTIVO
  function confirmar(){
    var textoId = $("input#id").val(); 
    var textoCantidad = $("input#cantidadCon").val(); 

    if (textoId <= 0) {
        M.toast({html:"Ingese un Id de corte.", classes: "rounded"});
    }else{
        $.post("../php/confirmar_corte.php", {
              valorId: textoId,
              valorCantidad: textoCantidad
            }, function(mensaje) {
                $("#resultado_confirmar").html(mensaje);
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
    <h3 class="hide-on-med-and-down">Pagos realizados por: <?php echo $usuario['user_name'];?></h3>
    <h5 class="hide-on-large-only">Pagos realizados por: <?php echo $usuario['user_name'];?></h5>
    <?php
      // SACAMOS LA SUMA DE TODAS LAS DEUDAS Y ABONOS ....
      $deuda = mysqli_fetch_array(mysqli_query($conn, "SELECT SUM(cantidad) AS suma FROM deudas_cortes WHERE cobrador = $id_user"));
      $abono = mysqli_fetch_array(mysqli_query($conn, "SELECT SUM(cantidad) AS suma FROM pagos WHERE id_cliente = $id_user AND tipo = 'Abono Corte'"));
      //COMPARAMOS PARA VER SI LOS VALORES ESTAN VACOIOS::
      if ($deuda['suma'] == "") {
        $deuda['suma'] = 0;
      }elseif ($abono['suma'] == "") {
        $abono['suma'] = 0;
      }
      //SE RESTAN DEUDAS DE ABONOS
      $Saldo = $deuda['suma']-$abono['suma'];
      if ($Saldo > 0) {
    ?>
      <h4 class="hide-on-med-and-down right red-text">Saldo Pendiente de: $<?php echo $Saldo;?> <form method="post" action="../views/saldo_cobrador.php"><input name="id" type="hidden" value="<?php echo $id_user; ?>"><button type="submit" class="btn btn-tiny waves-effect waves-light pink"><i class="material-icons  right">send</i>VER</button></form></h4>
      <h5 class="hide-on-large-only right red-text">Saldo Pendiente de: $<?php echo $Saldo;?> <form method="post" action="../views/saldo_cobrador.php"><input name="id" type="hidden" value="<?php echo $id_user; ?>"><button type="submit" class="btn btn-tiny waves-effect waves-light pink"><i class="material-icons  right">send</i>VER</button></form></h5>
    <br><br>
    <?php }//FIN IF ?>
    <br><br>
    <div>
     <div id="resultado_corte"></div> 
      <h4 class="row"><b><< Internet >></b></h4>
      <div class="row">
        <?php
        $sql_pagos = mysqli_query($conn, "SELECT * FROM pagos WHERE id_user=$id_user AND corte = 0 AND tipo_cambio='Efectivo' AND tipo != 'Dispositivo' AND tipo != 'Orden Servicio'");
        $filas = mysqli_num_rows($sql_pagos);
        if ($filas > 0) {
          $total = mysqli_fetch_array(mysqli_query($conn, "SELECT SUM(cantidad) AS precio FROM pagos WHERE id_user=$id_user AND corte = 0 AND tipo_cambio='Efectivo' AND tipo != 'Dispositivo' AND tipo != 'Orden Servicio'"));
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
        $sql_banco = mysqli_query($conn, "SELECT * FROM pagos WHERE id_user=$id_user AND corte = 0 AND tipo_cambio='Banco'  AND tipo != 'Dispositivo' AND tipo != 'Orden Servicio'");
        $filas = mysqli_num_rows($sql_banco);
        if ($filas > 0) {
          $total = mysqli_fetch_array(mysqli_query($conn, "SELECT SUM(cantidad) AS precio FROM pagos WHERE id_user=$id_user AND corte = 0 AND tipo_cambio='Banco' AND tipo != 'Dispositivo' AND tipo != 'Orden Servicio'"));
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
        $sql_pagos = mysqli_query($conn, "SELECT * FROM pagos WHERE id_user=$id_user AND corte = 0 AND tipo_cambio='Efectivo' AND tipo = 'Orden Servicio'");
        $filas = mysqli_num_rows($sql_pagos);
        if ($filas > 0) {
          $total = mysqli_fetch_array(mysqli_query($conn, "SELECT SUM(cantidad) AS precio FROM pagos WHERE id_user=$id_user AND corte = 0 AND tipo_cambio='Efectivo' AND tipo = 'Orden Servicio'"));
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
        $sql_banco = mysqli_query($conn, "SELECT * FROM pagos WHERE id_user=$id_user AND corte = 0 AND tipo_cambio='Banco'  AND tipo = 'Orden Servicio'");
        $filas = mysqli_num_rows($sql_banco);
        if ($filas > 0) {
          $total = mysqli_fetch_array(mysqli_query($conn, "SELECT SUM(cantidad) AS precio FROM pagos WHERE id_user=$id_user AND corte = 0 AND tipo_cambio='Banco' AND tipo = 'Orden Servicio'"));
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
        $sql_pagos = mysqli_query($conn, "SELECT * FROM pagos WHERE id_user=$id_user AND corte = 0 AND tipo_cambio='Efectivo' AND tipo = 'Dispositivo'");
        $filas = mysqli_num_rows($sql_pagos);
        if ($filas > 0) {
          $total = mysqli_fetch_array(mysqli_query($conn, "SELECT SUM(cantidad) AS precio FROM pagos WHERE id_user=$id_user AND corte = 0 AND tipo_cambio='Efectivo' AND tipo = 'Dispositivo'"));
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
        <a class="waves-effect waves-light btn pink right modal-trigger" href="#corte">CORTE<i class="material-icons right">content_cut</i></a>
        </div>

<!-- VISTA DE CONFIRMAR PAGO  -->
    <div id="resultado_confirmar"></div>
    <div class="row"><br><br>
      <h3 class="hide-on-med-and-down">Confirmar Corte:</h3>
      <h5 class="hide-on-large-only">Confirmar Corte:</h5>
      <form class="col s12"><br>     
          <div class="row col s10 m3 l3">
            <div class="input-field">
                <i class="material-icons prefix">filter_9_plus</i>
                <input id="id" type="number" class="validate" data-length="6"  required>
                <label for="id">Corte (ej: 1243):</label>
            </div>
          </div>
          <div class="row col s10 m3 l3">
            <div class="input-field">
                <i class="material-icons prefix">payment</i>
                <input id="cantidadCon" type="number" class="validate" data-length="6" value="0" required>
                <label for="cantidadCon">Cantidad (Efectivo):</label>
            </div>
          </div>
          <div class="row"><br>
            <a onclick="confirmar();" class="waves-effect waves-light btn pink right "><i class="material-icons right">send</i>Confirmar</a>
          </div>
      </form>
    </div>
  </div><br><br><!-- FIN DE CONTAINER  -->
<?php mysqli_close($conn);?>
</body>
</main>
</html>