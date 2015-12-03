<?php
include_once 'class/mysqldump.php';

if(!$error){
  echo('') . PHP_EOL;
  display_message('Backup drupal database !');

  if(!$settings = $drupal->getSettings()){
    display_message('settings.php bestaat niet !', 'error');
    $error = true;
  }else {
    display_message('settings.php ingelezen !', 'success');
  }
}

if(!$error){
  if(!$database = $drupal->getDatabase()){
    display_message('Geen database settings in de settings.php !', 'error');
    $error = true;
  }else {
    display_message('Database settings opgehaalt !', 'success');
  }
}

if(!$error){
  echo('') . PHP_EOL;
  display_message('Mysqldump initialiseren !');
  try {
    $mysqldump = new mysqldump($exec, $database['host'], $database['username'], $database['password'], $database['database'], $params['backup-dir'] . '/' . $database['database'] . '_bak_' . $date . '.sql');
  }catch (Exception $e) {
    display_message(sprintf('Bij initialiseren class mysqldump, catch: %s !', $e->getMessage()), 'error');
    $error = true;
  }
}

if(!$error){
  if(false === $output = $mysqldump->dump()){
    display_message('Bij backuppen van de drupal database !', 'error');
    $error = true;      
  }else {
    if($drush->check_error($output)){
      display_message('Bij backuppen van de drupal database !', 'error');
      $error = true;
    }else {
      display_message('Drupal database gebackupped !', 'success');
    }
  }
}