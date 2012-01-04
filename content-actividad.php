<?php 
	global $meta_query;
	global $tax_query;
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
		</div><!-- .entry-content -->
	</div><!-- #post-## -->
	<?php comments_template( '', true ); ?>
<?php endwhile; // end of the loop. ?>
<?php wp_reset_query() ?>
