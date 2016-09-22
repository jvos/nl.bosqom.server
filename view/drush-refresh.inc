<?php
include_once 'class/drush.php';
include_once 'inc/message.php';

if(!$error){
  echo('') . PHP_EOL;
  message('Refresh drupal via drush !');
  if(false === $output = $drush->refresh()){
    message('Bij refresh van de drupal via drush !', 'error');
    $error = true;
  }else {
    if($drush->check_error($output)){
      message('Bij refresh van de drupal via drush !', 'error');
      $error = true;
    }else {
      message('Drupal gerefreshed via drush !', 'success');
    }
  }
}