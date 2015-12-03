<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$params = array();
$params['user'] = '--user, is de sudo gebruikersnaam, b.v. root.';
$params['pass'] = '--pass, is de sudo wachtwoord van de gebruiker.';
$params['server_name'] = '--server_name, welke website met servernaam er geupdate moet worden, kan ook \'all\' zijn voor alle websites, b.v. bosqom.nl.';
$params['cmd'] = '--cmd, welke drush opdracht er uitgevoerd moet word, b.v. drush up.';
$params['version'] = '--version, naar welke versie civicrm geupdate moet worden.';
$params['backup-dir'] = '--backup_dir, waar de backup bestanden moeten komen.';

include_once 'parameters.php';

echo('') . PHP_EOL . PHP_EOL;

if(!isset($params['user']) or empty($params['user'])){
  echo('Geen gebruikersnaam van de sudo user !') . PHP_EOL;
  echo('Voor sommige opdrachten heb je de sudo gebruiker nodig !') . PHP_EOL;
  return false;
}

if(!isset($params['pass']) or empty($params['pass'])){
  echo('Geen wachtwoord van de sudo user !') . PHP_EOL;
  echo('Voor sommige opdrachten heb je de sudo gebruiker nodig !') . PHP_EOL;
  return false;
}

if(!isset($params['server_name']) or empty($params['server_name'])){
  echo('Geen server naam !') . PHP_EOL;
  echo('De server_name moet zoiets zijn als www.bosqom.nl.') . PHP_EOL;
  echo('Of als je alle websites wilt updaten gebruik dan all.') . PHP_EOL;
  return false;
}

if(!isset($params['cmd']) or empty($params['cmd'])){
  echo('Geen opdracht !') . PHP_EOL;
  echo('De opdracht moet zoiets zijn als drush up.') . PHP_EOL;
  echo('De opdracht is het zelfde als een normale drush opdracht.') . PHP_EOL;
  return false;
}

if(!isset($params['version']) or empty($params['version'])){
  echo('Geen versie !') . PHP_EOL;
  echo('De versie moet het nummer zijn van de nieuwe versie van civicrm, zoals 4.6.10 .') . PHP_EOL;
  return false;
}

if(!isset($params['backup-dir']) or empty($params['backup-dir'])){
  echo('Geen backup-dir, de backup-dir word /var/tmp !') . PHP_EOL;
  echo('.') . PHP_EOL;
  $param['backup-dir'] = '/var/tmp';
}

include_once 'apache2.php';
include_once 'drupal.php';
include_once 'civicrm.php';
include_once 'drush.php';
include_once 'mysqldump.php';
include_once 'linux.php';
include_once 'exec.php';

$apache2 = new apache2();
$vhosts = $apache2->getVirtualHostBySiteType('sites-enabled');

$date = date('Ymd');
$exec = new exec($params['user'], $params['pass']);

foreach($vhosts as $vhost){
  
  if('all' == $params['server_name']){
    
  }elseif(!isset($vhost['ServerName']) or $vhost['ServerName'] != $params['server_name']) {
    continue;
  }
  
  echo('') . PHP_EOL . PHP_EOL;
  echo('/**Start met ' . $vhost['ServerName'] . '**/') . PHP_EOL;
  
  $drush = new drush($exec, $vhost['DocumentRoot']);
  
  echo('') . PHP_EOL;
  echo('/*Zet de site in offline modus !*/') . PHP_EOL;
  if(!$drush->offline(1)){
    echo(sprintf("Kan website %s niet in onderhouds modus zetten !", $vhost['ServerName'])) . PHP_EOL;
    continue;
  }
  
  echo('') . PHP_EOL;
  echo('/*Backup drupal database !*/') . PHP_EOL;
  
  $drupal = new drupal($vhost['DocumentRoot'], $vhost['ServerName']);
  if(!$settings = $drupal->getSettings()){
    echo('settings.php bestaat niet !') . PHP_EOL;
    continue;
  }
  
  if(!$database = $drupal->getDatabase()){
    echo('Geen database settings in settings.php !') . PHP_EOL;
    continue;
  }
  
  $mysqldump = new mysqldump($exec, $database['host'], $database['username'], $database['password'], $database['database'], $param['backup-dir'] . '/' . $database['database'] . '_bak_' . $date . '.sql');
  $mysqldump->dump();
  
  echo('') . PHP_EOL;
  echo('/*Backup civicrm database !*/') . PHP_EOL;
  
  $civicrm = new civicrm($vhost['DocumentRoot'], $vhost['ServerName']);
  
  if(!$settings = $civicrm->getSettings()){
    echo('settings.php bestaat niet !') . PHP_EOL;
    continue;
  }
  
  if(!$database = $civicrm->getDatabase()){
    echo('Geen database settings in settings.php !') . PHP_EOL;
    continue;
  }  
  
  $mysqldump = new mysqldump($exec, $database['host'], $database['username'], $database['password'], $database['database'], $param['backup-dir'] . '/' . $database['database'] . '_bak_' . $date . '.sql');
  $mysqldump->dump();
  
  
  echo('') . PHP_EOL;
  echo('/*Backup bestanden !*//*') . PHP_EOL;
  
  $linux = new linux($exec);
  $linux->cp($vhost['DocumentRoot'], $param['backup-dir'] . '/' . $vhost['ServerName'] . '_bak_' . $date);
   
  echo('') . PHP_EOL;
  echo('/*Downlaod civicrm + vertalings bestand !*//*') . PHP_EOL;
  
  $civicrm->download($params['version']);
  
  echo('') . PHP_EOL;
  echo('/*Verander standaardvoorpagina !*//*') . PHP_EOL;
  echo('De standaardvoorpagina is: ' . $drupal->getSiteFrontPage()) . PHP_EOL;
  $drupal->changeSiteFrontpageTemporary('');
  
  
  echo('') . PHP_EOL;
  echo('/*Verander standaardvoorpagina terug !*//*') . PHP_EOL;
  echo('De standaardvoorpagina is: ' . $drupal->getSiteFrontPage()) . PHP_EOL;
  $drupal->changeSiteFrontpageTemporary($drupal->getSiteFrontPage());
  
 /* echo('') . PHP_EOL;
  echo('/*Update drupal cron !*//*') . PHP_EOL;
  $drupal->runCron();*/
  
  /*echo('') . PHP_EOL;
  echo('/*Update drupal via drush !*//*') . PHP_EOL;
  $drush->refresh();*/
  
  /*echo('') . PHP_EOL;
  echo('/*Update drupal !*//*') . PHP_EOL;
  
  $drush->execute($params['cmd']);*/
  
  /*echo('') . PHP_EOL;
  echo('/*Zet de rechten van de bestanden goed !*//*') . PHP_EOL;
  $linux->chown($vhost['DocumentRoot'], 'www-data', 'www-data');
  $linux->chmod($vhost['DocumentRoot'], '770');*/
  
  /*echo('') . PHP_EOL;
  echo('/*Zet de site weer online !*//*') . PHP_EOL;
  $drush->offline(0);*/
  
  /*echo('') . PHP_EOL;
  echo('/*Clear all cache !*//*') . PHP_EOL;
  $drush->clear_cache();*/
}