<?php
get_header();
?>

<?php 
	add_meta_box( 
		$this->meta_box['id'], 
		$this->meta_box['title'], 
		array( &$this, 'show' ), 
		$page, 
		$this->meta_box['context'], 
		$this->meta_box['priority'] 
	);
?>
<h2><?php _e('Categorías:'); bloginfo('url');?></h2>
<form action="" method="get">
	<div>
		<?php
			$idObj = get_category_by_slug('actividad'); 
			$id = $idObj->term_id;
			wp_dropdown_categories("child_of=$id&hide_empty=0&hierarchical=1");

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
		<input type="submit" name="submit" value="Publicar" />
		<input type="hidden" name="enviar" />
	</div>
</form>
<?php
if(isset($_GET['enviar'])){
	echo "GET: -------------------------------------------";
	print_r($_GET);
	echo "GET: -------------------------------------------";
	$title=$_GET['nombre'];
	$body="Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed vestibulum pharetra mi quis rhoncus. Mauris lacinia neque id lacus lobortis a dictum augue molestie. Proin ut elit velit, in faucibus neque. Aliquam libero odio, bibendum non tempor eu, sodales vel felis.";
	$rpcurl="http://213.60.211.30/wordpress/xmlrpc.php";
	$username="admin";
	$password="secreto";
	$categories="actividad";
	echo wpPostXMLRPC($title,$body,$rpcurl,$username,$password,$categories,'');
}
	
function wpPostXMLRPC($title,$body,$rpcurl,$username,$password,$category,$keywords='',$encoding='UTF-8') {
    $title = htmlentities($title,ENT_NOQUOTES,$encoding);
    $keywords = htmlentities($keywords,ENT_NOQUOTES,$encoding);
 
    $content = array(
        'title'=>             $title,
        'description'=>       $body,
        'mt_allow_comments'=> 0,  // 1 to allow comments
        'mt_allow_pings'=>    0,  // 1 to allow trackbacks
        'post_type'=>         'post',
        'mt_keywords'=>       $keywords,
        'categories'=>        array($category)
    );

/*
    $params = array(0,$username,$password,$content,true);
    $request = xmlrpc_encode_request('metaWeblog.newPost',$params);
	return wp_remote_get($request);
*/
    $params = array(0,$username,$password,$content,true);
    $request = xmlrpc_encode_request('metaWeblog.newPost',$params);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
    curl_setopt($ch, CURLOPT_URL, $rpcurl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 1);
    $results = curl_exec($ch);
    curl_close($ch);
    return $results;
}
get_sidebar();
get_footer(); 
?>
