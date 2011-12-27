<h1>Loop de Actividades modificado:</h1>
<?php
echo "----------------------------------------<br />";
$current_user = wp_get_current_user();
$user_id = $current_user->ID;
$filter = get_user_meta( $user_id, "qh_filter", true); 
/*
	Campos del array qh-filter que es un metadato del usuario. 
	Va todo el array en un solo texto, serializado.
	 date-min         
	 date-max         
	 time-min         
	 time-max         
	 price-min        
	 price-max        
	 map-center       
	 map-radio        
	 organizer        
	 categories       
	 participants           

	Metadatos de post que se aplican a cada actividad:
	   qh-date
	   qh-time
	   qh-price
	   qh-map-center
	   qh-map-radio
	   qh-organizer

	Taxonomías de post que se aplican a cada actividad:
	   categories
	   participants 
*/
$meta_query =  array(
	'relation' => 'AND'
);
if($filter[0]['date-min'] != ''){
	$meta_query[] = array( 
		'key'     => 'qh_fecha',
		'value'   => $filter[0]['date-min'],
		'type'    => 'DATE',
		'compare' => '>='
	);
}
if($filter[0]['date-max'] != ''){
	$meta_query[] = array(
		'key'     => 'qh_fecha',
		'value'   => $filter[0]['date-max'],
		'type'    => 'DATE',
		'compare' => '<='
	);
}
if($filter[0]['time-min'] != ''){
	$meta_query[] = array( 
		'key'     => 'qh_hora',
		'value'   => $filter[0]['time-min'],
		'type'    => 'TIME',
		'compare' => '>='
	);
}
if($filter[0]['time-max'] != ''){
	$meta_query[] = array(
		'key'     => 'qh_hora',
		'value'   => $filter[0]['time-max'],
		'type'    => 'TIME',
		'compare' => '<='
	);
}
if($filter[0]['price-min'] != 0){
	$meta_query[] = array( 
		'key'     => 'qh_price',
		'value'   => $filter[0]['price-min'],
		'type'    => 'NUMERIC',
		'compare' => '>='
	);
}
if($filter[0]['price-max'] != 0){
	$meta_query[] = array(
		'key'     => 'qh_price',
		'value'   => $filter[0]['price-max'],
		'type'    => 'NUMERIC',
		'compare' => '<='
	);
}
if($filter[0]['organizer'] != ''){
	$meta_query[] = array(
		'key'     => 'qh_organizer',
		'value'   => $filter[0]['organizer'],
		'type'    => 'TEXT',
		'compare' => '=='
	);
}

$tax_query = array(
	'relation' => 'AND' 
);
if($filter[0]['categories'] != ''){
	$tax_query[] = array(
		'taxonomy' => 'category',
		'terms' => array($filter[0]['categories']),
		'field' => 'slug',
	);
}
if($filter[0]['participants'] != ''){
	$tax_query[] = array(
		'taxonomy' => 'participants',
		'terms' => array($filter[0]['participants']),
		'field' => 'slug',
	);
}

$args = array(
	'post_type'  => 'post',
	'meta_query' => $meta_query,
	'tax_query'  => $tax_query
);
print_r($args);
$myQuery = new WP_Query( $args );
?>
<?php if ( $myQuery->have_posts() ) while ( $myQuery->have_posts() ) : $myQuery->the_post(); ?>

				<div id="nav-above" class="navigation">
					<div class="nav-previous"><?php previous_post_link( '%link', '<span class="meta-nav">' . _x( '&larr;', 'Previous post link', 'twentyten' ) . '</span> %title' ); ?></div>
					<div class="nav-next"><?php next_post_link( '%link', '%title <span class="meta-nav">' . _x( '&rarr;', 'Next post link', 'twentyten' ) . '</span>' ); ?></div>
				</div><!-- #nav-above -->

				<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
					<h1 class="entry-title"><?php the_title(); ?></h1>

					<div class="entry-meta">
						<?php twentyten_posted_on(); ?>
					</div><!-- .entry-meta -->

					<div class="entry-content">
						<?php the_content(); ?>
						<?php the_meta(); ?>
						<?php wp_link_pages( array( 'before' => '<div class="page-link">' . __( 'Pages:', 'twentyten' ), 'after' => '</div>' ) ); ?>
					</div><!-- .entry-content -->

<?php if ( get_the_author_meta( 'description' ) ) : // If a user has filled out their description, show a bio on their entries  ?>
					<div id="entry-author-info">
						<div id="author-avatar">
							<?php echo get_avatar( get_the_author_meta( 'user_email' ), apply_filters( 'twentyten_author_bio_avatar_size', 60 ) ); ?>
						</div><!-- #author-avatar -->
						<div id="author-description">
							<h2><?php printf( __( 'About %s', 'twentyten' ), get_the_author() ); ?></h2>
							<?php the_author_meta( 'description' ); ?>
							<div id="author-link">
								<a href="<?php echo get_author_posts_url( get_the_author_meta( 'ID' ) ); ?>" rel="author">
									<?php printf( __( 'View all posts by %s <span class="meta-nav">&rarr;</span>', 'twentyten' ), get_the_author() ); ?>
								</a>
							</div><!-- #author-link	-->
						</div><!-- #author-description -->
					</div><!-- #entry-author-info -->
<?php endif; ?>

					<div class="entry-utility">
						<?php twentyten_posted_in(); ?>
						<?php edit_post_link( __( 'Edit', 'twentyten' ), '<span class="edit-link">', '</span>' ); ?>
					</div><!-- .entry-utility -->
				</div><!-- #post-## -->

				<div id="nav-below" class="navigation">
					<div class="nav-previous"><?php previous_post_link( '%link', '<span class="meta-nav">' . _x( '&larr;', 'Previous post link', 'twentyten' ) . '</span> %title' ); ?></div>
					<div class="nav-next"><?php next_post_link( '%link', '%title <span class="meta-nav">' . _x( '&rarr;', 'Next post link', 'twentyten' ) . '</span>' ); ?></div>
				</div><!-- #nav-below -->

				<?php comments_template( '', true ); ?>

<?php endwhile; // end of the loop. ?>
<?php 
	/*NO SE SI SE NECESITA ESTO AQUI!!!!!*/
	wp_reset_postdata(); 
?>
