<?php
/*
Plugin Name: Health-e Story DB
Plugin URI: http://github.com/code4sa/health-e-story-db
Version: 1.1
Author: <a href="http://code4sa.org/">Code4SA</a>
Description: Allows creation of news story Syndications which link stories and syndicating outlets. Also provides syndication data export under the Tools menu.
*/

wp_register_style( 'healthe-syndication-db',  '/wp-content/plugins/health-e-story-db/css/healthe-syndication-db.css');

include "libs/debug.php";
include "libs/syndication_menu_page.php";
include "libs/syndication_dataset_download.php";
include "libs/syndication_ui.php";

?>