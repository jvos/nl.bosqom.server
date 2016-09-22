<?php
include_once 'class/drush.php';
include_once 'inc/message.php';

$modules_enabled = [];

if(!$error){
  echo('') . PHP_EOL;
  message('Haal alle statussen van modules op !');
  if(false === $modules_update_status = $drush->modules_update_status()){
    message('Bij ophalen alle statussen van modules !', 'error');
    $error = true;
  }else {
    message('Alle statussen van modules opgehaald !', 'success');
    $modules_update_status = list_modules_themes($modules_update_status);
    message_table($modules_update_status, ['Name', 'Installed Version', 'Proposed version', 'Message']);
  }
}