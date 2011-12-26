<?php get_header(); ?>
<h1>Filtro por fecha de Actividades--------------</h1>
<?php
	//Si recibo datos del formulario actualizo el metadata qh_filtro 
	if ((isset($_POST)) && (isset($_POST['fechaDesde']) && (isset($_POST['fechaHasta'])))){
		$current_user = wp_get_current_user();
		$user_id = $current_user->ID;
		$filtro[0]['fechaDesde'] = $_POST['fechaDesde'];
		$filtro[0]['fechaHasta'] = $_POST['fechaHasta'];
		update_user_meta($user_id, "qh_filtro", $filtro); 
	}else{
		//Si no lo recibo cargo datos desde el metadata qh_filtro 
		$current_user = wp_get_current_user();
		$user_id = $current_user->ID;
		$filtro = get_user_meta( $user_id, "qh_filtro"); 
		$desde = $filtro[0]['fechaDesde'];
		$hasta = $filtro[0]['fechaHasta'];
		?>
		<form action="" method="post">
			<input type="text" name="fechaDesde" value="<?php echo $desde; ?>" />
			<input type="text" name="fechaHasta" value="<?php echo $hasta; ?>" />
			<input type="submit" value="Filtrar" />
		</form>
		<?php
	}
?>
<?php get_footer(); ?>
