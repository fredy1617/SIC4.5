<?php
$array = array('172.16.12.101','alguna ip mas');
foreach ($array as &$valor) {
    if ($IP_M == $valor){
    	$filtro = true;
    }else{
    	$filtro = false;
    }
}
?>