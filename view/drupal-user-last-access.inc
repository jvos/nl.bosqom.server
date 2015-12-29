<?php
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
    message_table($users_last_access, ['name', 'status', 'created', 'access', 'minutest a go']);
    
    message('Is iedereen uitgelogd of al een tijd geleden ingelogd geweest !', 'warning');
    if(!input_continue()){
      include 'continue.inc';
    }
  }
}