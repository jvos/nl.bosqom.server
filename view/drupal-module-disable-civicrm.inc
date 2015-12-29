<?php
if(!$error){
  echo('') . PHP_EOL;
  message('Zet civicrm modules uit !');
}

$modules = [];

if(!$error){
  echo('') . PHP_EOL;
  message('Haal alle modules op !');
  if(false === $modules = $drush->modules_themes(true, true, true)){
    message('Bij ophalen alle modules !', 'error');
    $error = true;
  }else {
    if($drush->check_error($modules)){
      message('Bij ophalen alle modules !', 'error');
      $error = true;
    }else {
      message('Alle modules opgehaald !', 'success');
      $modules = $drush->list_modules_themes($modules);
    }
  }
}

if(!$error){
  echo('') . PHP_EOL;
  message('Zet alle civicrm modules uit !');
  foreach($modules as $module){
    if('CiviCRM' == $module['Package'] and 'civicrm' != $module['Module']){
      $output = $drush->disable_module($module['Module']);
        
      if($drush->check_error($output)){
        message(sprintf('Bij het uitzetten van de module: %s !', $module['Module']), 'error');
        $error = true;
      }else {
        message(sprintf('De module: %s is uitgezet !', $module['Module']), 'success');
      }
    }
  }
}