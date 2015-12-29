<?php
$modules_enabled = [];

if(!$error){
  echo('') . PHP_EOL;
  message('Haal alle modules op !');
  if(false === $modules_enabled = $drush->modules_themes(true, true, true)){
    message('Bij ophalen alle modules !', 'error');
    $error = true;
  }else {
    message('Alle modules opgehaald !', 'success');
    $modules_enabled = list_modules_themes($modules_enabled);
    message_table($modules_enabled, ['Package', 'Naam', 'Versie', 'Module']);
  }
}