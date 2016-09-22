<?php
include_once 'class/drush.php';
include_once 'inc/message.php';

if(!$error){
  echo('') . PHP_EOL;
  message('Clear views cache !');
  if(false === $output = $drush->clear_cache_views()){
    message('Bij het clearen van de views cache !', 'error');
    $error = true;
  }else {
    if($drush->check_error($output)){
      message('Bij het clearen van de views cache !', 'error');
      $error = true;
    }else {
      message('Views cache gecleared !', 'success');
    }
  }
}