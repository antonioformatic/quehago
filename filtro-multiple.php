<?php
	$current_user = wp_get_current_user();
	$user_id = $current_user->ID;
	$filter = get_user_meta( $user_id, "qh_filter", true); 
	//Si recibo datos del formulario actualizo el metadata qh_filter 
	if (isset($_POST)){
		if(isset($_POST['dateFilter'])){
			$filter[0]['dateMin'] = $_POST['dateMin'];
			$filter[0]['dateMax'] = $_POST['dateMax'];
		}
		if(isset($_POST['timeFilter'])){
			$filter[0]['timeMin'] = $_POST['timeMin'];
			$filter[0]['timeMax'] = $_POST['timeMax'];
		}
		if(isset($_POST['priceFilter'])){
			$filter[0]['priceMin'] = $_POST['priceMin'];
			$filter[0]['priceMax'] = $_POST['priceMax'];
		}
		if(isset($_POST['mapFilter'])){
			$filter[0]['mapCenter'] = $_POST['mapCenter'];
			$filter[0]['mapRadio']   = $_POST['mapRadio'];
			if($_POST['mapCenter'] != ''){
				$lugar = urlencode($_POST['mapCenter']);
				$key="ABQIAAAAROMagOEmVvKxc67sTMKv9BT2yXp_ZAY8_ufC3CFXhHIE1NvwkxTBexYVxC2gu8hVZn7bIELZTdjrxQ";
				$url = "http://maps.google.com/maps/geo?output=json&key=".$key."&q=".$lugar;
				$ret = wp_remote_get($url);
				$datos = json_decode($ret['body']);
				$filter[0]['mapLat'] =$datos->Placemark[0]->Point->coordinates[0]; 
				$filter[0]['mapLng'] =$datos->Placemark[0]->Point->coordinates[1]; 
			}else{
				$filter[0]['mapLat'] =0; 
				$filter[0]['mapLng'] =0; 
			}
		}
		if(isset($_POST['categoriesFilter'])){
			$filter[0]['categories'] = $_POST['categories'];
		}
		if(isset($_POST['organizerFilter'])){
			$filter[0]['organizer'] = $_POST['organizer'];
		}
		if(isset($_POST['participantsFilter'])){
			$filter[0]['participants'] = $_POST['participants'];
		}
		update_user_meta($user_id, "qh_filter", $filter); 
	}
?>
<?php
$current_user = wp_get_current_user();
$user_id = $current_user->ID;
$filter = get_user_meta( $user_id, "qh_filter", true); 

global $meta_query;
global $tax_query;

