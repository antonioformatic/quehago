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

/********************* META BOXES DEFINITION ***********************/

/**
 * Prefix of meta keys (optional)
 * Wse underscore (_) at the beginning to make keys hidden
 * You also can make prefix empty to disable it
 */
$prefix = 'qh_';

$meta_boxes = array( );

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
			'id' => $prefix . 'lugar',      // Field id, i.e. the meta key
			'type' => 'text',               // Field type: text box
			'std' => ''                   // Default value, optional
		),
		array(
			'name' => 'Fecha',
			'id' => $prefix . 'fecha',
			'type' => 'date',               // File type: date
			'format' => 'dd-mm-yy'          // Date format, default yy-mm-dd. Optional. See: http://goo.gl/po8vf
		),
		array(
			'name' => 'Real',
			'id' => $prefix . 'real',
			'type' => 'radio',              // File type: radio box
			'options' => array(             // Array of 'key' => 'value' pairs for radio options. Note: the 'key' is stored in meta field, not the 'value'
				'r' => 'Real',
				'i' => 'Imaginaria'
			),
			'std' => 'r',
			'desc' => 'Se realizará de veras o es sólo una idea?'
		),
		array(
			'name' => 'Descripción',
			'desc' => 'Descripción corta de la actividad',
			'id' => $prefix . 'descripcion',
			'type' => 'textarea',           // File type: textarea
			'std' => '',
			'style' => 'width: 200px; height: 100px'
		),
		array(
			'name' => 'Categorías',
			'id' => $prefix . 'categorias',
			'type' => 'taxonomy',           // File type: taxonomy
			'options' => array(
				'taxonomy' => 'category',   // Taxonomy name
				'type' => 'checkbox_list',  // How to show taxonomy: 'checkbox_list' (default) or 'select'. Optional
				'args' => array( ),         // Additional arguments for get_terms() function
			),
			'desc' => 'Categorías a las que pertenece'
		),
		array(
			'name' => 'Hora',
			'id' => $prefix . 'hora',
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
