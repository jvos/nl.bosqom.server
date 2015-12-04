<?php
exit();

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

include_once 'inc/date.inc';

$error = false;

include_once 'inc/apache2-init.inc';
include_once 'inc/exec-init.inc';
include_once 'inc/linux-init.inc';
include_once 'inc/php-init.inc';

include_once 'inc/php-isRunning.inc';

if(!$error){
  foreach($vhosts as $vhost){

    if('all' == $params['server_name']){

    }elseif(!isset($vhost['ServerName']) or $vhost['ServerName'] != $params['server_name']) {
      continue;
    }

    echo('') . PHP_EOL . PHP_EOL;
    message('Start met ' . $vhost['ServerName']);
    
    include_once 'inc/drush-init.inc';
    include_once 'inc/drupal-init.inc';
    
    // set site offline
    include_once 'inc/drush-offline.inc';
    
    // create a mysqldump of drupal database
    //include_once 'inc/drupal-mysqldump.inc';
    
    // create a backup of all the files 
    //include_once 'inc/linux-cp.inc';
    
    // run drupal cron
    include_once 'inc/drupal-cron.inc';
    
    // refresh drupal
    include_once 'inc/drush-refresh.inc';
    
    // drush execute
    /*if(!$error){
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
    }*/
    
    // setrights
    //include_once 'inc/linux-chown.inc';
    //include_once 'inc/linux-chmod.inc';
    
    // set site online
    include_once 'inc/drush-online.inc';
    
    // clear cache all
    include_once 'inc/drush-cc-all.inc';
  }
}