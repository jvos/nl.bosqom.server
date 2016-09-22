<?php
include_once 'inc/config.php';
include_once 'inc/message.php';
include_once 'inc/input.php';
include_once 'inc/parameters.php';

$params = array();
$params['user'] = '--user, is de sudo gebruikersnaam, b.v. root.';
$params['pass'] = '--pass, is de sudo wachtwoord van de gebruiker.';
$params['server_name'] = '--server_name, welke website met servernaam er geupdate moet worden, kan ook \'all\' zijn voor alle websites, b.v. bosqom.nl.';
$params['drush-bin'] = '--drush-bin, waar drush staat op de server (standaard staat op /usr/bin).';
$params['cmd'] = '--cmd, welke drush opdracht er uitgevoerd moet word, b.v. drush up.';
$params['backup-dir'] = '--backup_dir, waar de backup bestanden moeten komen (standaard staat op /var/tmp).';
//$params['chown'] = '--chown, is de gebruiker en de groep die de rechten krijgt van de bestanden, b.v. www-data:www-data.';
//$params['chmod'] = '--chmod, is de rechten die de bestanden krijgen, b.v. 755.';

$params = parameters_check($params);
$params = array_merge($params, input_sudo($params));

$error_params = false;
echo('') . PHP_EOL;

if(!isset($params['user']) or empty($params['user'])){
  message('Geen gebruikersnaam van de sudo user (--user) !', 'error');
  message('Voor sommige opdrachten heb je de sudo gebruiker nodig !', 'warning');
  $error_params = true;
}

if(!isset($params['pass']) or empty($params['pass'])){
  message('Geen wachtwoord van de sudo user (--pass) !', 'error');
  message('Voor sommige opdrachten heb je de sudo gebruiker nodig !', 'warning');
  $error_params = true;
}

if(!isset($params['server_name']) or empty($params['server_name'])){
  message('Geen server naam (--server_name) !', 'error');
  message('De server_name moet zoiets zijn als www.bosqom.nl.', 'warning');
  message('Of als je alle websites wilt updaten gebruik dan all.', 'warning');
  $error_params = true;
}

if(!isset($params['drush-bin']) or empty($params['drush-bin'])){
  message('Geef het path op van drush (--drush-bin) !', 'warning');
  $params['drush-bin'] = '/usr/bin';
}

if(!isset($params['cmd']) or empty($params['cmd'])){
  message('Geen opdracht (--cmd) !', 'error');
  message('De opdracht moet zoiets zijn als drush up.', 'warning');
  message('De opdracht is het zelfde als een normale drush opdracht.', 'warning');
  $error_params = true;
}

if(!isset($params['backup-dir']) or empty($params['backup-dir'])){
  message('Geen backup-dir (--backup-dir) , de backup-dir word /var/tmp !', 'warning');
  $params['backup-dir'] = '/var/tmp';
}

/*if(!isset($params['chown']) or empty($params['chown'])){
  //message('Geen chown (--chown) , de chown word www-data:www-data !', 'warning');
  //$params['chown'] = 'www-data:www-data';
  message('Geen chown (--chown) !', 'warning');
  message('De chown moet iets zijn zoals bosgoed:www-data .', 'error');
  $error_params = true;
}*/

/*if(!isset($params['chmod']) or empty($params['chmod'])){
  //message('Geen chmod (--chmod) , de chmod word 770 !', 'warning');
 // $params['chmod'] = '770';
  message('Geen chmod (--chmod) !', 'warning');
  message('De chmod moet iets zijn zoals 770 .', 'error');
  $error_params = true;
}*/

if($error_params){
  exit(1);
}

echo('') . PHP_EOL;
parameters_display($params);

echo('') . PHP_EOL . PHP_EOL;

$error = false;
$input = 'continue';

include_once 'view/apache2-init.inc';
include_once 'view/exec-init.inc';
include_once 'view/linux-init.inc';
include_once 'view/php-init.inc';

include_once 'view/php-isRunning.inc';

if(!$error){  
  foreach($vhosts as $vhost){
    $error = false;
    
    if('all' == $params['server_name']){

    }elseif(!isset($vhost['ServerName']) or $vhost['ServerName'] != $params['server_name']) {
      continue;
    }
    
    // if vhost does not exist, show start with server_name 
    include 'view/apache2-vhost.inc';
    
    include 'view/drush-init.inc';
    include 'view/drupal-init.inc';
    
    // set site offline
    /*include 'view/drush-offline.inc';
    if(!$error){
      // if the site is set offline, it must be set online at the end of this script
      $input = 'continue';
    }*/
    
    // create a mysqldump of drupal database
    //include 'view/drupal-mysqldump.inc';
    
    // create a backup of all the files 
    //include 'view/linux-cp.inc';
    
    // run drupal cron
    include 'view/drupal-cron.inc';
    
    // refresh drupal
    include 'view/drush-refresh.inc';
    
    // drush execute
    if(!$error){
      echo('') . PHP_EOL;
      message('Drupal updaten via drush met cmd !');
      if(false === $output = $drush->execute($params['cmd'] . ' --simulate --backupdir="' . $params['backup-dir'] . '"')){
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
    
    // set site online
    //include 'view/drush-online.inc';

    // clear cache all
    include 'view/drush-cc-all.inc';

    // after setting the site online the civicrm templates_c has the wrong rights
    // setrights
    //include 'view/linux-chown.inc';
    //include 'view/linux-chmod.inc';
  }
}

message('Klaar met het uitvoeren van het script !', 'success');
exit(0);