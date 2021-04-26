<?php
$array = array('172.16.12.101','172.16.189.120');
foreach ($array as &$valor) {
    if ($IP_M == $valor){
    	$filtro = true;
    }else{
    	$filtro = false;
    }
}
?>