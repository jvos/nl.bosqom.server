<?php
include_once 'class/drush.php';
include_once 'inc/message.php';

if(!$error){
  echo('') . PHP_EOL;
  message('Zet de site in offline modus !');
  if(false === $output = $drush->offline(1)){
    message('Bij de site in offline modus te zetten !', 'error');
    $error = true;
  }else {
    if($drush->check_error($output)){
      message('Bij de site in offline modus te zetten !', 'error');
      $error = true;
    }else {
      message('Site is in offline modus gezet !', 'success');
      $offline = true;
    }
  }
}