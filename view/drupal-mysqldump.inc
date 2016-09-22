<?php
include_once 'class/drupal.php';
include_once 'inc/message.php';

include_once 'class/mysqldump.php';

if(!$error){
  echo('') . PHP_EOL;
  message('Backup drupal database !');

  if(!$settings = $drupal->getSettings()){
    message('settings.php bestaat niet !', 'error');
    $error = true;
  }else {
    message('settings.php ingelezen !', 'success');
  }
}

if(!$error){
  if(!$database = $drupal->getDatabase()){
    message('Geen database settings in de settings.php !', 'error');
    $error = true;
  }else {
    message('Database settings opgehaalt !', 'success');
  }
}

if(!$error){
  echo('') . PHP_EOL;
  message('Mysqldump initialiseren !');
  try {
    $mysqldump = new mysqldump($exec, $database['host'], $database['username'], $database['password'], $database['database'], $params['backup-dir'] . '/' . $database['database'] . '_bak_' . $datetime . '.sql');
  }catch (Exception $e) {
    message(sprintf('Bij initialiseren class mysqldump, catch: %s !', $e->getMessage()), 'error');
    $error = true;
  }
}

if(!$error){
  if(false === $output = $mysqldump->dump()){
    message('Bij backuppen van de drupal database !', 'error');
    $error = true;      
  }else {
    if($drush->check_error($output)){
      message('Bij backuppen van de drupal database !', 'error');
      $error = true;
    }else {
      message('Drupal database gebackupped !', 'success');
    }
  }
}