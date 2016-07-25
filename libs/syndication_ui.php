<?php

wp_enqueue_style( 'healthe-syndication-db' );
wp_enqueue_script( 'healthe-syndication-db', array('jquery') );

add_action('pods_meta_meta_post_radio_syndications',  'healthe_post_syndication_radio', 10, 3);
add_action('pods_meta_meta_post_print_syndications',  'healthe_post_syndication_print', 10, 3);
add_action('pods_meta_meta_post_tv_syndications',     'healthe_post_syndication_tv', 10, 3);
add_action('pods_meta_meta_post_online_syndications', 'healthe_post_syndication_online', 10, 3);

add_action('pods_meta_meta_post_radio_syndications_post',  'healthe_post_syndication_radio_post', 10, 3);
add_action('pods_meta_meta_post_print_syndications_post',  'healthe_post_syndication_print_post', 10, 3);
add_action('pods_meta_meta_post_tv_syndications_post',     'healthe_post_syndication_tv_post', 10, 3);
add_action('pods_meta_meta_post_online_syndications_post', 'healthe_post_syndication_online_post', 10, 3);

function healthe_post_syndication_radio($post, $field, $pod) {
  healthe_post_syndication($post, $field, $pod, 'radio');
}

function healthe_post_syndication_print($post, $field, $pod) {
  healthe_post_syndication($post, $field, $pod, 'print');
}

function healthe_post_syndication_online($post, $field, $pod) {
  healthe_post_syndication($post, $field, $pod, 'online');
}

function healthe_post_syndication_tv($post, $field, $pod) {
  healthe_post_syndication($post, $field, $pod, 'tv');
}

function healthe_post_syndication_radio_post($post, $field, $pod) {
  healthe_post_syndication_post($post, $field, $pod, 'radio');
}

function healthe_post_syndication_print_post($post, $field, $pod) {
  healthe_post_syndication_post($post, $field, $pod, 'print');
}

function healthe_post_syndication_online_post($post, $field, $pod) {
  healthe_post_syndication_post($post, $field, $pod, 'online');
}

function healthe_post_syndication_tv_post($post, $field, $pod) {
  healthe_post_syndication_post($post, $field, $pod, 'tv');
}

function healthe_post_syndication($post, $field, $pod, $medium) {
}

function healthe_post_syndication_post($post, $field, $pod, $medium) {
  if ($post->ID && $pod->id) {
    $type = $medium . '_syndication';
    $syndication_pod = pods( $type );
    $params = array( 'fields_only' => true,
                     'fields' => array('outlet', 'post_title')
                     );
    $form = $syndication_pod->form($params);
    $container_id = "healthe-new-" . $field['name'] . "-form";
    $jsFieldVar = 'healthe_' . $medium . 'SyndicationField';
?>
    <div id="<?php print($container_id); ?>" class="healthe-new-syndication-form">
<?php
       echo $form;
?>
      <div id="<?php print($container_id) . '-create'; ?>" class="button">Create</div>
      <div id="<?php print($container_id) . '-cancel'; ?>" class="button">Cancel</div>
      <img src="/wp-content/plugins/health-e-story-db/images/Ajax-loader.gif"
           id="<?php print($container_id) . '-spinner'; ?>" class="healthe-spinner">
      <div id="<?php print($container_id) . '-notice'; ?>" class="healthe-notice"></div>
    </div>
    <script>
         var <?php print $jsFieldVar ?>;
         (function () {
          var fieldName = '<?php print $field['name'] ?>';
          var wpAPINonce = '<?php print wp_create_nonce('wp_rest') ?>';
          var containerId = '<?php print $container_id ?>';
          var syndicationType = '<?php print $type ?>';
          var medium = '<?php print $medium ?>';
          var postID = '<?php print $post->ID ?>';
          <?php print $jsFieldVar ?> = new HealtheSyndicationField(fieldName, containerId, medium, syndicationType, postID, wpAPINonce);
    })();
    </script>
<?php
  } else {
?>
    <script>
    (function () {
      var fieldName = "<?php print($field['name']); ?>";
      var newButton = jQuery('<div class="button disabled" title="You have to save a draft post and reload the page or publish before you can add syndications for it.">New</div>');
      jQuery('input[name="pods_meta_'+ fieldName +'" ]').after(newButton);
    })();
    </script>
<?php
  }
}
?>