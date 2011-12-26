<li id="categories">
	<h2><?php _e('Categorías:'); bloginfo('url');?></h2>
	<form action="<?php bloginfo('url'); ?>" method="get">
		<div>
			<?php
				$idObj = get_category_by_slug('actividad'); 
				$id = $idObj->term_id;
				wp_dropdown_categories("child_of=$id&hide_empty=0&hierarchical=1");
			?>
			<input type="submit" name="submit" value="view" />
		</div>
	</form>
</li>
<?php
$tags = get_tags("hide_empty=0");
$html = '<div class="post_tags">';
$html .='<ul>';
foreach ($tags as $tag){
	$tag_link = get_tag_link($tag->term_id);
	$html .= "<li><a href='{$tag_link}' title='{$tag->name} Tag' class='{$tag->slug}'>";
	$html .= "{$tag->name}</a></li>";
}
$html .= '</ul></div>';
echo $html;
?>
