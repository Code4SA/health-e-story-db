<?php
/*
Plugin Name: Health-e Story DB
Plugin URI: http://github.com/code4sa/health-e-story-db
Version: 0.1
Author: <a href="http://code4sa.org/">Code4SA</a>
Description: Allows creation of news story Syndications from the edit interface of the given story. Also provides a DB Export button.
*/

/** Step 2 (from text above). */
add_action( 'admin_menu', 'my_plugin_menu' );

/** Step 1. */
function my_plugin_menu() {
  add_management_page( 'Health-e Story Metadata Export', 'Story Metadata Export', 'export', 'health-e-story-metadata', 'story_metadata_page' );
}

/** Step 3. */
function story_metadata_page() {
  if ( !current_user_can( 'export' ) )  {
    wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
  }
  ?>
  <div class="wrap">
     <p><a href="/health-e-story-db/data.csv">Download</a></p>
  </div>
  <?php
}

add_action('template_redirect','health_e_metadata_export_template_redirect');

function health_e_metadata_export_template_redirect() {
  if (!current_user_can( 'export' ) ) {
    wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
  } else {
    if ($_SERVER['REQUEST_URI']=='/health-e-story-db/data.csv') {
      header("Content-type: application/x-msdownload",true,200);
      header("Content-Disposition: attachment; filename=data.csv");
      header("Pragma: no-cache");
      header("Expires: 0");
      echo 'data';
      exit();
    }
  }
}




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