// Filtros para los metadatos:
if($filter != ''){
	if($filter[0]['dateMin'] != ''){
		$meta_query[] = array( 
			'key'     => 'qh_fecha',
			'value'   => $filter[0]['dateMin'],
			'type'    => 'DATE',
			'compare' => '>='
		);
	}
	if($filter[0]['dateMax'] != ''){
		$meta_query[] = array(
			'key'     => 'qh_fecha',
			'value'   => $filter[0]['dateMax'],
			'type'    => 'DATE',
			'compare' => '<='
		);
	}
	if($filter[0]['timeMin'] != ''){
		$meta_query[] = array( 
			'key'     => 'qh_hora',
			'value'   => $filter[0]['timeMin'],
			'type'    => 'TIME',
			'compare' => '>='
		);
	}
	if($filter[0]['timeMax'] != ''){
		$meta_query[] = array(
			'key'     => 'qh_hora',
			'value'   => $filter[0]['timeMax'],
			'type'    => 'TIME',
			'compare' => '<='
		);
	}
	if($filter[0]['priceMin'] != 0){
		$meta_query[] = array( 
			'key'     => 'qh_price',
			'value'   => $filter[0]['priceMin'],
			'type'    => 'NUMERIC',
			'compare' => '>='
		);
	}
	if($filter[0]['priceMax'] != 0){
		$meta_query[] = array(
			'key'     => 'qh_price',
			'value'   => $filter[0]['priceMax'],
			'type'    => 'NUMERIC',
			'compare' => '<='
		);
	}
	if($filter[0]['organizer'] != ''){
		$meta_query[] = array(
			'key'     => 'qh_organizer',
			'value'   => $filter[0]['organizer'],
			'type'    => 'TEXT',
			'compare' => '='
		);
	}


	//Filtros para las taxonomías:
//	$tax_query[0][]  = array( 'relation' => 'AND');
	//$tax_query = array();
	if($filter[0]['categories'] != ''){
		$tax_query[] = array(
			'taxonomy' => 'category',
			'terms' => array($filter[0]['categories']),
			'field' => 'slug',
		);
	}
	if($filter[0]['participants'] != ''){
		$tax_query[] = array(
			'taxonomy' => 'participants',
			'terms' => array($filter[0]['participants']),
			'field' => 'slug',
		);
	}

	//Si filtró por lugar agregamos filtro especial de centro y distancia 
	if($filter[0]['mapCenter'] != ''){
		add_filter('posts_groupby', 'miGroupBy' );
		function miGroupBy( $groupBy) {
			$current_user = wp_get_current_user();
			$user_id = $current_user->ID;
			$filter = get_user_meta( $user_id, "qh_filter", true); 
			$radio = $filter[0]['mapRadio'];
			if($groupBy == ''){
				$groupBy = " wp_posts.ID ";
			}
			return $groupBy . " having distance <= $radio";
			
		}
		add_filter('posts_join_paged', 'miJoin' );
		function miJoin( $joins) {
			global $wpdb;
			$new_joins = array(
				"left join  ( 
				   select wp_posts.ID as id ,wp_postmeta.meta_value as lat 
				   from wp_postmeta 
				   join 
						wp_posts on post_id = wp_posts.ID 
						 where meta_key = 'qh_lat' 
				) as metaLat on metaLat.id = wp_posts.ID",
				"left join  ( 
				   select wp_posts.ID as id ,wp_postmeta.meta_value as lng 
				   from wp_postmeta 
				   join 
						wp_posts on post_id = wp_posts.ID 
						 where meta_key = 'qh_lng' 
				) as metaLng on metaLng.id = wp_posts.ID",
				$joins
			);
			return implode( ' ', $new_joins);
		}
		add_filter('posts_fields', 'miFields' );
		function miFields( $fields) {
			$current_user = wp_get_current_user();
			$user_id = $current_user->ID;
			$filter = get_user_meta( $user_id, "qh_filter", true); 
			$mapLat = $filter[0]['mapLat'];
			$mapLng = $filter[0]['mapLng'];
			$fields.=", metaLat.lat";
			$fields.=", metaLng.lng";
			$fields.=",( 
				6371
				* 
				acos( 
					cos( radians($mapLat) ) 
					* 
					cos( radians( metaLat.lat ) ) 
					* 
					cos( 
						radians( metaLng.lng ) 
						- 
						radians($mapLng) 
					) 
					+ 
					sin(radians($mapLat)) 
					* 
					sin(radians(metaLat.lat)) 
				) 
			) AS distance ";
			return $fields;
		}
	}
}
?>
<div id="filterMenu">
<button onclick='jQuery("#dateFilter").        toggle();'>Fecha</button>
<button onclick='jQuery("#timeFilter").        toggle();'>Hora</button>
<button onclick='jQuery("#priceFilter").       toggle();'>Precio</button>
<button onclick='jQuery("#mapFilter").         toggle();'>Lugar</button>
<button onclick='jQuery("#organizerFilter").   toggle();'>Organizador</button>
<button onclick='jQuery("#categoriesFilter").  toggle();'>Categoría</button>
<button onclick='jQuery("#participantsFilter").toggle();'>Participantes</button>
</div>
<div style="display:'none';">
<div id="dateFilter" style="display:none;">
	<form action="" method="post">
		Desde fecha<input type="text" class="rwmb-date" name="dateMin" value="<?php echo $filter[0]['dateMin']; ?>" />
		Hasta fecha<input type="text" class="rwmb-date" name="dateMax" value="<?php echo $filter[0]['dateMax']; ?>" />
		<input type="submit" value="Filtrar" />
		<input type="hidden" name="dateFilter" />
	</form>
</div>
<div id="timeFilter" style="display:none;">
	<form action="" method="post">
		Desde hora<input type="text" class="rwmb-time" name="timeMin" value="<?php echo $filter[0]['timeMin']; ?>" />
		Hasta hora<input type="text" class="rwmb-time" name="timeMax" value="<?php echo $filter[0]['timeMax']; ?>" />
		<input type="submit" value="Filtrar" />
		<input type="hidden" name="timeFilter" />
	</form>
</div>
<div id="priceFilter" style="display:none;">
	<form action="" method="post">
		Desde precio<input type="text" name="priceMin" value="<?php echo $filter[0]['priceMin']; ?>" />
		Hasta precio<input type="text" name="priceMax" value="<?php echo $filter[0]['priceMax']; ?>" />
		<input type="submit" value="Filtrar" />
		<input type="hidden" name="priceFilter" />
	</form>
</div>
<div id="mapFilter" style="display:none;">
	<form action="" method="post">
		Centro<input type="text" name="mapCenter" value="<?php echo $filter[0]['mapCenter']; ?>" />
		Radio<input type="text" name="mapRadio"  value="<?php echo $filter[0]['mapRadio']; ?>" />
		<input type="submit" value="Filtrar" />
		<input type="hidden" name="mapFilter" />
	</form>
</div>
<div id="organizerFilter" style="display:none;">
	<form action="" method="post">
		Organizador<input type="text" name="organizer" value="<?php echo $filter[0]['organizer']; ?>" />
		<input type="submit" value="Filtrar" />
		<input type="hidden" name="organizerFilter" />
	</form>
</div>
<div id="categoriesFilter" style="display:none;">
	<form action="" method="post">
		Categorías<input type="text" name="categories" value="<?php echo $filter[0]['categories']; ?>" />
		<input type="submit" value="Filtrar" />
		<input type="hidden" name="categoriesFilter" />
	</form>
</div>
<div id="participantsFilter" style="display:none;">
	<form action="" method="post">
		Participantes<input type="text" name="participants" value="<?php echo $filter[0]['participants']; ?>" />
		<input type="submit" value="Filtrar" />
		<input type="hidden" name="participantsFilter" />
	</form>
</div>
</div>
