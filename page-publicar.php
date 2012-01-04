<?php
get_header();
?>
<form action="" method="get">
	<div>
		<?php
			$idObj = get_category_by_slug('actividad'); 
			$id = $idObj->term_id;
			wp_dropdown_categories("child_of=$id&hide_empty=0&hierarchical=1");
		?>
		Título:<input        type="text" name="title"       /><br />
		Resumen:<input       type="text" name="content"     /><br />
		Organizador:<input   type="text" name="organizer"   /><br />
		Lugar:<input         type="text" name="location"    /><br />
		Fecha:<input         class="rwmb-date" type="text" name="date"        /><br />
		Hora:<input          type="text" name="time"        /><br />
		Precio:<input        type="text" name="price"       /><br />
		Participantes:<input type="text" name="participants"/><br />

		<input type="submit" name="submit" value="Publicar" />
		<input type="hidden" name="enviar" />
	</div>
</form>
<?php
if(isset($_GET['enviar'])){
	$current_user = wp_get_current_user();
	$user_id = $current_user->ID;
	$my_post = array(
		'post_status'   => 'publish',
		'post_title'    => $_GET['title'],
		'post_content'  => $_GET['content'],
		'post_author'   => $user_id,
		'post_category' => array($_GET['cat']),
		'tags_input'    => array($_GET['participants']) 
	);
	$new_post_id = wp_insert_post( $my_post );
	update_post_meta($new_post_id, "qh_place",     $_GET['location']);
	update_post_meta($new_post_id, "qh_date",      $_GET['date']);
	update_post_meta($new_post_id, "qh_time",      $_GET['time']);
	update_post_meta($new_post_id, "qh_organizer", $_GET['organizer']);
	update_post_meta($new_post_id, "qh_price",     $_GET['price']);

	$lugar = urlencode($_GET['location']);
	$key="ABQIAAAAROMagOEmVvKxc67sTMKv9BT2yXp_ZAY8_ufC3CFXhHIE1NvwkxTBexYVxC2gu8hVZn7bIELZTdjrxQ";
	$url = "http://maps.google.com/maps/geo?output=json&key=".$key."&q=".$lugar;
	$ret = wp_remote_get($url);
	$datos = json_decode($ret['body']);
	update_post_meta($new_post_id, "qh_lat", $datos->Placemark[0]->Point->coordinates[0]);
	update_post_meta($new_post_id, "qh_lng", $datos->Placemark[0]->Point->coordinates[1]);
}
/*
$post = array(
  'ID' => [ <post id> ] //Are you updating an existing post?
  'menu_order' => [ <order> ] //If new post is a page, sets the order should it appear in the tabs.
  'comment_status' => [ 'closed' | 'open' ] // 'closed' means no comments.
  'ping_status' => [ 'closed' | 'open' ] // 'closed' means pingbacks or trackbacks turned off
  'pinged' => [ ? ] //?
  'post_author' => [ <user ID> ] //The user ID number of the author.
  'post_category' => [ array(<category id>, <...>) ] //Add some categories.
  'post_content' => [ <the text of the post> ] //The full text of the post.
  'post_date' => [ Y-m-d H:i:s ] //The time post was made.
  'post_date_gmt' => [ Y-m-d H:i:s ] //The time post was made, in GMT.
  'post_excerpt' => [ <an excerpt> ] //For all your post excerpt needs.
  'post_name' => [ <the name> ] // The name (slug) for your post
  'post_parent' => [ <post ID> ] //Sets the parent of the new post.
  'post_password' => [ ? ] //password for post?
  'post_status' => [ 'draft' | 'publish' | 'pending'| 'future' | 'private' ] //Set the status of the new post. 
  'post_title' => [ <the title> ] //The title of your post.
  'post_type' => [ 'post' | 'page' | 'link' | 'nav_menu_item' | custom post type ] //You may want to insert a regular post, page, link, a menu item or some custom post type
  'tags_input' => [ '<tag>, <tag>, <...>' ] //For tags.
  'to_ping' => [ ? ] //?
  'tax_input' => [ array( 'taxonomy_name' => array( 'term', 'term2', 'term3' ) ) ] // support for custom taxonomies. 
);  
*/
get_sidebar();
get_footer(); 
?>
