<?php

wp_enqueue_style( 'healthe-syndication-db' );

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
  echo "<pre>\n";
  echo "pod: $pod->id\n";
  echo "post: $post->ID\n";
  echo "medium: $medium\n";
  echo "field type: " . $field['type'] . "\n";
  echo "field type: " . $field['name'] . "\n";
  echo "</pre>\n";
}

function healthe_post_syndication_post($post, $field, $pod, $medium) {
  echo "<pre>\n";
  echo "post\n";
  echo "</pre>\n";
  $pod = pods( $medium . '_syndication' );
  $params = array( 'fields_only' => true,
                   'fields' => array('post_title', 'post', 'outlet')
                   );
  $form = $pod->form($params);
  $container_id = "healthe-new-" . $field['name'] . "-form";
?>
  <div id="<?php print($container_id); ?>" class="healthe-new-syndication-form">
<?php
  echo $form;
?>
  <div id="<?php print($container_id) . '-cancel'; ?>">Cancel</div>
  </div>
  <script>
      (function () {
     var field_name = "<?php print($field['name']); ?>";
     var newButton = jQuery('<div class="new-syndication-button">New</div>');
     var formContainer = jQuery('#<?php print($container_id); ?>');
     var formCancelButton = jQuery('#<?php print($container_id) . '-cancel'; ?>');
     newButton.on('click', function() { formContainer.show(); });
     formCancelButton.on('click', function() { formContainer.hide(); });
     jQuery('input[name="pods_meta_'+ field_name +'" ]').after(newButton);
     newButton.after(formContainer);
     console.log("stuff " + field_name);
  })();
  </script>
<?php

}
?>