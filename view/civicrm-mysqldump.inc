<?php
include_once 'class/mysqldump.php';

if(!$error){
  echo('') . PHP_EOL;
  message('Backup civicrm database !');

  if(!$settings_civicrm = $civicrm->getSettings()){
    message('civicrm.settings.php bestaat niet !', 'error');
    $error = true;
  }else {
    message('civicrm.settings.php ingelezen !', 'success');
  }
}

if(!$error){
  if(!$database_civicrm = $civicrm->getDatabase()){
    var_dump($database_civicrm);
    message('Geen database settings in de civicrm.settings.php !', 'error');
    $error = true;
  }else {
    message('Civicrm database settings opgehaalt !', 'success');
  }
}

if(!$error){
  echo('') . PHP_EOL;
  message('Mysqldump initialiseren !');
  try {
    $mysqldump_civicrm = new mysqldump($exec, $database_civicrm['host'], $database_civicrm['username'], $database_civicrm['password'], $database_civicrm['database'], $params['backup-dir'] . '/' . $database_civicrm['database'] . '_bak_' . $datetime . '.sql');
  }catch (Exception $e) {
    message(sprintf('Bij initialiseren class mysqldump, catch: %s !', $e->getMessage()), 'error');
    $error = true;
  }
}

if(!$error){
  if(false === $output = $mysqldump_civicrm->dump()){
    message('Bij backuppen van de civicrm database !', 'error');
    $error = true;      
  }else {
    if($drush->check_error($output)){
      message('Bij backuppen van de civicrm database !', 'error');
      $error = true;
    }else {
      message('Civicrm database gebackupped !', 'success');
    }
  }
}