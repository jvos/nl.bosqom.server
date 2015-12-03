<?php

if(!$error){
  echo('') . PHP_EOL;
  display_message('Zet alle civicrm modules uit !');
  foreach($modules_enabled as $module){
    if('CiviCRM' == $module['Package'] and 'civicrm' != $module['Module']){
      $output = $drush->disable_module($module['Module']);
        
      if($drush->check_error($output)){
        display_message(sprintf('Bij het uitzetten van de module: %s !', $module['Module']), 'error');
        $error = true;
      }else {
        display_message(sprintf('De module: %s is uitgezet !', $module['Module']), 'success');
      }
    }
  }
}