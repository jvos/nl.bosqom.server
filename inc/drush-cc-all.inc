<?php
if(!$error){
  echo('') . PHP_EOL;
  display_message('Clear all cache !');
  if(false === $output = $drush->clear_cache()){
    display_message('Bij het clearen van de cache !', 'error');
    $error = true;
  }else {
    if($drush->check_error($output)){
      display_message('Bij het clearen van de cache !', 'error');
      $error = true;
    }else {
      display_message('Cache gecleared !', 'success');
    }
  }
}
