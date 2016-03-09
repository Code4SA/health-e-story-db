<?php
/*
Plugin Name: Health-e Story DB
Plugin URI: http://github.com/code4sa/health-e-story-db
Version: 0.1
Author: <a href="http://code4sa.org/">Code4SA</a>
Description: Allows creation of news story Syndications from the edit interface of the given story. Also provides a DB Export button.
*/

//add_action('post_edit_form_tag', 'debug');
add_action( 'pods_meta_meta_post_radio_publications', 'post_edit_form_tag', 10, 3);
add_action( 'pods_meta_meta_post_print_syndications', 'post_edit_form_tag', 10, 3 );
add_action( 'pods_meta_meta_post_tv_broadcasts', 'post_edit_form_tag', 10, 3 );
add_action( 'pods_meta_meta_post_online_publications', 'post_edit_form_tag', 10, 3 );

function debug() {
  global $wp_filter;
  ob_start();
  var_dump($wp_filter);
  $result = ob_get_clean();
  echo ' data-vardump="' . $result . '"';
}

function post_edit_form_tag($post, $field, $pod) {
  //
  echo '<tr><td>DEADBEEF_' . $field['name'] . '</td></tr>';
}

?>