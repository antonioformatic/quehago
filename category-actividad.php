<?php get_header(); ?>
	<h1>Listado de Actividades--------------</h1>
	<div id="container">
		<div id="content" role="main">
			<h1 class="page-title"><?php
				printf( __( 'Category Archives: %s', 'twentyten' ), '<span>' . single_cat_title( '', false ) . '</span>' );
			?></h1>
			<?php
				$category_description = category_description();
				if ( ! empty( $category_description ) ){
					echo '<div class="archive-meta">' . $category_description . '</div>';
				}

				get_template_part( 'content', 'actividad');
			?>
		</div><!-- #content -->
	</div><!-- #container -->
<?php get_sidebar(); ?>
<?php get_footer(); ?>
