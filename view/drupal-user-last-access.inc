<?php
include_once 'class/drupal.php';
include_once 'inc/message.php';

if(!$error){
  echo('') . PHP_EOL;
  message('Wie is er als laatste ingelogd !');
}

$users_last_access = [];

if(!$error){
  echo('') . PHP_EOL;
  message('Haal alle users op !');
  if(false === $users_last_access = $drupal->getUsersLastLogin()){
    message('Haal alle users !', 'error');
    $error = true;
  }else {
    message('Alle users opgehaald !', 'success');
    message_table($users_last_access, ['Naam', 'Status', 'Gemaakt', 'Toegang', 'Toegang aantal minuten geleden', 'Toegang aantal uren geleden']);
  }
}