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
  if ($post-ID && $pod->id) {
    $type = $medium . '_syndication';
    $syndication_pod = pods( $type );
    $params = array( 'fields_only' => true,
                     'fields' => array('outlet', 'post_title')
                     );
    $form = $syndication_pod->form($params);
    $container_id = "healthe-new-" . $field['name'] . "-form";
?>
    <div id="<?php print($container_id); ?>" class="healthe-new-syndication-form">
<?php
    echo $form;
?>
    <div id="<?php print($container_id) . '-create'; ?>" class="button">Create</div>
    <div id="<?php print($container_id) . '-cancel'; ?>" class="button">Cancel</div>
    </div>
    <script>
        (function () {
       var fieldName = "<?php print($field['name']); ?>";
       var newButton = jQuery('<div class="button">New</div>');
       var formContainer = jQuery('#<?php print($container_id); ?>');
       var formCancelButton = jQuery('#<?php print($container_id) . '-cancel'; ?>');
       var createButton = jQuery('#<?php print($container_id) . '-create'; ?>');
       var postTitleField = jQuery('#post-body input[name="post_title"]');
       var syndicationTitleField = jQuery('#<?php print($container_id); ?> input[name="post_title"]');
       var outletField = jQuery('#<?php print($container_id); ?> input[name="outlet"]');
       var postID = "<?php the_ID() ?>";

       syndicationTitleField.attr('disabled', 'true');
       var updateSyndicationTitle = function() {
         var outletFieldUI = jQuery('#s2id_pods-form-ui-outlet');
         title = outletFieldUI.text().trim() + ": " + postTitleField.val();
         syndicationTitleField.val(title);
       };
       outletField.change(updateSyndicationTitle);
       newButton.on('click', function() {
           updateSyndicationTitle();
           formContainer.show();
         });
       formCancelButton.on('click', function() { formContainer.hide(); });
       jQuery('input[name="pods_meta_'+ fieldName +'" ]').after(newButton);
       newButton.after(formContainer);
       var startSpinner = function() {
         console.log("start spinned");
       }
       var stopSpinner = function() {
         console.log("stop spinner");
       }
       var createSyndication = function() {
         var data = {
           "title": syndicationTitleField.val(),
           "outlet": parseInt(outletField.val()),
           "post": parseInt(postID)
         };
         console.log(fieldName, data);
         startSpinner();
         jQuery.ajax({
           type: "post",
           url: "/wp-json/wp/v2/<?php echo $type; ?>",
           dataType: "json",
           data: data,
           beforeSend: function(xhr) {
               xhr.setRequestHeader('X-WP-Nonce', '<?php print wp_create_nonce('wp_rest') ?>')
             }
           })
         .done(function(response, textStatus) {
             console.log("success", textStatus, response);
           })
         .fail(function(response, textStatus) {
             console.log(textStatus, response);
           })
         .always(function() {
             stopSpinner();
           });
         syndicationTitleField.val('');
       };
       createButton.on('click', createSyndication);
       console.log("stuff " + fieldName);
    })();
    </script>
<?php
  } else {
?>
    <script>
    (function () {
      var fieldName = "<?php print($field['name']); ?>";
      var newButton = jQuery('<div class="button disabled" title="You have to save a draft post or publish before you can add syndications for it.">New</div>');
      jQuery('input[name="pods_meta_'+ fieldName +'" ]').after(newButton);
    })();
    </script>
<?php
  }
  echo "<pre>\n";
  echo "post\n";
  echo "</pre>\n";
}
?>