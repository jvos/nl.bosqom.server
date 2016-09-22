<?php
include_once 'class/drupal.php';
include_once 'inc/message.php';

if(!$error){
  echo('') . PHP_EOL;
  message('Drupal / civicrm theme instellingen checken !');
}

if(!$error){
  $data = [];
  foreach($drupal_civicrm_theme as $name => $value){
    $data[] = [$name, $value];
  }

  message_table($data, ['Naam', 'Waarde']);
}