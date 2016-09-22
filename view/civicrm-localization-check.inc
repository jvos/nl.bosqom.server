<?php
if(!$error){
  echo('') . PHP_EOL;
  message('civicrm localization instellingen checken !');
}

if(!$error){
  $data = [];
  foreach($civirm_localization_settings as $setting => $value){
    $data[] = [$setting, $value];
  }

  message_table($data, ['Instelling', 'Waarde']);
}