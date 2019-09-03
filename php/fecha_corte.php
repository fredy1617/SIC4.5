<?php
date_default_timezone_set('America/Mexico_City');
$Fecha = date('Y-m-d');
$nuevafecha = strtotime('+1 month', strtotime($Fecha));
$FechaCorte = date('Y-m-d', $nuevafecha);
echo $FechaCorte;

?>