<?php 
session_start();
include('../php/conexion.php');
date_default_timezone_set('America/Mexico_City');

$Tipo_Campio = $conn->real_escape_string($_POST['valorTipo_Campio']);
$Cantidad = $conn->real_escape_string($_POST['valorCantidad']);
$Descripcion = $conn->real_escape_string($_POST['valorDescripcion']);
$IdCliente = $conn->real_escape_string($_POST['valorId']);
$Fecha_hoy = date('Y-m-d');
$Hora = date('H:i:s');
$id_user = $_SESSION['user_id'];

if(mysqli_num_rows(mysqli_query($conn, "SELECT * FROM pagos WHERE id_cliente = $IdCliente AND descripcion = '$Descripcion' AND  tipo = 'Abono Corte' AND cantidad='$Cantidad' AND fecha='$Fecha_hoy'"))>0){
	echo '<script>M.toast({html:"Ya se encuentra un abono registrado con los mismos valores el día de hoy.", classes: "rounded"})</script>';
}else{ 
	$sql = "INSERT INTO pagos (id_cliente, cantidad, fecha, hora, descripcion , tipo_cambio, id_user, tipo, corte) VALUES ($IdCliente, '$Cantidad', '$Fecha_hoy', '$Hora', '$Descripcion', '$Tipo_Campio', '$id_user', 'Abono Corte', 0)";
	if(mysqli_query($conn, $sql)){
          
    $Ver = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM deudas_cortes WHERE cobrador = $IdCliente AND liquidada = 0 limit 1"));
     
    // SACAMOS LA SUMA DE TODAS LAS DEUDAS_cortes QUE ESTAN LIQUIDADDAS Y TODOS LOS ABONOS ....
    $deuda = mysqli_fetch_array(mysqli_query($conn, "SELECT SUM(cantidad) AS suma FROM deudas_cortes WHERE cobrador = $IdCliente AND liquidada = 1"));
    $abono = mysqli_fetch_array(mysqli_query($conn, "SELECT SUM(cantidad) AS suma FROM pagos WHERE id_cliente = $IdCliente AND tipo = 'Abono Corte'"));
    if ($deuda['suma'] == "") {
      $deuda['suma'] = 0;
    }
    if ($abono['suma'] == "") {
      $abono['suma'] = 0;
    }
    $Resta = $abono['suma']-$deuda['suma'];

    $Entra = False;
    if ($Ver['cantidad'] <=0) {
      $Entra = False;
    }else if ($Ver['cantidad'] <= $Resta) {
      $Entra = True;  
    }
    $id_deuda = $Ver['id'];
     while ($Entra) {
      if (mysqli_query($conn, "UPDATE deudas_cortes SET liquidada = 1 WHERE id = $id_deuda")) {
        echo '<script>M.toast({html:"Deuda liquidada.", classes: "rounded"})</script>';
      }  
      $Ver = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM deudas_cortes WHERE cobrador = $IdCliente AND liquidada=0 limit 1"));
      $id_deuda = $Ver['id'];
      // SACAMOS LA SUMA DE TODAS LAS DEUDAS_cortes QUE ESTAN LIQUIDADDAS Y TODOS LOS ABONOS ....
      $deuda = mysqli_fetch_array(mysqli_query($conn, "SELECT SUM(cantidad) AS suma FROM deudas_cortes WHERE cobrador = $IdCliente AND liquidada = 1"));
      $abono = mysqli_fetch_array(mysqli_query($conn, "SELECT SUM(cantidad) AS suma FROM pagos WHERE id_cliente = $IdCliente AND tipo = 'Abono Corte'"));
      if ($deuda['suma'] == "") {
        $deuda['suma'] = 0;
      }elseif ($abono['suma'] == "") {
        $abono['suma'] = 0;
      }

      $Resta = $abono['suma']-$deuda['suma'];
      $Entra = False;
      if ($Ver['cantidad'] <=0) {
        $Entra = False;
      }else if ($Ver['cantidad'] <= $Resta) {
        $Entra = True;  
      }
      $id_deuda = $Ver['id_deuda'];
     }
		echo '<script>M.toast({html:"El abono se dió de alta satisfcatoriamente.", classes: "rounded"})</script>';
	}else{
		echo '<script>M.toast({html:"Ha ocurrido un error.", classes: "rounded"})</script>';	
	  }
}
?>
  <div id="mostrar_abonos">
    <?php 
    $id = $IdCliente;
    #TOMAMOS LA INFORMACION DEL COBRADOR
    $sql = mysqli_query($conn,"SELECT * FROM users WHERE user_id=$id");
    $datos = mysqli_fetch_array($sql);

    // SACAMOS LA SUMA DE TODAS LAS DEUDAS Y ABONOS ....
    $deuda = mysqli_fetch_array(mysqli_query($conn, "SELECT SUM(cantidad) AS suma FROM deudas_cortes WHERE cobrador = $id"));
    $abono = mysqli_fetch_array(mysqli_query($conn, "SELECT SUM(cantidad) AS suma FROM pagos WHERE id_cliente = $id AND tipo = 'Abono Corte'"));
    //COMPARAMOS PARA VER SI LOS VALORES ESTAN VACOIOS::
    if ($deuda['suma'] == "") {
      $deuda['suma'] = 0;
    }elseif ($abono['suma'] == "") {
      $abono['suma'] = 0;
    }
    //SE RESTAN DEUDAS DE ABONOS Y SI EL SALDO ES NEGATIVO SE CAMBIA DE COLOR
    $Saldo = $abono['suma']-$deuda['suma'];
    $color = 'green';
    if ($Saldo < 0) {
      $color = 'red darken-2';
    }
    ?>
    <div class="row"><br><br>
      <ul class="collection">
            <li class="collection-item avatar">
              <img src="../img/cliente.png" alt="" class="circle">
              <span class="title"><b>No. Usuario: </b><?php echo $id; ?></span>
              <p><b>Nombre(s): </b><?php echo $datos['firstname'].' '.$datos['lastname']; ?><br>
                 <b>Usuario: </b><?php echo $datos['user_name']; ?><br>
                 <br><hr>
                 <b>SALDO: </b> <span class="new badge <?php echo $color ?>" id="mostrar_deuda" data-badge-caption="">$<?php echo $Saldo; ?><br>
              </p>
            </li>
        </ul>   
    </div>
    <div class="row">
      <h3 class="hide-on-med-and-down">Abonar:</h3>
      <h5 class="hide-on-large-only">Abonar:</h5>
    </div>
    <div class="row">
      <form class="col s12">        
        <div class="row col s12 m3 l3">
        <div class="input-field">
          <i class="material-icons prefix">payment</i>
          <input id="cantidad" type="number" class="validate" data-length="6" value="0" required>
          <label for="cantidad">Cantidad:</label>
        </div>
      </div>
      <div class="row col s12 m7 l7">
        <div class="input-field">
          <i class="material-icons prefix">description</i>
          <input id="descripcion" type="text" class="validate" data-length="100" required>
          <label for="descripcion">Descripción: </label>
        </div>
      </div>
      <div class="col s12 m2 l2">
          <p>
            <br>
            <input type="checkbox" id="banco"/>
            <label for="banco">Banco</label>
          </p>
        </div>
        <input id="id" value="<?php echo htmlentities($id);?>" type="hidden">
      </form>
      <a onclick="insert_abono();" class="waves-effect waves-light btn pink right"><i class="material-icons right">send</i>Registrar Abono</a>
    <br>
    </div>
    <div class="row">
      <div class="col s12 m6 l6">
        <h4>Deudas: </h4>
        <table>
          <thead>
            <tr>
              <th>Corte</th>
              <th>Realizo</th>
              <th>Cantidad</th>
              <th>Fecha</th>
              <th>Estatus</th>
            </tr>
          </thead>
          <tbody>
            <?php
              $deudas = mysqli_query($conn, "SELECT * FROM  deudas_cortes WHERE cobrador = $id");
              $aux = mysqli_num_rows($deudas);
              if ($aux > 0) {
                while ($resultados = mysqli_fetch_array($deudas)) {
                  $id_corte = $resultados['id_corte'];
                  $Corte = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM cortes WHERE id_corte = '$id_corte'"));
            ?>
            <tr>
              <td><b><?php echo $id_corte;?></b></td>         
              <td><?php echo $Corte['realizo'];?></td>
              <td>$<?php echo $resultados['cantidad'];?></td>
              <td><?php echo $Corte['fecha'];?></td>
              <td><?php echo ($resultados['liquidada'] == 1)?'<span class="new badge green" data-badge-caption="Liquidada"></span>':'<span class="new badge red" data-badge-caption="Pendiente"></span>';?></td>
            </tr>
            <?php 
             }//fin while
            }else{
              echo "<center><b><h5>Este cliente aún no ha registrado Deudas</h5></b></center>";
            }
            ?>
          </tbody>
        </table>
      </div>
      <div class="col s12 m6 l6">
        <h4>Abonos: </h4>
        <table >
          <thead>
            <tr>
              <th>Id Abono</th>
              <th>Cantidad</th>
              <th>Fecha</th>
              <th>Descripcion</th>
              <th>Usuario</th>
            </tr>
          </thead>
          <tbody>
           <?php
              $abonos = mysqli_query($conn, "SELECT * FROM pagos WHERE id_cliente = $id AND tipo = 'Abono Corte'");
              $aux = mysqli_num_rows($abonos);
              if ($aux > 0) {
                while ($resultados = mysqli_fetch_array($abonos)) {
                  $id_user = $resultados['id_user'];
                  $user = mysqli_fetch_array(mysqli_query($conn, "SELECT user_name FROM users WHERE user_id = '$id_user'"));
            ?>
            <tr>
              <td><b><?php echo $resultados['id_pago'];?></b></td>         
              <td>$<?php echo $resultados['cantidad'];?></td>
              <td><?php echo $resultados['fecha'];?></td>
              <td><?php echo $resultados['descripcion'];?></td>
              <td><?php echo $user['user_name'];?></td>
            </tr>
            <?php 
             }//fin while
            }else{
              echo "<center><b><h5>Este cliente aún no ha registrado Abonos</h5></b></center>";
            }
            ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
<?php 
mysqli_close($conn);
?>