<?php
include 'class/civicrm.php';
include_once 'inc/message.php';

if(!$error){
  echo('') . PHP_EOL; 
  message('Civicrm initialiseren !');
  try {
    $civicrm = new civicrm($vhost['DocumentRoot'], $vhost['ServerName'], $params['backup-dir']);
  }catch (Exception $e) {
    message(sprintf('Bij initialiseren class civicrm, catch: %s !', $e->getMessage()), 'error');
    $error = true;
  }
}