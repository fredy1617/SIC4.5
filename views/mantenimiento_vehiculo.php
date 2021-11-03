<!DOCTYPE html>
<html>
<head>
  <title>SIC | Vehiculos Mantenimiento</title>
</head>
<?php
#INCLUIMOS EL ARCHIVO DONDE ESTA LA BARRA DE NAVEGACION DEL SISTEMA
include('fredyNav.php');
#INCLUIMOS EL ARCHIVO EL CUAL HACE LA CONEXION DE LA BASE DE DATOS PARA ACCEDER A LA INFORMACION DEL SISTEMA
include('../php/conexion.php');
#INCLUIMOS UN ARCHIVO QUE PROHIBE EL ACCESO A ESTA VISTA A LOS USUARIOS CON EL ROL DE COBRADOR 
include('../php/cobrador.php');
#VERIFICAMOS SI CON EL METODO POST ESTAMOS RECIBIENDO ALGUN VALOR DE LA VARIABLE id
if (isset($_POST['id']) == false) {
?>
  <script>    
    function atras() {
      M.toast({html: "Regresando a listado de vehiculos...", classes: "rounded"});
      //REDIRECCIONAMOS A LA VISTA stock.php (RETROCEDEMOS)
      setTimeout("location.href='vehiculos.php'", 1000);
    }
    atras();//SI NO RECIBIMOS NINGUN VALOR MANDAMOS LLAMAR LA FUNCION CON LA CUAL REDIRECCIONAMOS
  </script>
<?php
}else{
  #SI EL VALOR NO ESTA VACIO RECIBIMOS LA VARIABLE Y LA GUARDAMOS $id Y MOSTRAMOS TODO EL CONTENIDO DE LA PAGINA
  $id = $_POST['id'];
?>
<script>
  function borrar(id){
    var textoUnidad = <?php echo $id; ?>;

    $.post("../php/borrar_insumo.php", { 
            valorId: id,
            valorUnidad: textoUnidad
    }, function(mensaje) {
    $("#resultado_update_insumo").html(mensaje);
    }); 
  };
  //FUNCION QUE ENVIA LA INFORMACION PARA QUE SE VALLA AGREGANDO EL MANTENIMIENTO A LA UNIDAD
  function update_insumo(id) {
      var textoDescripcion = $("select#Descripcion_V").val();
      var textoFecha = $("input#fecha_a").val();

      if(textoDescripcion == 0){
        M.toast({html:"Elige una descripcion...", classes: "rounded"})
      }else if(textoFecha == ""){
        M.toast({html:"Ingrese una fecha...", classes: "rounded"})
      }else{
        $.post("../php/inster_insumo.php", {
            valorId: id,
            valorDescripcion: textoDescripcion,
            valorFecha: textoFecha
        }, function(mensaje) {
            $("#resultado_update_insumo").html(mensaje);
        });
      }
  };
</script>
<body>
  <div class="container"><br>
    <?php   
    #SELECCIONAMOS LA INFORMACION DEL TECNICO QUE SACAMOS DE LA VARIABLE QUE RECIBIMOS ID $id
    $datos = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM unidades WHERE id = $id"));
    ?>
    <div class="row">
      <ul class="collection">
            <li class="collection-item avatar">
              <!-- MOSTRAMOS LA INFORMACION DE LA UNIDAD -->
              <div class="hide-on-large-only"><br><br></div>
              <img src="../img/cliente.png" alt="" class="circle">
              <span class="title"><b>Id: </b><?php echo $id;?></span>
              <p><b>Unidad: </b><?php echo $datos['nombre'];?><br>
                <b>Descripcion:</b> <?php echo $datos['descripcion'];?><br>                  
                <b>Responsable:</b> <?php echo $datos['responsable'];?><br>       
                <hr>
                <!-- BOTON QUE SIRVE PARA VER UN HISTRORIAL DEL STOCK QUE A USARO, QUE DIA ETC. -->
                <!-- <a href="history_stock.php?id=<?php echo $id_tecnico;?>" class="waves-effect waves-light btn pink right "><i class="material-icons right">visibility</i>Historial</a><br> -->
              </p>
              <br>
            </li>
      </ul>
      
      <!--FORMULARIO QUE SIRVE PARA AGREGAR un matenimiento a la unidad -->
      <form class="col s12">
        <h3>Mantenimiento: </h3>
          <!--CONTENIDO PARA ANTENA-->
          <div class="input-field col s12 m4 l4">
            <i class="col s1"> <br></i>
            <select id="Descripcion_V" class="browser-default col s11" required>
              <option value="0" selected>Descripcion: </option>
              <option value="General">General</option>
              <option value="Aceite de motor">Aceite de motor</option>
              <option value="Aceite de transmision y transfer">Aceite de transmision y transfer</option>
              <option value="Aceite de diferencial">Aceite de diferencial</option>
              <option value="Balatas delanteras">Balatas delanteras</option>
              <option value="Balatas traseras">Balatas traseras</option>
              <option value="Cuerpo de aceleracion">Cuerpo de aceleracion</option>
              <option value="Inspeccion de de monturas de motor y transmision">Inspeccion de de monturas de motor y transmision</option>
              <option value="Inspeccion de flechas y crucetas">Inspeccion de flechas y crucetas</option>
              <option value="Inspeccion de refrigerante">Inspeccion de refrigerante</option>
            </select>
          </div> 
          <div class="input-field col s12 l4 m4">
                <input id="fecha_a" type="date" >
                <label for="fecha_a">Fecha siguiente:</label><br>
          </div><br>
        <div class="row">
            <a onclick="update_insumo(<?php echo $id;?>);" class="waves-effect waves-light btn pink right"><i class="material-icons right">add</i>Agregar</a> <br>
        </div>  
      </form> 
      <div class="row" id="resultado_update_insumo">
          <h3>Historial</h3>
          <div class="col s2"></div>
          <div class="row col s8">
          <!--CREAMOS UNA TABLA QUE MUESTRA EL HISTORIAL DE LOS MANTENIMIENTOS O INSUMOS -->
          <table class="bordered highlight responsive-table">
            <thead>
              <th>#</th>
              <th>Descripcion</th>
              <th>Fecha</th>
              <th>Registro</th>
              <th>Borrar</th>
            </thead>
            <tbody>
            <?php
            #SELECCIONAMOS TODOS LOS ROUTERS Y ANTENAS DISPONIBLES EN STOCK
            $tab = mysqli_query($conn, "SELECT * FROM insumos WHERE vehiculo = $id");
            #SI TIENE DISPONIBLES LA RECORREMOS UNA POR UNA Y MOSTRAMOS LA INFORMACION
            while($insumo = mysqli_fetch_array($tab)){
              ?>
              <tr>
                <td><?php echo $insumo['id']; ?></td>
                <td><?php echo $insumo['descripcion']?></td>
                <td><?php echo $insumo['fecha']; ?></td>
                <td><?php echo $insumo['registro']; ?></td>
                <td><a onclick="borrar(<?php echo $insumo['id'];?>);" class="btn btn-floating red darken-1 waves-effect waves-light"><i class="material-icons">delete</i></td>
              </tr>
            <?php
            }
            ?> 
            </tbody>
          </table>
          </div>
      </div>    
    </div>
  </div>
</body>
<?php
}
mysqli_close($conn);
?>
</script>
</html>