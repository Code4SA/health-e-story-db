<?php
/*
Plugin Name: Health-e Story DB
Plugin URI: http://github.com/code4sa/health-e-story-db
Version: 0.1
Author: <a href="http://code4sa.org/">Code4SA</a>
Description: Allows creation of news story Syndications from the edit interface of the given story. Also provides a DB Export button.
*/

add_action( 'post_edit_form_tag', 'post_edit_form_tag' );

function post_edit_form_tag( ) {
    echo ' hello world ';
}

?>