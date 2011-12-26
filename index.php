<?php
get_header();
echo "<h1>Index.php</h1>";
if (have_posts()) :
   while (have_posts()) :
      the_post();
      the_content();
   endwhile;
endif;
get_sidebar();
get_footer(); 
?>
