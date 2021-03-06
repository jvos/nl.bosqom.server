<?php
include_once 'class/drush.php';
include_once 'inc/message.php';

$modules_update_status = [];

if(!$error){
  echo('') . PHP_EOL;
  message('Haal alle statussen van modules op !');
  if(false === $modules_update_status = $drush->modules_update_status()){
    message('Bij ophalen alle statussen van modules !', 'error');
    $error = true;
  }else {
    message('Alle statussen van modules opgehaald !', 'success');
    $modules_update_status = $drush->list_modules_update_status($modules_update_status);
    message_table($modules_update_status, ['Name', 'Installed Version', 'Proposed version', 'Message']);
  }
}