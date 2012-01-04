<?php
add_action('init', 'create_activity');
function create_activity(){
	  $labels = array(
	    'name'              => _x('Actividades', 'post type general name'),
	    'singular_name'     => _x('Actividad', 'post type singular name'),
	    'add_new'           => _x('Agregar nueva', 'actividad'),
	    'add_new_item'      => __('Agregar Nueva Actividad'),
	    'edit_item'         => __('Editar Actividad'),
	    'new_item'          => __('Nueva Actividad'),
	    'all_items'         => __('Todas las Actividades'),
	    'view_item'         => __('Ver Actividades'),
	    'search_items'      => __('Buscar Actividades'),
	    'not_found'         => __('No se encuentran actividades'),
	    'not_found_in_trash'=> __('No hay actividades en la papelera'), 
	    'parent_item_colon' => '',
	    'menu_name'         => 'Actividades'
	  );
	$args = array(
		'labels'             => $labels,
		'public'             => true,
		'publicly_queryable' => true,
		'show_ui'            => true,
		'exclude_from_search'=> false,
		'query_var'          => true,
		'rewrite'            => array('slug','activity'),
		'capability_type'    => 'post',
		'hierarchical'       => false,
		'menu_position'      => 5,
		'supports'           => array('title', 'editor', 'comments', 'custom-fields', 'post-formats')
	);
	register_post_type('activity', $args);
}
add_action('init', 'activity_taxonomies',0);
function activity_taxonomies(){
	  $labels= array(
	    'name'              => _x('Temas', 'activity type general name'),
	    'singular_name'     => _x('Tema', 'activity type singular name'),
	    'search_items'      => __('Buscar en Temas'),
	    'all_items'         => __('Todos los Temas'),
	    'most_used_items'   => __('Temas más usados'),
	    'parent_item'       => __('Tema padre'),
	    'parent_item_colon' => __('Tema padre:'), 
	    'edit_item'         => __('Editar Tema'),
	    'update_item'       => __('Actualizar Tema'),
	    'add_new_item'      => __('Agregar nuevo Tema'),
	    'new_item_name'     => __('Nuevo Tema'),
	    'menu_name'         => __('Temas')
	  );

	register_taxonomy('theme','activity', array(
		'hierarchical' => true,
		'labels'       => $labels,
		'show-ui'      => true,
		'query-var'    => true,
		'rewrite'      => array('slug', 'theme')
	));
	$labels= array(
	    'name'              => _x('Participantes', 'participant type general name'),
	    'singular_name'     => _x('Participante', 'participant type singular name'),
	    'search_items'      => __('Buscar en Participantes'),
	    'all_items'         => __('Todos los Participantes'),
	    'most_used_items'   => __('Participantes más usados'),
	    'parent_item'       => null,
	    'parent_item_colon' => null,		
	    'edit_item'         => __('Editar Participante'),
	    'update_item'       => __('Actualizar Participante'),
	    'add_new_item'      => __('Agregar nuevo Participante'),
	    'new_item_name'     => __('Nuevo Participante'),
	    'menu_name'         => __('Participantes')
	  );

	register_taxonomy('participant','activity', array(
		'hierarchical'          => false,
		'labels'                => $labels,
		'show-ui'               => true,
		'update_count_callback' => '_update_post_term_count',
		'query-var'             => true,
		'rewrite'               => array('slug', 'participant')
	));
	// Change the columns for the edit activity screen
	function change_columns( $cols ) {
		$cols = array(
			'cb'           => '<input type="checkbox" />',
			'title'        => __( 'Título',   'trans' ),
			'qh_place'     => __( 'Lugar',    'trans' ),
			'qh_date'      => __( 'Fecha',    'trans' ),
			'qh_time'      => __( 'Hora',     'trans' ),
			'qh_price'     => __( 'Precio',   'trans' ),
			'qh_organizer' => __( 'Organizador',   'trans' ),
		);
		return $cols;
	}
	add_filter( "manage_edit-activity_columns", "change_columns" );

	function custom_columns( $column, $post_id ) {
		switch ( $column ) {
		case "title":
			echo get_post_meta( $post_id, 'title', true);
			break;
		case "qh_place":
			echo get_post_meta( $post_id, 'qh_place', true);
			break;
		case "qh_date":
			echo get_post_meta( $post_id, 'qh_date', true);
			break;
		case "qh_time":
			echo get_post_meta( $post_id, 'qh_time', true);
			break;
		case "qh_price":
			echo get_post_meta( $post_id, 'qh_price', true);
			break;
		case "qh_organizer":
			echo get_post_meta( $post_id, 'qh_organizer', true);
			break;
		}
	}

	add_action( "manage_activity_posts_custom_column", "custom_columns", 10, 2 );

	// Make these columns sortable
	function sortable_columns() {
		return array(
			'title'        => 'title'       ,
			'qh_place'     => 'qh_place'    ,
			'qh_date'      => 'qh_date'     ,
			'qh_time'      => 'qh_time'     ,
			'qh_price'     => 'qh_price'    ,
			'qh_organizer' => 'qh_organizer'
		);
	}
	add_filter( "manage_edit-activity_sortable_columns", "sortable_columns" );
}
/*
// Filter the request to just give posts for the given taxonomy, if applicable.
add_action( 'restrict_manage_posts', 'taxonomy_filter_restrict_manage_posts');
function taxonomy_filter_restrict_manage_posts() {
	global $typenow;

	// If you only want this to work for your specific post type,
	// check for that $type here and then return.
	// This function, if unmodified, will add the dropdown for each
	// post type / taxonomy combination.

	//$post_types = get_post_types( array( '_builtin' => false ) );
	//if ( in_array( $typenow, $post_types ) ) {
		$filters = get_object_taxonomies( $typenow );
		foreach ( $filters as $tax_slug ) {
			$tax_obj = get_taxonomy( $tax_slug );
			wp_dropdown_categories( array(
				'show_option_all' => __('Show All '.$tax_obj->label ),
				'taxonomy' 	  => $tax_slug,
				'name' 		  => $tax_obj->name,
				'orderby' 	  => 'name',
				'selected' 	  => isset($_GET[$tax_slug])?$_GET[$tax_slug]:0,
				'hierarchical'=> $tax_obj->hierarchical,
				'show_count'  => false,
				'hide_empty'  => true
				) 
			);
		}
	//}
}

//Filtra las actividades por todas las taxonomías aplicadas
add_filter( 'parse_query', 'taxonomy_filter_post_type_request' );
function taxonomy_filter_post_type_request( $query ) {
	global $pagenow, $typenow;
	//OJO. No sé por qué solo lo hace en la página edit.php!!!!!!!!!!!!!!
	if ( 'edit.php' == $pagenow ) {
		$filters = get_object_taxonomies( $typenow );
		foreach ( $filters as $tax_slug ) {
			$var = &$query->query_vars[$tax_slug];
			if ( isset( $var ) ) {
				//echo "Buscando por id: " . $var . ", slug: " . $tax_slug;
				$term = get_term_by( 'id', $var, $tax_slug );
				//print_r($term);	
				$var = $term->slug;
			}
		}
	}
}
*/
?>

