<?php

$language =  pll_current_language( $field = 'slug' );

$args = array(
    'posts_per_page' => 1,
    'post_type' => 'post',
    'lang' => $language,
    'offset' => 3
   
); 

$the_query = new WP_Query( $args );
// The Loop
if ( $the_query->have_posts() ) :
while ( $the_query->have_posts() ) : $the_query->the_post();
  $title = the_title();
  echo $title;
  //$excerpt = the_excerpt();
  //echo $excerpt;
  //$ID = the_ID();
  //echo $ID;
  //$fiurl = get_the_post_thumbnail_url($ID);
  //echo $fiurl;
endwhile;
endif;
// Reset Post Data
wp_reset_postdata();






?>