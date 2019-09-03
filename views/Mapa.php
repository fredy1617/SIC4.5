<!DOCTYPE html>
<html>
<head>
	<title>SIC | MAPA</title>
</head>
<body>
	<button onclick="findMe()">Mostrar Ubicación</button>
	<div id="map"></div>


	<script>
		function findMe(){
			var output= document.getElementById('map');
			//Verifica si soporta la GEOLOCALIZACION.
			if (navigator.geolocation) {
				output.innerHTML = "<p> Tu navegador soporta Geolicalización</p>";
			}else{
				output.innerHTML = "<p> Tu navegador no soporta Geolicalización </p>";
			}
			//Obtenemos la longitud y latitud.
			function locatizacion(posicion){
				var latitude = posicion.coords.latitude;
				var longitude = posicion.coords.longitude;
				output.innerHTML = "<p>Latitud: "+latitude+"<br>Longitud: "+longitude+"</p>";
			};

			function error(){
				output.innerHTML = "<p>No se pudo obtener tu ubicación</p>";
			}

			navigator.geolocation.getCurrentPosition(locatizacion,error);
		};
	</script>
</body>
</html>