<?php
/**
 * Add field type: 'taxonomy'
 *
 * Note: The class name must be in format "RWMB_{$field_type}_Field"
 */
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
			global $post;

			$options = $field['options'];

			$meta = wp_get_post_terms( $post->ID, $options['taxonomy'], array( 'fields' => 'ids' ) );
			$meta = is_array( $meta ) ? $meta : ( array ) $meta;
			$terms = get_terms( $options['taxonomy'], $options['args'] );

			$html = '';
			// Checkbox_list
			if ( 'checkbox_list' == $options['type'] ) {
				foreach ( $terms as $term ) {
					$html .= "<input type='checkbox' name='{$field['id']}[]' value='{$term->term_id}'" . checked( in_array( $term->term_id, $meta ), true, false ) . " /> {$term->name}<br/>";
				}
			}
			// Select
			else {
				$html .= "<select name='{$field['id']}" . ( $field['multiple'] ? "[]' multiple='multiple' style='height: auto;'" : "'" ) . ">";
				foreach ( $terms as $term ) {
					$html .= "<option value='{$term->term_id}'" . selected( in_array( $term->term_id, $meta ), true, false ) . ">{$term->name}</option>";
				}
				$html .= "</select>";
			}

			return $html;
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
		 * Cuando salva el campo va a google para geolocalizarlo y agrega un meta
		 * llamado qh_position con las coordenadas del sitio
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
			update_post_meta($post_id, "qh_position", $datos->Placemark[0]->Point->coordinates);
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
	'pages' => array( 'post'),            // Post types, accept custom post types as well, default is array('post'); optional
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
			'name' => 'Fecha',
			'id' => $prefix . 'date',
			'type' => 'date',               // File type: date
			'format' => 'dd-mm-yy'          // Date format, default yy-mm-dd. Optional. See: http://goo.gl/po8vf
		),
		array(
			'name' => 'Categorías',
			'id' => $prefix . 'categories',
			'type' => 'taxonomy',           // File type: taxonomy
			'options' => array(
				'taxonomy' => 'category',   // Taxonomy name
				'type' => 'checkbox_list',  // How to show taxonomy: 'checkbox_list' (default) or 'select'. Optional
				'args' => $getTermParms     // Additional arguments for get_terms() function
			),
			'desc' => 'Categorías a las que pertenece'
		),
		array(
			'name' => 'Hora',
			'id' => $prefix . 'time',
			'type' => 'time',                // Field type: time
			'format' => 'hh:mm:ss'           // Time format, default hh:mm. Optional. See: http://goo.gl/hXHWz
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
