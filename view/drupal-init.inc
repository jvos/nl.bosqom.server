<?php
include 'class/drupal.php';
include_once 'inc/message.php';

if(!$error){
  echo('') . PHP_EOL; 
  message('Drupal initialiseren !');
  try {
    $drupal = new drupal($vhost['DocumentRoot'], $vhost['ServerName']);
  }catch (Exception $e) {
    message(sprintf('Bij initialiseren class drupal, catch: %s !', $e->getMessage()), 'error');
    $error = true;
  }
}