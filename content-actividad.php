<?php 
	global $meta_query;
	global $tax_query;
	$tax_query[] = array(
		'taxonomy' => 'category',
		'terms' => array('actividad'),
		'field' => 'slug',
	);
	$args= array(
		'post_type'  => 'post',
		'meta_query' => $meta_query,
		'tax_query'  => $tax_query
	);
	$myQuery = new WP_Query( $args );
	if ( $myQuery->have_posts() ) while ( $myQuery->have_posts() ) : $myQuery->the_post(); ?>
	<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
		<h1 class="entry-title"><?php the_title(); ?></h1>
		<div class="entry-content">
			<?php the_content(); ?>
			<?php
				$datos = get_post_custom($post->ID);
				echo "<br />Fecha:" .$datos['qh_date'][0];
				echo "<br />Hora:" .$datos['qh_time'][0];
				echo "<br />Precio:" .$datos['qh_price'][0];
				echo "<br />Lugar:" .$datos['qh_place'][0];
				echo "<br />Organizador:" .$datos['qh_organizer'][0];
			?>
			<?php 
				echo "<br />Temas:<ul>";
				foreach((get_the_category()) as $category) { 
					echo '<li>'.$category->cat_name ."</li>"; 
				} 
				echo "</ul>";
			?>
			<?php
				$posttags = get_the_tags();
				if ($posttags) {
					echo "Participantes:<br />";
					echo "<ul>";
					foreach($posttags as $tag) {
						echo '<li>'. $tag->name . '</li>'; 
					}
					echo "</ul>";
				}
			?>
			<form method="get" name="meGusta" action="">
				<button type="submit">Me gusta</button>
				<input type="hidden" name="meGusta" />
			</form>
		</div><!-- .entry-content -->
	</div><!-- #post-## -->
	<?php comments_template( '', true ); ?>
<?php endwhile; // end of the loop. ?>
<?php wp_reset_query() ?>
			<?php
			if(isset($_GET['meGusta'])){
				$current_user = wp_get_current_user();
				$user_id = $current_user->ID;
				$my_post = array(
					'post_status'   => 'publish',
					'post_title'    => get_the_title($post->ID), 
					'post_content'  => '',
					'post_author'   => $user_id,
					'post_category' => array(get_term_by('slug', 'agenda', 'category')->term_id)
				);
				$new_post_id = wp_insert_post( $my_post );
				update_post_meta($new_post_id, "qh_like", $post->ID);
			}
			?>
