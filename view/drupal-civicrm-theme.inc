<?php
include_once 'class/drupal.php';
include_once 'inc/message.php';

if(!$error){
  echo('') . PHP_EOL;
  message('Ophalen drupal / civicrm theme instellingen !');
}

if(!$error){
  if(false == $drupal_civicrm_theme = $drupal->getTheme()){
    message('Geen drupal / civicrm theme instellingen !', 'error');
    $error = true;
  }else {
    message('Drupal / civicrm theme instellingen opgehaald !', 'succes');
    
    $data = [];
    foreach ($drupal_civicrm_theme as $name => $value){
      $data[] = [$name, $value];
    }
    message_table($data, ['Naam', 'Waarde']);
  }
}