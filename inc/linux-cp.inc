<?php

if(!$error){
  echo('') . PHP_EOL;
  display_message('Backup bestanden !');

  if(false === $output = $linux->cp($vhost['DocumentRoot'], $params['backup-dir'] . '/' . $vhost['ServerName'] . '_bak_' . $date)){
    display_message('Bij backuppen van de bestanden !', 'error');
    $error = true;
  }else {
    if($drush->check_error($output)){
      display_message('Bij backuppen van de bestanden !', 'error');
      $error = true;
    }else {
      display_message('Bestanden gebackupped !', 'success');
    }
  }
}