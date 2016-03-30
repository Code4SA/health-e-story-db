<?php
function healthe_debug($val) {
  ob_start();
  var_dump($val);
  $result = ob_get_clean();
  echo "\n=========================\n";
  echo  $result ;
  echo "=========================\n";
}
?>