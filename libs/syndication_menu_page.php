<?php

add_action( 'admin_menu', 'healthe_syndication_menu_item' );

function healthe_syndication_menu_item() {
  add_management_page( 'Health-e Story Syndication Export',
                       'Story Syndication Export',
                       'export',
                       'health-e-story-metadata',
                       'healthe_syndication_menu_page' );
}

function healthe_syndication_menu_page() {
  if ( !current_user_can( 'export' ) )  {
    wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
  }
  ?>
  <div style="border: 3px solid white; padding: 10px; margin: 5px">
     <h1>Story Syndication Export</h1>
     <p>Here you can download an export file of all the stories and where they've been syndicated.</p>
     <a href="/health-e-story-db/Health-e_Story_DB.csv">
       <button type="submit" style="height: 50px">Download Story Syndication Export</button>
     </a>
     <p>You can also <a href="#">download a manual</a> to explain the format of the data and some suggestions for how to analyse.
  </div>
  <div style="border: 3px solid white; padding: 10px; margin: 5px">
    <h2>Import</p>
    <form action="" method="post" enctype="multipart/form-data">
      <p>
        <label for="pod-name">Select item to import</label>
        <select name="pod-name">
          <option value="print_syndication">Print Syndications</option>
          <option value="online_syndication">Online Syndications</option>
          <option value="radio_syndication">Radio Syndications</option>
          <option value="tv_syndication">TV Syndications</option>
        </select>
      </p>
      <p>
        <label for="pod-name">Select CSV file to upload</label>
        <input type="file" name="csv-file" id="csv-file">
      </p>
      <p><input type="submit" value="Import" name="submit"></p>
    </form>
  </div><!-- ' -->
     <?php
     if(isset($_POST["submit"])) {
       $data = file_get_contents($_FILES["csv-file"]["tmp_name"]);
       $api = pods_api($_POST["pod-name"]);
       $imported = $api->import( $data, true, 'csv' );
       echo '<p>Imported ' . count($imported) . ' items.</p>';
     }
}
?>