<?php

if(!$error){
  echo('') . PHP_EOL;
  message('Clear all cache !');
  if(false === $output = $drush->clear_cache()){
    message('Bij het clearen van de cache !', 'error');
    $error = true;
  }else {
    if($drush->check_error($output)){
      message('Bij het clearen van de cache !', 'error');
      $error = true;
    }else {
      message('Cache gecleared !', 'success');
    }
  }
}
