<?php
require('autoload.php'); //Nota: si renombraste la carpeta a algo diferente de "ticket" cambia el nombre en esta línea
use Mike42\Escpos\Printer;
use Mike42\Escpos\EscposImage;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
 
/*
	Este ejemplo imprime un
	ticket de venta desde una impresora térmica
*/
 
 
/*
	Una pequeña clase para
	trabajar mejor con
	los productos
	Nota: esta clase no es requerida, puedes
	imprimir usando puro texto de la forma
	que tú quieras
*/
class Producto{
 
	public function __construct($nombre, $precio, $cantidad){
		$this->nombre = $nombre;
		$this->precio = $precio;
		$this->cantidad = $cantidad;
	}
}
 
/*
	Vamos a simular algunos productos. Estos
	podemos recuperarlos desde $_POST o desde
	cualquier entrada de datos. Yo los declararé
	aquí mismo
*/
 
$productos = array(
		new Producto("Papas fritas", 10, 1),
		new Producto("Pringles", 22, 2),
		/*
			El nombre del siguiente producto es largo
			para comprobar que la librería
			bajará el texto por nosotros en caso de
			que sea muy largo
		*/
		new Producto("Galletas saladas con un sabor muy salado y un precio excelente", 10, 1.5),
	);
 
/*
	Aquí, en lugar de "POS-58" (que es el nombre de mi impresora)
	escribe el nombre de la tuya. Recuerda que debes compartirla
	desde el panel de control
*/
 
$nombre_impresora = "EPSON"; 
 
 
$connector = new WindowsPrintConnector($nombre_impresora);
$printer = new Printer($connector);
 
 
/*
	Vamos a imprimir un logotipo
	opcional. Recuerda que esto
	no funcionará en todas las
	impresoras
 
	Pequeña nota: Es recomendable que la imagen no sea
	transparente (aunque sea png hay que quitar el canal alfa)
	y que tenga una resolución baja. En mi caso
	la imagen que uso es de 250 x 250
*/
 
# Vamos a alinear al centro lo próximo que imprimamos
$printer->setJustification(Printer::JUSTIFY_CENTER);
 
/*
	Intentaremos cargar e imprimir
	el logo
*/
try{
	$logo = EscposImage::load("logo.png", false);
    $printer->bitImage($logo);
}catch(Exception $e){/*No hacemos nada si hay error*/}
 
/*
	Ahora vamos a imprimir un encabezado
*/
 
$printer->text("Yo voy en el encabezado" . "\n");
$printer->text("Otra linea" . "\n");
#La fecha también
$printer->text(date("Y-m-d H:i:s") . "\n");
 
 
/*
	Ahora vamos a imprimir los
	productos
*/
 
# Para mostrar el total
$total = 0;
foreach ($productos as $producto) {
	$total += $producto->cantidad * $producto->precio;
 
	/*Alinear a la izquierda para la cantidad y el nombre*/
	$printer->setJustification(Printer::JUSTIFY_LEFT);
    $printer->text($producto->cantidad . "x" . $producto->nombre . "\n");
 
    /*Y a la derecha para el importe*/
    $printer->setJustification(Printer::JUSTIFY_RIGHT);
    $printer->text(' $' . $producto->precio . "\n");
}
 
/*
	Terminamos de imprimir
	los productos, ahora va el total
*/
$printer->text("--------\n");
$printer->text("TOTAL: $". $total ."\n");
 
 
/*
	Podemos poner también un pie de página
*/
$printer->text("Muchas gracias por su compra\nparzibyte.me");
 
 
 
/*Alimentamos el papel 3 veces*/
$printer->feed(3);
 
/*
	Cortamos el papel. Si nuestra impresora
	no tiene soporte para ello, no generará
	ningún error
*/
$printer->cut();
 
/*
	Por medio de la impresora mandamos un pulso.
	Esto es útil cuando la tenemos conectada
	por ejemplo a un cajón
*/
$printer->pulse();
 
/*
	Para imprimir realmente, tenemos que "cerrar"
	la conexión con la impresora. Recuerda incluir esto al final de todos los archivos
*/
$printer->close();
?>