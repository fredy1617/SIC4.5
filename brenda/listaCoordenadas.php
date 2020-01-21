<?php
require_once 'datosbd.php';
$conexion=mysql_connect($hostname,$username,$password)or die("ocurrio un problema en la conexion a la base de datos");
mysql_select_db($database,$conexion)or die("problema conexion en la base DB");
mysql_set_charset('utf8',$conexion);

//consulta sql
$consulta=mysql_query("select * from coordenadas");

//si esta en blanco el json
if(mysql_num_rows($consulta)>0)
{
    $json=array();
    while ($fila=mysql_fetch_assoc($consulta))
    {
        $json['cabezera'][]=$fila;
    }
    print json_encode($json);
}
else{
    echo"no";
    echo $consulta;
}
mysql_close($conexion);
?>