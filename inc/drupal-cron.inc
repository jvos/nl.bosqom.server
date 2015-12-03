<?php
if(!$error){
  echo('') . PHP_EOL;
  display_message('Update drupal cron !');
  if(!$drupal->runCron()){
    display_message('Bij update van de drupal cron !', 'error');
    $error = true;
  }else {
    display_message('Drupal cron geupdated !', 'success');
  }
}
