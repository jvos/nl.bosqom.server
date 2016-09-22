<?php
include_once 'class/drupal.php';
include_once 'inc/message.php';

if(!$error or (isset($modules_disabled_civicrm) and !empty($modules_disabled_civicrm))){
  echo('') . PHP_EOL;
  message('Zet civicrm modules aan !');
}

if(!$error or (isset($modules_disabled_civicrm) and !empty($modules_disabled_civicrm))){
  echo('') . PHP_EOL;
  message('Zet alle civicrm modules aan !');
  message('De volgende civicrm modules worden aangezet: ');
  
  $data = [];
  foreach($modules as $module){
    if('CiviCRM' == $module['Package'] and 'civicrm' != $module['Module']){
      $data[] = [$module['Package'], $module['Naam'], $module['Versie'], $module['Module']];
    }
  }
  message_table($data, ['Package', 'Naam', 'Versie', 'Module']);
  
  foreach($modules_disabled_civicrm as $module){
    $output = $drush->enable_module($module['Module']);

    if($drush->check_error($output)){
      message(sprintf('Bij het aanzetten van de module: %s !', $module['Module']), 'error');
      $error = true;
    }else {
      message(sprintf('De module: %s is aangezet !', $module['Module']), 'success');
    }
  }
}