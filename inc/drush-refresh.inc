<?php

if(!$error){
  echo('') . PHP_EOL;
  display_message('Refresh drupal via drush !');
  if(false === $output = $drush->refresh()){
    display_message('Bij refresh van de drupal via drush !', 'error');
    $error = true;
  }else {
    if($drush->check_error($output)){
      display_message('Bij refresh van de drupal via drush !', 'error');
      $error = true;
    }else {
      display_message('Drupal gerefreshed via drush !', 'success');
    }
  }
}