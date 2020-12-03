<html>
<head>
	<title>SIC | Ordenes de Servicio</title>
<?php 
include('fredyNav.php');
include('../php/conexion.php');
include ('../php/cobrador.php');
$id_user = $_SESSION['user_id'];
?>
</head>
<main>
<body>
<div id="borrar_inst"></div>
<div id="reporte_borrar"></div>
<div class="container">
  <div class="row">
      <br><br>
      <h3 class="hide-on-med-and-down col l9">Ordenes de Servicio </h3>
      <h5 class="hide-on-large-only col s12 m9 l9">Ordenes de Servicio </h5>
        <div class="col s4 m3 l3"><br>
          <a class="waves-effect waves-light btn pink" href="../views/ordenes_pendientes.php"><i class="material-icons prefix right">send</i>Pendientes</a>
        </div>        
    </div>
  <?php
  #<!-- ************  VISTA PARA REDES Y ADMINISTRADORES  ****************** -->

  #VERIFICAMOS QUE EL USUARIO LOGEADO PERTENEZCA A LOS SUPER ADMINISTRADORES O SEA DEL DEPARTAMENTO DE REDES
  if ((($id_user == 49 OR $id_user == 10 OR $id_user == 56) AND $area['area'] == "Administrador") OR $area['area'] == 'Redes' OR $id_user == 25 OR $id_user == 28) {
    #SI SI PERTENECE MOSTRAR TODAS LAS ORDENES SEPARADAS POR DEPARTAMENTO
  ?>
    <div class="row">
      <h4 class="hide-on-med-and-down col l9">Redes Ordenes Pendientes</h4>
      <h5 class="hide-on-large-only col s12 m9 l9">Redes Ordenes Pendientes</h5>
      <?php 
      #CONTENIDO DE REDES
      $sql_orden = mysqli_query($conn,"SELECT * FROM orden_servicios  WHERE  estatus IN ('PorConfirmar', 'Revisar', 'Realizar')  AND dpto = 1 ORDER BY fecha");

      include ('../php/tabla_ordenes_pendientes.php');
      ?>
    </div>
  <?php } //CIERRA IF 
 
  #<!-- *************  VISTA PARA TALLER Y ADMINISTRADORES  **************** -->

  #VERIFICAMOS QUE EL USUARIO LOGEADO PERTENEZCA A LOS SUPER ADMINISTRADORES O SEA DEL DEPARTAMENTO DE TALLER
  if ((($id_user == 49 OR $id_user == 10 OR $id_user == 56) AND $area['area'] == "Administrador") OR $area['area'] == 'Taller') {
    #SI SI PERTENECE MOSTRAR TODAS LAS ORDENES SEPARADAS POR DEPARTAMENTO
  ?>
    <div class="row">
      <h4 class="hide-on-med-and-down col l9">Taller Ordenes Pendientes</h4>
      <h5 class="hide-on-large-only col s12 m9 l9">Taller Ordenes Pendientes</h5>
      <?php 
        #CONTENIDO TALLER
      $sql_orden = mysqli_query($conn,"SELECT * FROM orden_servicios  WHERE  estatus IN ('PorConfirmar', 'Revisar', 'Realizar')  AND dpto = 2 ORDER BY fecha");

      include ('../php/tabla_ordenes_pendientes.php');
      ?>
    </div>
  <?php } //CIERRA IF 

  #<!-- *************  VISTA PARA VENTAS Y ADMINISTRADORES  **************** -->

  #VERIFICAMOS QUE EL USUARIO LOGEADO PERTENEZCA A LOS SUPER ADMINISTRADORES O SEA DEL DEPARTAMENTO DE VENTAS
  if ((($id_user == 49 OR $id_user == 10 OR $id_user == 56) AND $area['area'] == "Administrador") OR $id_user == 59 OR $id_user == 66 OR $id_user == 70) {
    #SI SI PERTENECE MOSTRAR TODAS LAS ORDENES SEPARADAS POR DEPARTAMENTO
  ?>
    <div class="row">
      <h4 class="hide-on-med-and-down col l9">Ventas Ordenes Pendientes</h4>
      <h5 class="hide-on-large-only col s12 m9 l9">Ventas Ordenes Pendientes</h5>
      <?php 
        #CONTENIDO DE VENTAS
      $sql_orden = mysqli_query($conn,"SELECT * FROM orden_servicios  WHERE  estatus IN ('PorConfirmar', 'Cotizar', 'Cotizado', 'Pedir') ORDER BY fecha");

      include ('../php/tabla_ordenes_pendientes.php');
      ?>
    </div>
  <?php } //CIERRA IF ?>
<br>
</body>
</main>
</html>