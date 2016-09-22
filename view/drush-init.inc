<?php
include_once 'class/drush.php';
include_once 'inc/message.php';

if(!$error){
  echo('') . PHP_EOL;
  message('Drush initialiseren !');

  try {
    $drush = new drush($exec, $vhost['DocumentRoot'], $params['drush-bin']);
  }catch (Exception $e) {
    message(sprintf('Bij initialiseren class drush, catch: %s !', $e->getMessage()), 'error');
    $error = true;
  }
}