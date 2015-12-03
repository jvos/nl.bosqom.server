<?php
include_once 'class/drupal.php';

if(!$error){
  echo('') . PHP_EOL; 
  display_message('Drupal initialiseren !');
  try {
    $drupal = new drupal($vhost['DocumentRoot'], $vhost['ServerName']);
  }catch (Exception $e) {
    display_message(sprintf('Bij initialiseren class drupal, catch: %s !', $e->getMessage()), 'error');
    $error = true;
  }
}