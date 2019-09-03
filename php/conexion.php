<?php
	/* Declaración de variables*/
	$servername = "localhost";
	$username = "root";
	$password = "root";
	$bd = "servintcomp";

	/* Realización de la conexión, en caso de existir un error lo muestra en pantalla*/
	$conn = mysqli_connect($servername, $username, $password, $bd) or die ('Se econtró un error en la conexión a la BD');
	mysqli_set_charset($conn,"utf8"); // Esta línea tiene la función de permitir el uso de caracteres propios del idioma español, como la ñ  o el acento gráfico.
?>