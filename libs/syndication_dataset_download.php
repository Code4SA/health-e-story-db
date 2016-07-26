<?php

add_action('wp','healthe_syndication_dataset_download', 0);

function healthe_syndication_dataset_download() {

  $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

  if ($path == '/health-e-story-db/health-e-stories.csv') {
    if (!current_user_can( 'export' ) ) {
      wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
    } else {
      header("Content-type: application/x-msdownload", true, 200);
      header("Content-Disposition: attachment; filename=health-e-stories.csv");
      header("Pragma: no-cache");
      header("Expires: 0");

      $query_args = array('post_type' => 'post',
                          'posts_per_page' => -1,
                          'fields' => 'ids'
                          );
      if ($_GET['since']) {
        $query_args['date_query'] = array('after' => $_GET['since']);
      }
      $query = new WP_Query($query_args);

      $output = fopen('php://output', 'w');
      $delim = ",";

      if ($_GET['format'] == 'flat') {
        $header_function = healthe_write_header_flat;
        $post_loop_function = healthe_write_post_flat;
      } else {
        $header_function = healthe_write_header;
        $post_loop_function = healthe_write_post;
      }

      $header_function($output, $delim);

      // copy posts array to ensure index isn't an issue for foreach
      $ids = $query->posts;
      foreach($ids as $id) {
        $post_pod = pods('post', $id, true);
        $post_loop_function($output, $delim, $post_pod);
        // aggressively flush cache otherwise we quickly run out of memory
        wp_cache_flush();
      }

      exit();
    }
  }
}

function healthe_write_header($output, $delim) {
  fputcsv($output,
          array('post_id',
                'headline',
                'date',
                'author',
                'categories',
                'marginalised_voices',
                'outlet_names',
                ), $delim);
}

function healthe_write_header_flat($output, $delim) {
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
                'outlet_id',
                'outlet_name',
                'geographic',
                'reach',
                'advertising_value_equivalent',
                'impact',
                'circulation',
                'tams'
                ), $delim);
}

function healthe_write_post($output, $delim, $post_pod) {
}

function healthe_write_post_flat($output, $delim, $post_pod) {
  $marginalised_voice_terms = wp_get_post_terms($post_pod->field('ID'), 'marginalised_voices');
  $marginalised_voice_terms = $marginalised_voice_terms ?: array(new StdClass);
  $author_terms = wp_get_post_terms($post_pod->field('ID'), 'author');
  $author_terms = $author_terms ?: array(new StdClass);
  $categories = get_the_category($post_pod->field('ID'));
  $categories = $categories ?: array(new StdClass);

  $print_syndications = $post_pod->field('print_syndications') ?: array();
  $online_syndications = $post_pod->field('online_syndications') ?: array();
  $radio_syndications = $post_pod->field('radio_syndications') ?: array();
  $tv_syndications = $post_pod->field('tv_syndications') ?: array();


  if (!($print_syndications||$online_syndications||$radio_syndications||$tv_syndications)) {
    $dummy_syndications = array(array());
  } else {
    $dummy_syndications = array();
  }

  $syndications = array_merge($print_syndications,
                              $online_syndications,
                              $radio_syndications,
                              $tv_syndications,
                              $dummy_syndications);

  foreach ($syndications as $syndication) {
    healthe_write_syndication_flat($output,
                              $delim,
                              $post_pod,
                              $syndication,
                              $marginalised_voice_terms,
                              $categories,
                              $author_terms
                              );
  }
}

function healthe_write_syndication_flat($output,
                           $delim,
                           $post_pod,
                           $syndication,
                           $marginalised_voice_terms,
                           $categories,
                           $author_terms) {
  list($syndication_pod_name,
       $media_type,
       $outlet_pod_name) = healthe_post_type_to_medium($syndication['post_type']);
  $syndication_pod = pods($syndication_pod_name, $syndication["ID"], false);
  $outlet = $syndication_pod->field('outlet');
  $outlet_pod = pods($outlet_pod_name, $outlet["ID"], false);

  foreach ($categories as $category) {
    foreach ($marginalised_voice_terms as $marginalised_voice_term) {
      foreach ($author_terms as $author_term) {

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
                      $syndication_pod->field('advertising_value_equivalent') ?: '',
                      $syndication_pod->field('impact'),
                      $outlet_pod->field('circulation', true) ?: '',
                      $syndication_pod->field('tams', true)
                      ),
                $delim);

      }
    }
  }
}

function healthe_post_type_to_medium($post_type) {
  $syndication_pod_name = $post_type;
  switch ($post_type) {
  case 'print_syndication':
    $media_type = 'print';
    $outlet_pod_name = 'print_publisher';
    break;
  case 'online_syndication':
    $media_type = 'online';
    $outlet_pod_name = 'online_publisher';
    break;
  case 'radio_syndication':
    $media_type = 'radio';
    $outlet_pod_name = 'radio_broadcaster';
    break;
  case 'tv_syndication':
    $media_type = 'tv';
    $outlet_pod_name = 'tv_broadcaster';
    break;
  default:
    $media_type = 'health-e_only';
    $outlet_pod_name = NULL;
    break;
  }
  return array($syndication_pod_name,
               $media_type,
               $outlet_pod_name);
}

?>