<?php
/* 
A continuación se muestra la instrucción SQL que encontrará las 20 ubicaciones más cercanas en un radio de 40,23 km 
para las coordenadas 37, -122. Calcula la distancia basada en la longitud y en la latitud de esa fila y en la latitud 
en y la longitud de destino y, a continuación, solicita solo las filas donde el valor de la distancia sea inferior a 25, 
ordena la consulta por distancia y la limita a 20 resultados. 
Para realizar la búsqueda en kilómetros en lugar de en millas, sustituye 3.959 por 6.371.
$myLat=37;
$myLng=-122;
SELECT 
	id, 
	( 
		$km 
		* 
		acos( 
			cos( radians($myLat) ) 
			* 
			cos( radians( lat ) ) 
			* 
			cos( 
				radians( lng ) 
				- 
				radians($myLng) 
			) 
			+ 
			sin(radians($myLat)) 
			* 
			sin(radians(lat)) 
		) 
	) 
AS distance FROM markers HAVING distance < 25 ORDER BY distance LIMIT 0 , 20;
*/
$km=6371;
$myLat=37;
$myLng=-122;
$maxDistance=10;
$wpdb->get_results(
"SELECT 
	id, 
	( 
		$km 
		* 
		acos( 
			cos( radians($myLat) ) 
			* 
			cos( radians( lat ) ) 
			* 
			cos( 
				radians( lng ) 
				- 
				radians($myLng) 
			) 
			+ 
			sin(radians($myLat)) 
			* 
			sin(radians(lat)) 
		) 
	) AS distance 
FROM markers 
HAVING distance < $maxDistance 
ORDER BY distance 
LIMIT 0 , 20"
);

?>
