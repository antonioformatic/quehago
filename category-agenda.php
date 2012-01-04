<?php get_header(); ?>
	<div id="container">
		<div id="content" role="main">
			<?php
				$category_description = category_description();
				if ( ! empty( $category_description ) ){
					echo '<div class="archive-meta">' . $category_description . '</div>';
				}
				$meta_query=array();
				$tax_query=array();
				get_template_part( 'filtro', 'multiple');
				get_template_part( 'content', 'agenda');
			?>
		</div><!-- #content -->
	</div><!-- #container -->
<?php get_sidebar(); ?>
<?php get_footer(); ?>
