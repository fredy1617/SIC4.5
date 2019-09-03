<?php
session_start();
include('../php/conexion.php');
date_default_timezone_set('America/Mexico_City');

$sql = mysqli_query($conn, "SELECT * FROM deudas WHERE liquidada=0");
$filas = mysqli_num_rows($sql);
if ($filas <= 0) {
  echo 'No hay deudas.<br>';
}else{
  while ($deuda = mysqli_fetch_array($sql)) {
    $IdCliente = $deuda['id_cliente'];

    $Ver = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM deudas WHERE id_cliente = $IdCliente AND liquidada=0 limit 1"));
     
    // SACAMOS LA SUMA DE TODAS LAS DEUDAS QUE ESTAN LIQUIDADDAS Y TODOS LOS ABONOS ....
    $deuda = mysqli_fetch_array(mysqli_query($conn, "SELECT SUM(cantidad) AS suma FROM deudas WHERE id_cliente = $IdCliente AND liquidada = 1"));
    $abono = mysqli_fetch_array(mysqli_query($conn, "SELECT SUM(cantidad) AS suma FROM pagos WHERE id_cliente = $IdCliente AND tipo = 'Abono'"));
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
     while ($Entra) {
      if (mysqli_query($conn, "UPDATE deudas SET liquidada = 1 WHERE id_deuda = $id_deuda")) {
        echo 'Deuda liquidada.<br>';
      }  
      $Ver = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM deudas WHERE id_cliente = $IdCliente AND liquidada=0 limit 1"));
      $id_deuda = $Ver['id_deuda'];
      // SACAMOS LA SUMA DE TODAS LAS DEUDAS QUE ESTAN LIQUIDADDAS Y TODOS LOS ABONOS ....
      $deuda = mysqli_fetch_array(mysqli_query($conn, "SELECT SUM(cantidad) AS suma FROM deudas WHERE id_cliente = $IdCliente AND liquidada = 1"));
      $abono = mysqli_fetch_array(mysqli_query($conn, "SELECT SUM(cantidad) AS suma FROM pagos WHERE id_cliente = $IdCliente AND tipo = 'Abono'"));
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

  }
}

?>