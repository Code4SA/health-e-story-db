<?php

//add_action('post_edit_form_tag', 'debug');
add_action( 'pods_meta_meta_post_radio_syndications', 'post_edit_form_tag', 10, 3);
add_action( 'pods_meta_meta_post_print_syndications', 'post_edit_form_tag', 10, 3 );
add_action( 'pods_meta_meta_post_tv_syndications', 'post_edit_form_tag', 10, 3 );
add_action( 'pods_meta_meta_post_online_syndications', 'post_edit_form_tag', 10, 3 );


function post_edit_form_tag($post, $field, $pod) {
  echo '<!-- insert custom inputs here -->';
}
?>