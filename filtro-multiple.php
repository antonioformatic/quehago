<h2>Filtro por fecha<h2>
<?php
	 $current_user = wp_get_current_user();
	 $user_id = $current_user->ID;
	 $filter = array();
	//Si recibo datos del formulario actualizo el metadata qh_filter 
	if ((isset($_POST)){
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
			$filter[0]['mapSize']   = $_POST['mapSize'];
		}
		if(isset($_POST['categoriesFilter'])){
			$filter[0]['categories'] = $_POST['categories'];
		}
		if(isset($_POST['participantsFilter'])){
			$filter[0]['participants'] = $_POST['participants'];
		}
		update_user_meta($user_id, "qh_filter", $filter); 
	}else{
		//Si no lo recibo cargo datos desde el metadata qh_filter 
		$filter = get_user_meta( $user_id, "qh_filter"); 
	}
?>
<div id="dateFilter">
	<form action="" method="post">
		<input type="text" name="dateMin" value="<?php echo $filter[0]['dateMin']; ?>" />
		<input type="text" name="dateMax" value="<?php echo $filter[0]['dateMax']; ?>" />
		<input type="submit" value="Filtrar" />
		<input type="hidden" value="dateFilter" />
	</form>
</div>
<div id="timeFilter">
	<form action="" method="post">
		<input type="text" name="timeMin" value="<?php echo $filter[0]['timeMin']; ?>" />
		<input type="text" name="timeMax" value="<?php echo $filter[0]['timeMax']; ?>" />
		<input type="submit" value="Filtrar" />
		<input type="hidden" value="timeFilter" />
	</form>
</div>
<div id="priceFilter">
	<form action="" method="post">
		<input type="text" name="priceMin" value="<?php echo $filter[0]['priceMin']; ?>" />
		<input type="text" name="priceMax" value="<?php echo $filter[0]['priceMax']; ?>" />
		<input type="submit" value="Filtrar" />
		<input type="hidden" value="priceFilter" />
	</form>
</div>
<div id="mapFilter">
	<form action="" method="post">
		<input type="text" name="mapCenter" value="<?php echo $filter[0]['mapCenter']; ?>" />
		<input type="text" name="mapRadio"  value="<?php echo $filter[0]['mapRadio']; ?>" />
		<input type="submit" value="Filtrar" />
		<input type="hidden" value="mapFilter" />
	</form>
</div>
<div id="organizerFilter">
	<form action="" method="post">
		<input type="text" name="organizer" value="<?php echo $filter[0]['organizer']; ?>" />
		<input type="submit" value="Filtrar" />
		<input type="hidden" value="organizerFilter" />
	</form>
</div>
<div id="categoriesFilter">
	<form action="" method="post">
		<input type="text" name="categories" value="<?php echo $filter[0]['categories']; ?>" />
		<input type="submit" value="Filtrar" />
		<input type="hidden" value="categoriesFilter" />
	</form>
</div>
<div id="participantsFilter">
	<form action="" method="post">
		<input type="text" name="participants" value="<?php echo $filter[0]['participants']; ?>" />
		<input type="submit" value="Filtrar" />
		<input type="hidden" value="participantsFilter" />
	</form>
</div>

