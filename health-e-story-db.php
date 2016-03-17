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
     <p><a href="/health-e-story-db/Health-e_Story_DB.csv">Download</a></p>
  </div>
  <?php

}

add_action('wp','health_e_metadata_export_template_redirect', 0);

function health_e_metadata_export_template_redirect() {
  if (!current_user_can( 'export' ) ) {
    wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
  } else {
    if ($_SERVER['REQUEST_URI']=='/health-e-story-db/Health-e_Story_DB.csv') {
      header("Content-type: application/x-msdownload", true, 200);
      header("Content-Disposition: attachment; filename=Health-e_Story_DB.csv");
      header("Pragma: no-cache");
      header("Expires: 0");

      $output = fopen('php://output', 'w');
      $delim = ",";
      fputcsv($output,
              array('post_id',
                    'headline',
                    'date',
                    'author',
                    'category',
                    'marginalised_voice',
                    'syndication_id',
                    'syndication_name',
                    'media_form',
                    'print_publisher_id',
                    'print_publisher_name',
                    'online_publisher_id',
                    'online_publisher_name',
                    'radio_broadcaster_id',
                    'radio_broadcaster_name',
                    'tv_broadcaster_id',
                    'tv_broadcaster_name',
                    'geographic',
                    'reach',
                    'advertising_value_equivalent',
                    'impact',
                    'circulation',
                    'tams'
                    ), $delim);

      $post_pod = pods('post', 13149, true);

      $syndications = $post_pod->field('print_syndications');
      $marginalised_voice_terms = wp_get_post_terms($post_pod->field('ID'), 'marginalised_voices');
      $author_terms = wp_get_post_terms($post_pod->field('ID'), 'author');
      $categories = get_the_category($post_pod->field('ID'));

      foreach ($categories as &$category) {
        foreach ($marginalised_voice_terms as &$marginalised_voice_term) {
          foreach ($author_terms as &$author_term) {
            foreach ($syndications as &$syndication) {
              $syndication_pod = pods('print_syndication', $syndication["ID"], true);
              $outlet = $syndication_pod->field('outlet');
              switch ($syndication_pod->pod) {
              case 'print_syndication':
                $media_type = 'print';
                $outlet_pod = 'print_publisher';
                break;
              case 'online_syndication':
                $media_type = 'online';
                $outlet_pod = 'online_publisher';
                break;
              case 'radio_syndication':
                $media_type = 'radio';
                $outlet_pod = 'radio_broadcaster';
                break;
              case 'tv_syndication':
                $media_type = 'tv';
                $outlet_pod = 'tv_broadcaster';
                break;
              }
              $outlet_pod = pods($outlet_pod, $publisher["ID"], true);

              fputcsv($output,
                      array($post_pod->field('ID'),
                            $post_pod->field('title'),
                            $post_pod->field('date'),
                            get_user_by('login', $author_term->name)->data->display_name,
                            $category->name,
                            $marginalised_voice_term->name,
                            $syndication['ID'],
                            $syndication['post_title'],
                            $media_type,
                            $outlet['ID'],
                            $outlet['post_title'],
                            $outlet_pod->field('geographic'),
                            $outlet_pod->field('reach'),
                            $syndication_pod->field('advertising_value_equivalent'),
                            $syndication_pod->field('impact')

                            ), $delim);

            }
          }
        }
      }
      exit();
    }
  }
}

function debug($val) {
  ob_start();
  var_dump($val);
  $result = ob_get_clean();
  echo "\n=========================\n";
  echo  $result ;
  echo "=========================\n";
}



//add_action('post_edit_form_tag', 'debug');
add_action( 'pods_meta_meta_post_radio_syndications', 'post_edit_form_tag', 10, 3);
add_action( 'pods_meta_meta_post_print_syndications', 'post_edit_form_tag', 10, 3 );
add_action( 'pods_meta_meta_post_tv_syndications', 'post_edit_form_tag', 10, 3 );
add_action( 'pods_meta_meta_post_online_syndications', 'post_edit_form_tag', 10, 3 );


function post_edit_form_tag($post, $field, $pod) {
  //
  echo '<tr><td>DEADBEEF_' . $field['name'] . '</td></tr>';
}

?>