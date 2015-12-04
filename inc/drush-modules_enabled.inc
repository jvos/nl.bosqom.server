<?php
$modules_enabled = [];

if(!$error){
  echo('') . PHP_EOL;
  message('Haal alle modules op !');
  if(false === $modules_enabled = $drush->modules_themes(true, true, true)){
    message('Bij ophalen alle modules !', 'error');
    $error = true;
  }else {
    if($drush->check_error($modules_enabled)){
      message('Bij ophalen alle modules !', 'error');
      $error = true;
    }else {
      message('Alle modules opgehaald !', 'success');
      $modules_enabled = $drush->list_modules_themes($modules_enabled);
      foreach ($modules_enabled as $module_enabled){
        message(implode(', ', $module_enabled));
      }
    }
  }
}
