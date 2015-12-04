<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
include_once 'inc/message.php';

$params = array();
$params['user'] = '--user, is de sudo gebruikersnaam, b.v. root.';
$params['pass'] = '--pass, is de sudo wachtwoord van de gebruiker.';
$params['server_name'] = '--server_name, welke website met servernaam er geupdate moet worden, kan ook \'all\' zijn voor alle websites, b.v. bosqom.nl.';
$params['cmd'] = '--cmd, welke drush opdracht er uitgevoerd moet word, b.v. drush up.';
$params['backup-dir'] = '--backup_dir, waar de backup bestanden moeten komen (standaard staat op /var/tmp).';
$params['chown'] = '--chown, is de gebruiker en de groep die de rechten krijgt van de bestanden, b.v. www-data:www-data (standaard op www-data:www-data).';
$params['chmod'] = '--chmod, is de rechten die de bestanden krijgen, b.v. 755 (standaard op 770).';

include_once 'inc/parameters.php';

echo('') . PHP_EOL . PHP_EOL;

if(!isset($params['user']) or empty($params['user'])){
  message('Geen gebruikersnaam van de sudo user (--user) !', 'error');
  message('Voor sommige opdrachten heb je de sudo gebruiker nodig !', 'warning');
  return false;
}

if(!isset($params['pass']) or empty($params['pass'])){
  message('Geen wachtwoord van de sudo user (--pass) !', 'error');
  message('Voor sommige opdrachten heb je de sudo gebruiker nodig !', 'warning');
  return false;
}

if(!isset($params['server_name']) or empty($params['server_name'])){
  message('Geen server naam (--server_name) !', 'error');
  message('De server_name moet zoiets zijn als www.bosqom.nl.', 'warning');
  message('Of als je alle websites wilt updaten gebruik dan all.', 'warning');
  return false;
}

if(!isset($params['cmd']) or empty($params['cmd'])){
  message('Geen opdracht (--cmd) !', 'error');
  message('De opdracht moet zoiets zijn als drush up.', 'warning');
  message('De opdracht is het zelfde als een normale drush opdracht.', 'warning');
  return false;
}

if(!isset($params['backup-dir']) or empty($params['backup-dir'])){
  message('Geen backup-dir (--backup-dir) , de backup-dir word /var/tmp !', 'warning');
  $params['backup-dir'] = '/var/tmp';
}

if(!isset($params['chown']) or empty($params['chown'])){
  message('Geen chown (--chown) , de chown word www-data:www-data !', 'warning');
  $params['chown'] = 'www-data:www-data';
}

if(!isset($params['chmod']) or empty($params['chmod'])){
  message('Geen chmod (--chmod) , de chmod word 770 !', 'warning');
  $params['chmod'] = '770';
}

include_once 'apache2.php';
include_once 'drupal.php';
include_once 'drush.php';
include_once 'mysqldump.php';
include_once 'linux.php';
include_once 'exec.php';

echo('') . PHP_EOL;
message('Apache2 initialiseren !');
$apache2 = new apache2();
$vhosts = $apache2->getVirtualHostBySiteType('sites-enabled');

$date = date('Ymd');

echo('') . PHP_EOL;
message('Exec initialiseren !');
$exec = new exec($params['user'], $params['pass']);

$error = false;

if(!$error){
  echo('') . PHP_EOL;
  message('Linux initialiseren !');
  try {
    $linux = new linux($exec);
  }catch (Exception $e) {
    message(sprintf('Bij initialiseren class linux, catch: %s !', $e->getMessage()), 'error');
    $error = true;
  }
}

if(!$error){
  echo('') . PHP_EOL;
  message('PHP initialiseren !');
  try {
    $php = new php($linux);
  }catch (Exception $e) {
    message(sprintf('Bij initialiseren class php, catch: %s !', $e->getMessage()), 'error');
    $error = true;
  }
}

if(!$error){
  echo('') . PHP_EOL;
  if($php->isRunning()){
    message('PHP script draaid al, sluit het andere script of wacht tot het andere script klaar is !');
    $error = true;
  }
}

