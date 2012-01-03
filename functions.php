<?php
add_action('template_redirect', 'add_my_script');
 
function add_my_script() {
	wp_enqueue_script('my-script', content_url('themes/formatic/my-script.js'), array('jquery'), '1.0', true);
}

if ( !class_exists( 'RWMB_Taxonomy_Field' ) ) {
	class RWMB_Taxonomy_Field {

		/**
		 * Add default value for 'taxonomy' field
		 * @param $field
		 * @return array
		 */
		static function normalize_field( $field ) {
			// Default query arguments for get_terms() function
			$default_args = array(
				'hide_empty' => false
			);
			if ( !isset( $field['options']['args'] ) )
				$field['options']['args'] = $default_args;
			else
				$field['options']['args'] = wp_parse_args( $field['options']['args'], $default_args );

			// Show field as checkbox list by default
			if ( !isset( $field['options']['type'] ) )
				$field['options']['type'] = 'checkbox_list';

			// If field is shown as checkbox list, add multiple value
			if ( 'checkbox_list' == $field['options']['type'] )
				$field['multiple'] = true;

			return $field;
		}

		/**
		 * Get field HTML
		 * @param $html
		 * @param $field
		 * @param $meta
		 * @return string
		 */
		static function html( $html, $meta, $field ) {
				$idObj = get_category_by_slug('actividad'); 
				$id = $idObj->term_id;
				return wp_dropdown_categories("echo=0&child_of=$id&hide_empty=0&hierarchical=1");

		}

		/**
		 * Save post taxonomy
		 * @param $post_id
		 * @param $field
		 * @param $old
		 * @param $new
		 */
		static function save( $new, $old, $post_id, $field ) {
			wp_set_post_terms( $post_id, $new, $field['options']['taxonomy'] );
		}
	}
}
/**
 * Clase para los campos de tipo location 
 */
if ( !class_exists( 'RWMB_Location_Field' ) ) {
	class RWMB_Location_Field {

		static function html( $html, $meta, $field ) {
			$val   = " value='{$meta}'";
			$name  = " name='{$field['id']}'";
			$id    = " id='{$field['id']}'";
			$html .= "<input type='text' class='rwmb-text'{$name}{$id}{$val} size='30' />";

			return $html;
		}

		/**
		 * Save post taxonomy
		 * @param $post_id
		 * @param $field
		 * @param $old
		 * @param $new
		 * Cuando salva el campo va a google para geolocalizarlo y agrega unos metas
		 * llamados qh_lat y qh_lng con las coordenadas del sitio
		 */
		static function save( $new, $old, $post_id, $field ) {
			$name = $field['id'];
			delete_post_meta( $post_id, $name );
			if ( '' === $new || array() === $new ){
				return;
			}
			if ( $field['multiple'] ) {
				foreach ( $new as $add_new ) {
					add_post_meta( $post_id, $name, $add_new, false );
				}
			}else{
				update_post_meta( $post_id, $name, $new );
			}
			$lugar = urlencode($new);
			$key="ABQIAAAAROMagOEmVvKxc67sTMKv9BT2yXp_ZAY8_ufC3CFXhHIE1NvwkxTBexYVxC2gu8hVZn7bIELZTdjrxQ";
			$url = "http://maps.google.com/maps/geo?output=json&key=".$key."&q=".$lugar;
			$ret = wp_remote_get($url);
			$datos = json_decode($ret['body']);
			update_post_meta($post_id, "qh_lat", $datos->Placemark[0]->Point->coordinates[0]);
			update_post_meta($post_id, "qh_lng", $datos->Placemark[0]->Point->coordinates[1]);
		}
	}
}

/********************* META BOXES DEFINITION ***********************/

/**
 * Prefix of meta keys (optional)
 * Wse underscore (_) at the beginning to make keys hidden
 * You also can make prefix empty to disable it
 */
$prefix = 'qh_';


// Agrego $getTermParms para que busque solo los tipos de actividad:
// Luego se la mando como parámetro al campo de tipo taxonomy
$idObj = get_category_by_slug('actividad'); 
$id = $idObj->term_id;
$getTermParms = array(
	'child_of'    =>$id,
	'hide_empty'  =>0,
	'hierarchical'=>1
);
////////////////////////////////////////////////////////////

$meta_boxes = array( );
/*
	   qh-date
	   qh-time
	   qh-price
	   qh-map-center
	   qh-map-radio
	   qh-organizer
	   categories
	   participants 
*/

// First meta box
$meta_boxes[] = array(
	'id' => 'activity-info',              // Meta box id, unique per meta box
	'title' => 'Información',             // Meta box title
	'pages' => array('post'),            // Post types, accept custom post types as well, default is array('post'); optional
	'context' => 'normal',                // Where the meta box appear: normal (default), advanced, side; optional
	'priority' => 'high',                 // Order of meta box: high (default), low; optional

	'fields' => array(                    // List of meta fields
		array(
			'name' => 'Lugar',          // Field name
			'desc' => 'Dónde se realiza', // Field description, optional
			'id' => $prefix . 'place',      // Field id, i.e. the meta key
			'type' => 'location',               // Field type: text box
			'std' => ''                   // Default value, optional
		),
		array(
			'name' => 'Fecha',
			'id' => $prefix . 'date',
			'type' => 'date',               // File type: date
			'format' => 'dd-mm-yy'          // Date format, default yy-mm-dd. Optional. See: http://goo.gl/po8vf
		),
		array(
			'name' => 'Hora',
			'id' => $prefix . 'time',
			'type' => 'time',                // Field type: time
			'format' => 'hh:mm:ss'           // Time format, default hh:mm. Optional. See: http://goo.gl/hXHWz
		),
		array(
			'name' => 'Organizador',
			'id' => $prefix . 'organizer',
			'type' => 'text',               // File type: date
			'std' => ''                   // Default value, optional
		),
		array(
			'name' => 'Precio',
			'id' => $prefix . 'price',
			'type' => 'text',               // File type: date
			'std' => '0'          // Date format, default yy-mm-dd. Optional. See: http://goo.gl/po8vf
		),
		array(
			'name' => 'Categories',
			'id' => $prefix . 'cats',
			'type' => 'taxonomy',           // File type: taxonomy
			'options' => array(
				'taxonomy' => 'category',   // Taxonomy name
				'type' => 'checkbox_list',  // How to show taxonomy: 'checkbox_list' (default) or 'select'. Optional
				'args' => array( ),         // Additional arguments for get_terms() function
			),
			'desc' => 'Choose One Category'
		)
	)
);

/**
 * Register meta boxes
 * Make sure there's no errors when the plugin is deactivated or during upgrade
 */
if ( class_exists( 'RW_Meta_Box' ) ) {
	foreach ( $meta_boxes as $meta_box ) {
		new RW_Meta_Box( $meta_box );
	}
}
?>
