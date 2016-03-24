import("debug");

add_action('wp','healthe_syndication_dataset_download', 0);

function healthe_syndication_dataset_download() {
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
                    'outlet_id',
                    'outlet_name',
                    'geographic',
                    'reach',
                    'advertising_value_equivalent',
                    'impact',
                    'circulation',
                    'tams'
                    ), $delim);

      $pagenum = 0;
      do {
        $query_args = array('post_type' => 'post',
                            'posts_per_page' => 1,
                            'paged' => $pagenum++
                            );
        $query = new WP_Query($query_args);
        if( $query->have_posts() ) {
          while ($query->have_posts()) {
            $query->the_post();

            $post_pod = pods('post', get_post()->ID, true);
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

            foreach ($syndications as &$syndication) {
              write_syndication($output,
                                $delim,
                                $post_pod,
                                $syndication,
                                $marginalised_voice_terms,
                                $categories,
                                $author_terms
                                );
            }
          }
        }

      } while ($pagenum < $query->max_num_pages);

      wp_reset_query();  // Restore global post data stomped by the_post().
      exit();
    }
  }
}

function write_syndication($output,
                           $delim,
                           $post_pod,
                           $syndication,
                           $marginalised_voice_terms,
                           $categories,
                           $author_terms) {
  $syndication_pod_name = $syndication['post_type'];
  switch ($syndication['post_type']) {
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

  $syndication_pod = pods($syndication_pod_name, $syndication["ID"], false);
  $outlet = $syndication_pod->field('outlet');
  $outlet_pod = pods($outlet_pod_name, $outlet["ID"], false);

  foreach ($categories as &$category) {
    foreach ($marginalised_voice_terms as &$marginalised_voice_term) {
      foreach ($author_terms as &$author_term) {

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
                      $syndication_pod->field('impact'),
                      $outlet_pod->field('circulation', true) ?: '',
                      $syndication_pod->field('tams', true)
                      ),
                $delim);

      }
    }
  }
}