if(!$error){
  foreach($vhosts as $vhost){

    if('all' == $params['server_name']){

    }elseif(!isset($vhost['ServerName']) or $vhost['ServerName'] != $params['server_name']) {
      continue;
    }

    echo('') . PHP_EOL . PHP_EOL;
    message('Start met ' . $vhost['ServerName']);

    echo('') . PHP_EOL;
    message('Drush initialiseren !');

    try {
      $drush = new drush($exec, $vhost['DocumentRoot']);
    }catch (Exception $e) {
      message(sprintf('Bij initialiseren class drush, catch: %s !', $e->getMessage()), 'error');
      $error = true;
    }

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
        }
      }
    }

    if(!$error){
      echo('') . PHP_EOL; 
      message('Drupal initialiseren !');
      try {
        $drupal = new drupal($vhost['DocumentRoot'], $vhost['ServerName']);
      }catch (Exception $e) {
        message(sprintf('Bij initialiseren class drupal, catch: %s !', $e->getMessage()), 'error');
        $error = true;
      }
    }

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
        $mysqldump = new mysqldump($exec, $database['host'], $database['username'], $database['password'], $database['database'], $params['backup-dir'] . '/' . $database['database'] . '_bak_' . $date . '.sql');
      }catch (Exception $e) {
        message(sprintf('Bij initialiseren class mysqldump, catch: %s !', $e->getMessage()), 'error');
        $error = true;
      }
    }

    /*if(!$error){
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
    }*/

    /*if(!$error){
      echo('') . PHP_EOL;
      message('Backup bestanden !');

      if(false === $output = $linux->cp($vhost['DocumentRoot'], $params['backup-dir'] . '/' . $vhost['ServerName'] . '_bak_' . $date)){
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
    }*/

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

    if(!$error){
      echo('') . PHP_EOL;
      message('Refresh drupal via drush !');
      if(false === $output = $drush->refresh()){
        message('Bij refresh van de drupal via drush !', 'error');
        $error = true;
      }else {
        if($drush->check_error($output)){
          message('Bij refresh van de drupal via drush !', 'error');
          $error = true;
        }else {
          message('Drupal gerefreshed via drush !', 'success');
        }
      }
    }

    if(!$error){
      echo('') . PHP_EOL;
      message('Drupal updaten via drush met cmd !');
      if(false === $output = $drush->execute($params['cmd'] . ' --backupdir="' . $params['backup-dir'] . '"')){
        message('Bij update van de drupal via drush met cmd !', 'error');
        $error = true;
      }else {      
        if($drush->check_error($output)){
          message('Bij update van de drupal via drush met cmd !', 'error');
          $error = true;
        }else {
          message('Updaten van drupal via drush met cmd !', 'success');
        }
      }
    }

    if(!$error){
      echo('') . PHP_EOL;
      message('Zet de gebruiker en groep van de bestanden goed !');
      if(!$linux->chown($vhost['DocumentRoot'], $params['chown'])){
        message('Bij het zetten van de gebruikers en groep van de bestanden !', 'error');
        $error = true;
      }else {
        message('Gebruikers en groep bestanden goed gezet !', 'success');
      }
    }

    if(!$error){
      message('Zet de rechten van de bestanden goed !');
      if(!$linux->chmod($vhost['DocumentRoot'], $params['chmod'])){
        message('Bij het zetten van de rechten van de bestanden !', 'error');
        $error = true;
      }else {
        message('Het zetten van de rechten van de bestanden !', 'success');
      }
    }

    echo('') . PHP_EOL;
    message('Zet de site weer online !');
    if(false === $output = $drush->offline(0)){
      message('Bij de site weer online zetten !', 'error');
      $error = true;
    }else {
      if($drush->check_error($output)){
        message('Bij de site weer online zetten !', 'error');
        $error = true;
      }else {
        message('Site weer online gezet !', 'success');
      }
    }

    if(!$error){
      echo('') . PHP_EOL;
      message('Clear all cache !');
      if(false === $output = $drush->clear_cache()){
        message('Bij het clearen van de cache !', 'error');
        $error = true;
      }else {
        if($drush->check_error($output)){
          message('Bij het clearen van de cache !', 'error');
          $error = true;
        }else {
          message('Cache gecleared !', 'success');
        }
      }
    }
  }
}