<h1>Esto es un loop de Convocatorias</h1>
<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>
	<div id="nav-above" class="navigation">
		<div class="nav-previous"><?php previous_post_link( '%link', '<span class="meta-nav">' . _x( '&larr;', 'Previous post link', 'twentyten' ) . '</span> %title' ); ?></div>
		<div class="nav-next"><?php next_post_link( '%link', '%title <span class="meta-nav">' . _x( '&rarr;', 'Next post link', 'twentyten' ) . '</span>' ); ?></div>
	</div><!-- #nav-above -->

	<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
		<div class="entry-content">
			<?php the_meta(); ?>
		</div><!-- .entry-content -->

		<div class="entry-utility">
			<?php edit_post_link( __( 'Edit', 'twentyten' ), '<span class="edit-link">', '</span>' ); ?>
		</div><!-- .entry-utility -->
	</div><!-- #post-## -->

	<div id="nav-below" class="navigation">
		<div class="nav-previous"><?php previous_post_link( '%link', '<span class="meta-nav">' . _x( '&larr;', 'Previous post link', 'twentyten' ) . '</span> %title' ); ?></div>
		<div class="nav-next"><?php next_post_link( '%link', '%title <span class="meta-nav">' . _x( '&rarr;', 'Next post link', 'twentyten' ) . '</span>' ); ?></div>
	</div><!-- #nav-below -->

	<?php comments_template( '', true ); ?>

<?php endwhile; // end of the loop. ?>
