<?php
include_once 'class/drupal.php';
include_once 'inc/message.php';

if(!$error){
  echo('') . PHP_EOL;
  message('Update drupal cron !');
  if(!$drupal->runCron()){
    message('Bij update van de drupal cron !', 'error');
    $error = true;
  }else {
    message('Drupal cron geupdated !', 'success');
  }
}