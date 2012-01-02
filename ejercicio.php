<?php
/**
Made with the help of a tutorial at WPShout.com => http://wpshout.com.

Courtesy of the Hybrid theme - themehybrid.com

 * Adds the Hybrid Settings meta box on the Write Post/Page screeens
 *
 * @package Hybrid
 * @subpackage Admin
 */

/* Add a new meta box to the admin menu. */
	add_action( 'admin_menu', 'hybrid_create_meta_box' );

/* Saves the meta box data. */
	add_action( 'save_post', 'hybrid_save_meta_data' );

/**
 * Function for adding meta boxes to the admin.
 * Separate the post and page meta boxes.
 *
 * @since 0.3
 */
function hybrid_create_meta_box() {
	global $theme_name;

	add_meta_box( 'post-meta-boxes', __('Post options'), 'post_meta_boxes', 'post', 'normal', 'high' );
	add_meta_box( 'page-meta-boxes', __('Post options'), 'page_meta_boxes', 'page', 'normal', 'high' );
}

/**
 * Array of variables for post meta boxes.  Make the 
 * function filterable to add options through child themes.
 *
 * @since 0.3
 * @return array $meta_boxes
 */
function hybrid_post_meta_boxes() {

	/* Array of the meta box options. */
	$meta_boxes = array(
		'title' => array( 'name' => 'Title', 'title' => __('Title', 'hybrid'), 'type' => 'text' ),
		'description' => array( 'name' => 'Description', 'title' => __('Description', 'hybrid'), 'type' => 'textarea' ),
		'image' => array( 'name' => 'Image', 'title' => __('Image:', 'hybrid'), 'type' => 'text' ),
		'featured' => array( 'name' => 'Featured', 'title' => __('Featured img:', 'hybrid'), 'type' => 'text' ),
		

	);

	return apply_filters( 'hybrid_post_meta_boxes', $meta_boxes );
}

/**
 * Array of variables for page meta boxes.  Make the 
 * function filterable to add options through child themes.
 *
 * @since 0.3
 * @return array $meta_boxes
 */
function hybrid_page_meta_boxes() {

	/* Array of the meta box options. */
	$meta_boxes = array(
		'title' => array( 'name' => 'Title', 'title' => __('Title', 'hybrid'), 'type' => 'text' ),
		'description' => array( 'name' => 'Description', 'title' => __('Description', 'hybrid'), 'type' => 'textarea' ),

	);

	return apply_filters( 'hybrid_page_meta_boxes', $meta_boxes );
}

/**
 * Displays meta boxes on the Write Post panel.  Loops 
 * through each meta box in the $meta_boxes variable.
 * Gets array from hybrid_post_meta_boxes().
 *
 * @since 0.3
 */
function post_meta_boxes() {
	global $post;
	$meta_boxes = hybrid_post_meta_boxes(); ?>

	<table class="form-table">
	<?php foreach ( $meta_boxes as $meta ) :

		$value = get_post_meta( $post->ID, $meta['name'], true );

		if ( $meta['type'] == 'text' )
			get_meta_text_input( $meta, $value );
		elseif ( $meta['type'] == 'textarea' )
			get_meta_textarea( $meta, $value );
		elseif ( $meta['type'] == 'select' )
			get_meta_select( $meta, $value );

	endforeach; ?>
	</table>
<?php
}

/**
 * Displays meta boxes on the Write Page panel.  Loops 
 * through each meta box in the $meta_boxes variable.
 * Gets array from hybrid_page_meta_boxes()
 *
 * @since 0.3
 */
function page_meta_boxes() {
	global $post;
	$meta_boxes = hybrid_page_meta_boxes(); ?>

	<table class="form-table">
	<?php foreach ( $meta_boxes as $meta ) :

		$value = stripslashes( get_post_meta( $post->ID, $meta['name'], true ) );

		if ( $meta['type'] == 'text' )
			get_meta_text_input( $meta, $value );
		elseif ( $meta['type'] == 'textarea' )
			get_meta_textarea( $meta, $value );
		elseif ( $meta['type'] == 'select' )
			get_meta_select( $meta, $value );

	endforeach; ?>
	</table>
<?php
}

