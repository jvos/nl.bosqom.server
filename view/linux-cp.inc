<?php

if(!$error){
  echo('') . PHP_EOL;
  message('Backup bestanden !');

  if(false === $output = $linux->cp($vhost['DocumentRoot'], $params['backup-dir'] . '/' . $vhost['ServerName'] . '_bak_' . $datetime)){
    message('Bij backuppen van de bestanden !', 'error');
    $error = true;
  }else {
    if($drush->check_error($output)){
      message('Bij backuppen van de bestanden !', 'error');
      $error = true;
    }else {
      message('Bestanden gebackupped !', 'success');
    }
  }
}