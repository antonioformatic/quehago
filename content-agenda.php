<?php 
	global $meta_query;
	global $tax_query;
	$current_user = wp_get_current_user();
	$user_id = $current_user->ID;
	$tax_query[] = array(
		'taxonomy' => 'category',
		'terms' => array('agenda'),
		'field' => 'slug',
	);
	$args= array(
		'post_type'  => 'post',
		'author'     => $user_id,
		'meta_query' => $meta_query,
		'tax_query'  => $tax_query
	);
	$myQuery = new WP_Query( $args );
	if ( $myQuery->have_posts() ) 
		while ( $myQuery->have_posts() ) : $myQuery->the_post(); ?>
		<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
			<?php
				$datos = get_post_custom($post->ID);
				$newPost = get_post($datos['qh_like'][0]);
				echo "<a href=". get_permalink($newPost->ID). ">". get_the_title($newPost->ID)."<a>";
			?>
		</div><!-- #post-## -->
		<?php endwhile;?>
		<?php wp_reset_query() ?>
