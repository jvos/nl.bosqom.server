<?php
$modules_enabled = [];

if(!$error){
  echo('') . PHP_EOL;
  display_message('Haal alle modules op !');
  if(false === $modules_enabled = $drush->modules_themes(true, true, true)){
    display_message('Bij ophalen alle modules !', 'error');
    $error = true;
  }else {
    if($drush->check_error($modules_enabled)){
      display_message('Bij ophalen alle modules !', 'error');
      $error = true;
    }else {
      display_message('Alle modules opgehaald !', 'success');
      $modules_enabled = $drush->list_modules_themes($modules_enabled);
    }
  }
}
