<?php
if(!$error){
  echo('') . PHP_EOL;
  message('Civicrm  Directory`s / Instellingen - Upload Mappen checken !');
}

if(!$error){
  $setting_path = [];
  $setting_path = $civicrm->getSettingsPath();
  
  $data = [];
  foreach($setting_path as $name => $value){
    $data[] = [$name, $value];
  }

  message_table($data, ['Naam', 'Waarde']);
}