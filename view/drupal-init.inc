<?php
include_once 'class/drupal.php';
include_once 'inc/message.php';

if(!$error){
  echo('') . PHP_EOL; 
  message('Drupal initialiseren !');
  try {
    $drupal = new drupal($vhost['DocumentRoot'], $vhost['ServerName']);
    
    if(!$drupal->getSettings()){
      echo('') . PHP_EOL . PHP_EOL;
      message(sprintf('%s, wordt overgeslagen, omdat de settings.php niet bestaat !', $vhost['ServerName']));

      if(!input_continue()){
        $error = true;
        exit(0);
      }

      $error = true;
      include 'continue.inc';
    }
    
  }catch (Exception $e) {
    message(sprintf('Bij initialiseren class drupal, catch: %s !', $e->getMessage()), 'error');
    $error = true;
  }
}