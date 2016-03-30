<?php

add_action('pods_meta_meta_post_radio_syndications',  'healthe_post_edit_form_tag', 10, 3);
add_action('pods_meta_meta_post_print_syndications',  'healthe_post_edit_form_tag', 10, 3);
add_action('pods_meta_meta_post_tv_syndications',     'healthe_post_edit_form_tag', 10, 3);
add_action('pods_meta_meta_post_online_syndications', 'healthe_post_edit_form_tag', 10, 3);

function healthe_post_edit_form_tag($post, $field, $pod) {
  echo '<!-- insert custom inputs here -->';
}
?>