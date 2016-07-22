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
      <img src="/wp-content/plugins/health-e-story-db/images/Ajax-loader.gif"
           id="<?php print($container_id) . '-spinner'; ?>" class="healthe-spinner">
    </div>
    <script>
        (function () {
       var fieldName = "<?php print($field['name']); ?>";
       var newButton = jQuery('<div class="button">New</div>');
       var formContainer = jQuery('#<?php print($container_id); ?>');
       var formCancelButton = jQuery('#<?php print($container_id) . '-cancel'; ?>');
       var createButton = jQuery('#<?php print($container_id) . '-create'; ?>');
       var spinner = jQuery('#<?php print($container_id) . '-spinner'; ?>');
       var postTitleField = jQuery('#post-body input[name="post_title"]');
       var syndicationTitleField = jQuery('#<?php print($container_id); ?> input[name="post_title"]');
       var outletField = jQuery('#<?php print($container_id); ?> input[name="outlet"]');
       var postID = "<?php the_ID() ?>";

       syndicationTitleField.attr('disabled', 'true');
       /* Rename the form fields we don't actually want involved in the Post form */
       jQuery('#<?php print($container_id); ?> input').each(function(i, el) {
           var el = jQuery(el);
           el.prop('name', fieldName + '_' + el.prop('name'));
         });

       var updateSyndicationTitle = function() {
         var outletFieldUI = jQuery('#<?php print($container_id); ?> .pods-form-ui-field-name-outlet');
         var title = outletFieldUI.text().trim() + ": " + postTitleField.val();
         syndicationTitleField.val(title);
       };
       var disableCreate = function() {
         createButton.addClass('disabled');
       };
       var enableCreate = function() {
         createButton.removeClass('disabled');
       };
       var disableCancel = function() {
         formCancelButton.addClass('disabled');
       };
       var enableCancel = function() {
         formCancelButton.removeClass('disabled');
       };
       var checkSubmittable = function() {
         if (outletField.val()) {
           enableCreate();
         } else {
           disableCreate();
         }
       }
       checkSubmittable();
       var showForm = function() {
         formContainer.show();
       }
       var hideForm = function() {
         formContainer.hide();
       }
       outletField.change(updateSyndicationTitle);
       outletField.change(checkSubmittable);
       newButton.on('click', function() {
           showForm();
         });
       formCancelButton.on('click', function() {
           if (!formCancelButton.hasClass('disabled'))
             hideForm();
         });
       var syndicationsInput = jQuery('input[name="pods_meta_'+ fieldName +'" ]');
       syndicationsInput.after(newButton);
       newButton.after(formContainer);
       var startSpinner = function() {
         spinner.show();
       }
       var stopSpinner = function() {
         spinner.hide();
       }
       var createSyndication = function() {
         var data = {
           title: syndicationTitleField.val(),
           outlet: parseInt(outletField.val()),
           post: parseInt(postID),
           status: "publish"
         };
         startSpinner();
         disableCreate();
         disableCancel();
         jQuery.ajax({
           type: "post",
           url: "/wp-json/wp/v2/<?php echo $type; ?>",
           dataType: "json",
           contentType: "application/json",
           data: JSON.stringify(data),
           beforeSend: function(xhr) {
               xhr.setRequestHeader('X-WP-Nonce', '<?php print wp_create_nonce('wp_rest') ?>')
             }
           })
         .done(function(response, textStatus) {
             var syndicationList = jQuery('#s2id_pods-form-ui-pods-meta-<?php print($medium); ?>-syndications ul.select2-choices');
             var fakeItem = jQuery("<li class=\"select2-search-choice\"><div>"
                                   + data.title + "</div></li>");
             syndicationList.append(fakeItem);
             /* Add new syndication to post form so it doesn't try to remove
                the post from the syndication we just created if someone
                submits the current post form */
             var syndications = syndicationsInput.val();
             if (syndications) {
               syndications += "," + response.id;
             } else {
               syndications = response.id;
             }
             syndicationsInput.val(syndications);
             hideForm();
             // clear title so it's not confusing if creating another syndication.
             syndicationTitleField.val('');
           })
         .fail(function(response, textStatus) {
             console.log(textStatus, response);
           })
         .always(function() {
             stopSpinner();
             enableCreate();
             enableCancel();
           });
       };
       createButton.on('click', function() {
           if (!createButton.hasClass('disabled')) {
             createSyndication();
           }
         });
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