<?php
if(!$error){
  echo('') . PHP_EOL;
  message('Civicrm Bron URL`s checken !');
}

if(!$error){
  $setting_url = [];
  $setting_url = $civicrm->getSettingsUrl();
  
  $data = [];
  foreach($setting_url as $name => $value){
    $data[] = [$name, $value];
  }

  message_table($data, ['Naam', 'Waarde']);
}