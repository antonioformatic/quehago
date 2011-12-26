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
			'name' => 'Categorías',
			'id' => $prefix . 'categorias',
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
<?php
/**
 * Plugin Name: Example Widget
 * Plugin URI: http://example.com/widget
 * Description: A widget that serves as an example for developing more advanced widgets.
 * Version: 0.1
 * Author: Justin Tadlock
 * Author URI: http://justintadlock.com
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

/**
 * Add function to widgets_init that'll load our widget.
 * @since 0.1
 */
add_action( 'widgets_init', 'example_load_widgets' );

/**
 * Register our widget.
 * 'Example_Widget' is the widget class used below.
 *
 * @since 0.1
 */
function example_load_widgets() {
	register_widget( 'Example_Widget' );
}

/**
 * Example Widget class.
 * This class handles everything that needs to be handled with the widget:
 * the settings, form, display, and update.  Nice!
 *
 * @since 0.1
 */
class Example_Widget extends WP_Widget {

	/**
	 * Widget setup.
	 */
	function Example_Widget() {
		/* Widget settings. */
		$widget_ops = array( 
			'classname' => 'example', 
			'description' => __('An example widget that displays a person\'s name and sex.', 'example') 
		);

		/* Widget control settings. */
		$control_ops = array( 
			'width' => 300, 
			'height' => 350, 
			'id_base' => 'example-widget' 
		);

		/* Create the widget. */
		$this->WP_Widget( 
			'example-widget',
			 __('Example Widget', 'example'), 
			$widget_ops, 
			$control_ops 
		);
	}

	/**
	 * How to display the widget on the screen.
	 */
	function widget( $args, $instance ) {
		extract( $args );

		/* Our variables from the widget settings. */
		$title = apply_filters('widget_title', $instance['title'] );
		$name = $instance['name'];
		$sex = $instance['sex'];
		$show_sex = isset( $instance['show_sex'] ) ? $instance['show_sex'] : false;

		/* Before widget (defined by themes). */
		echo $before_widget;

		/* Display the widget title if one was input (before and after defined by themes). */
		if ( $title )
			echo $before_title . $title . $after_title;

		/* Display name from widget settings if one was input. */
		if ( $name )
			printf( '<p>' . __('Hello. My name is %1$s.', 'example') . '</p>', $name );

		/* If show sex was selected, display the user's sex. */
		if ( $show_sex )
			printf( '<p>' . __('I am a %1$s.', 'example.') . '</p>', $sex );

		/* After widget (defined by themes). */
		echo $after_widget;
		$this->form($instance);
	}

	/**
	 * Update the widget settings.
	 */
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		/* Strip tags for title and name to remove HTML (important for text inputs). */
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['name'] = strip_tags( $new_instance['name'] );

		/* No need to strip tags for sex and show_sex. */
		$instance['sex'] = $new_instance['sex'];
		$instance['show_sex'] = $new_instance['show_sex'];

		return $instance;
	}

	/**
	 * Displays the widget settings controls on the widget panel.
	 * Make use of the get_field_id() and get_field_name() function
	 * when creating your form elements. This handles the confusing stuff.
	 */
	function form( $instance ) {

		/* Set up some default widget settings. */
		$defaults = array( 'title' => __('Example', 'example'), 'name' => __('John Doe', 'example'), 'sex' => 'male', 'show_sex' => true );
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>

		<!-- Widget Title: Text Input -->
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Title:', 'hybrid'); ?></label>
			<input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" style="width:100%;" />
		</p>

		<!-- Your Name: Text Input -->
		<p>
			<label for="<?php echo $this->get_field_id( 'name' ); ?>"><?php _e('Your Name:', 'example'); ?></label>
			<input id="<?php echo $this->get_field_id( 'name' ); ?>" name="<?php echo $this->get_field_name( 'name' ); ?>" value="<?php echo $instance['name']; ?>" style="width:100%;" />
		</p>

		<!-- Sex: Select Box -->
		<p>
			<label for="<?php echo $this->get_field_id( 'sex' ); ?>"><?php _e('Sex:', 'example'); ?></label> 
			<select id="<?php echo $this->get_field_id( 'sex' ); ?>" name="<?php echo $this->get_field_name( 'sex' ); ?>" class="widefat" style="width:100%;">
				<option <?php if ( 'male' == $instance['format'] ) echo 'selected="selected"'; ?>>male</option>
				<option <?php if ( 'female' == $instance['format'] ) echo 'selected="selected"'; ?>>female</option>
			</select>
		</p>

		<!-- Show Sex? Checkbox -->
		<p>
			<input class="checkbox" type="checkbox" <?php checked( $instance['show_sex'], true ); ?> id="<?php echo $this->get_field_id( 'show_sex' ); ?>" name="<?php echo $this->get_field_name( 'show_sex' ); ?>" /> 
			<label for="<?php echo $this->get_field_id( 'show_sex' ); ?>"><?php _e('Display sex publicly?', 'example'); ?></label>
		</p>

	<?php
	}
}

?>
<?php
add_action( 'widgets_init', 'setupFiltersWidget' );

/**
 * Register our widget.
 * 'Example_Widget' is the widget class used below.
 *
 * @since 0.1
 */
function setupFiltersWidget() {
	register_widget( 'QHFilter' );
}

/**
 * Example Widget class.
 * This class handles everything that needs to be handled with the widget:
 * the settings, form, display, and update.  Nice!
 *
 * @since 0.1
 */
class QHFilter extends WP_Widget {

	function Fecha_Widget() {
		$widget_ops = array( 
			'classname' => 'example', 
			'description' => __('Filtrando datos', 'example') 
		);

		$control_ops = array( 
			'width' => 40, 
			'height' => 10, 
			'id_base' => 'filters-widget' 
		);

		/* Create the widget. */
		$this->WP_Widget( 
			'filters-widget',
			 __('Filters Widget', 'example'), 
			$widget_ops, 
			$control_ops 
		);
	}

	function widget( $args, $instance ) {
		$this->form($instance);
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['desde'] = strip_tags( $new_instance['desde'] );
		return $instance;
	}

	function form( $instance ) {
		$defaults = array( 
			'desde'      => '01/01/2011' 
		);
		$instance = wp_parse_args( (array) $instance, $defaults ); 
		?>
		<p>
			<label 
				for="<?php echo $this->get_field_id( 'desde' ); ?>">
					<?php _e('Desde:', 'hybrid'); ?>
			</label>
			<input 
				id   ="<?php echo $this->get_field_id( 'desde' );   ?>" 
				name ="<?php echo $this->get_field_name( 'desde' ); ?>" 
				value="<?php echo $instance['desde'];               ?>" 
			/>
		</p>


	<?php
	}
}

?>