/**
 * Outputs a text input box with arguments from the 
 * parameters.  Used for both the post/page meta boxes.
 *
 * @since 0.3
 * @param array $args
 * @param array string|bool $value
 */
function get_meta_text_input( $args = array(), $value = false ) {

	extract( $args ); ?>

	<tr>
		<th style="width:10%;">
			<label for="<?php echo $name; ?>"><?php echo $title; ?></label>
		</th>
		<td>
			<input type="text" name="<?php echo $name; ?>" id="<?php echo $name; ?>" value="<?php echo wp_specialchars( $value, 1 ); ?>" size="30" tabindex="30" style="width: 97%;" />
			<input type="hidden" name="<?php echo $name; ?>_noncename" id="<?php echo $name; ?>_noncename" value="<?php echo wp_create_nonce( plugin_basename( __FILE__ ) ); ?>" />
		</td>
	</tr>
	<?php
}

/**
 * Outputs a select box with arguments from the 
 * parameters.  Used for both the post/page meta boxes.
 *
 * @since 0.3
 * @param array $args
 * @param array string|bool $value
 */
function get_meta_select( $args = array(), $value = false ) {

	extract( $args ); ?>

	<tr>
		<th style="width:10%;">
			<label for="<?php echo $name; ?>"><?php echo $title; ?></label>
		</th>
		<td>
			<select name="<?php echo $name; ?>" id="<?php echo $name; ?>">
			<?php foreach ( $options as $option ) : ?>
				<option <?php if ( htmlentities( $value, ENT_QUOTES ) == $option ) echo ' selected="selected"'; ?>>
					<?php echo $option; ?>
				</option>
			<?php endforeach; ?>
			</select>
			<input type="hidden" name="<?php echo $name; ?>_noncename" id="<?php echo $name; ?>_noncename" value="<?php echo wp_create_nonce( plugin_basename( __FILE__ ) ); ?>" />
		</td>
	</tr>
	<?php
}

/**
 * Outputs a textarea with arguments from the 
 * parameters.  Used for both the post/page meta boxes.
 *
 * @since 0.3
 * @param array $args
 * @param array string|bool $value
 */
function get_meta_textarea( $args = array(), $value = false ) {

	extract( $args ); ?>

	<tr>
		<th style="width:10%;">
			<label for="<?php echo $name; ?>"><?php echo $title; ?></label>
		</th>
		<td>
			<textarea name="<?php echo $name; ?>" id="<?php echo $name; ?>" cols="60" rows="4" tabindex="30" style="width: 97%;"><?php echo wp_specialchars( $value, 1 ); ?></textarea>
			<input type="hidden" name="<?php echo $name; ?>_noncename" id="<?php echo $name; ?>_noncename" value="<?php echo wp_create_nonce( plugin_basename( __FILE__ ) ); ?>" />
		</td>
	</tr>
	<?php
}

/**
 * Loops through each meta box's set of variables.
 * Saves them to the database as custom fields.
 *
 * @since 0.3
 * @param int $post_id
 */
function hybrid_save_meta_data( $post_id ) {
	global $post;

	if ( 'page' == $_POST['post_type'] )
		$meta_boxes = array_merge( hybrid_page_meta_boxes() );
	else
		$meta_boxes = array_merge( hybrid_post_meta_boxes() );

	foreach ( $meta_boxes as $meta_box ) :

		if ( !wp_verify_nonce( $_POST[$meta_box['name'] . '_noncename'], plugin_basename( __FILE__ ) ) )
			return $post_id;

		if ( 'page' == $_POST['post_type'] && !current_user_can( 'edit_page', $post_id ) )
			return $post_id;

		elseif ( 'post' == $_POST['post_type'] && !current_user_can( 'edit_post', $post_id ) )
			return $post_id;

		$data = stripslashes( $_POST[$meta_box['name']] );

		if ( get_post_meta( $post_id, $meta_box['name'] ) == '' )
			add_post_meta( $post_id, $meta_box['name'], $data, true );

		elseif ( $data != get_post_meta( $post_id, $meta_box['name'], true ) )
			update_post_meta( $post_id, $meta_box['name'], $data );

		elseif ( $data == '' )
			delete_post_meta( $post_id, $meta_box['name'], get_post_meta( $post_id, $meta_box['name'], true ) );

	endforeach;
}
?>
