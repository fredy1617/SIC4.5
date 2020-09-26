<!DOCTYPE html>
<html>
<head>
	<title>SIC | Saldo Cobrador</title>
<?php
  #INCLUIMOS EL ARCHIVO DONDE ESTA LA BARRA DE NAVEGACION DEL SISTEMA
  include('fredyNav.php');
  #INCLUIMOS EL ARCHIVO EL CUAL HACE CONEXION A LA BASE DE DATOS DEL SISTEMA
  include('../php/conexion.php');
  ?>
<script>
  //FUNCION PARA MANDAR LA INFORMACION DEL FORMULARIO DE ABONO A EL ARCHIVO PHP QUE HARA LA INSERCION DEL MISMO
  function insert_abono(){    
    var textoCantidad = $("input#cantidad").val();
    var textoDescripcion = $("input#descripcion").val();
    var textoId = $("input#id").val();
    //VERIFICAMOS SI SE SELECCIONO EL CHECK DE BANCO
    if(document.getElementById('banco').checked==true){
      textoTipo_Campio = "Banco";
    }else{
      textoTipo_Campio = "Efectivo";
    }

    if (textoCantidad == "" || textoCantidad ==0) {
      M.toast({html:"El campo Cantidad se encuentra vacío o en 0.", classes: "rounded"});
    }else{
      $.post("../php/insert_abono_corte.php", { 
          valorTipo_Campio: textoTipo_Campio,
          valorCantidad: textoCantidad,
          valorDescripcion: textoDescripcion,
          valorId: textoId,
        }, function(mensaje) {
            $("#mostrar_abonos").html(mensaje);
              
        });
      }
  }
</script>
</head>
<?php
#VERIDICAMOS SI ESTAMOS RECIBIENDO ALGUN VALOR CON EL METODO POST EN LA VARIABLE id
if (isset($_POST['id']) == false) {
  ?>
  <script>    
    function atras() {
      //SI NO RECIBIMOS NINGUN VALOR EN id MOSTRAR ALERTA Y REDIRECCIONAR A LA LISTA DE COBRADORES
      M.toast({html: "Regresando a cobradores.", classes: "rounded"})
      setTimeout("location.href='cobradores_list.php'", 1000);
    }
    atras();
  </script>
  <?php
}else{
$id = $_POST['id'];//SI SI ESTAMOS RECIBIENDO EL VALOR DE id LO GUARDAMOS EN LA VARIABLE $id
?>
<body>
	<div class="container" id="mostrar_abonos">
    <?php 
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
</body>
<?php 
}
?>
</html>