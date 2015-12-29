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
$params['chown'] = '--chown, is de gebruiker en de groep die de rechten krijgt van de bestanden, b.v. www-data:www-data (standaard op www-data:www-data).';
$params['chmod'] = '--chmod, is de rechten die de bestanden krijgen, b.v. 755 (standaard op 770).';
$params['version'] = '--version, naar welke versie civicrm geupdate moet worden.';

$params = parameters_check($params);
$params = array_merge($params, input_sudo($params));

echo('') . PHP_EOL;

if(!isset($params['user']) or empty($params['user'])){
  message('Geef gebruikersnaam van de sudo user (--user) !', 'error');
  message('Voor sommige opdrachten heb je de sudo gebruiker nodig !', 'warning');
  return false;
}

if(!isset($params['pass']) or empty($params['pass'])){
  message('Geef wachtwoord van de sudo user (--pass) !', 'error');
  message('Voor sommige opdrachten heb je de sudo gebruiker nodig !', 'warning');
  return false;
}

if(!isset($params['server_name']) or empty($params['server_name'])){
  message('Geen server naam (--server_name) !', 'error');
  message('De server_name moet zoiets zijn als www.bosqom.nl.', 'warning');
  message('Of als je alle websites wilt updaten gebruik dan all.', 'warning');
  return false;
}

if(!isset($params['drush-bin']) or empty($params['drush-bin'])){
  message('Geef het path op van drush (--drush-bin) !', 'warning');
  $params['drush-bin'] = '/usr/bin';
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

if(!isset($params['version']) or empty($params['version'])){
  message('Geen versie (--version) !', 'error');
  message('De versie moet het nummer zijn van de nieuwe versie van civicrm, zoals 4.6.10 .', 'error');
  return false;
}

echo('') . PHP_EOL;
parameters_display($params);

echo('') . PHP_EOL . PHP_EOL;

$error = false;
$input = 'exit';

include_once 'view/apache2-init.inc';
include_once 'view/exec-init.inc';
include_once 'view/linux-init.inc';
include_once 'view/php-init.inc';

include_once 'view/php-isRunning.inc';

if(!$error){
  $vhost = [];
  foreach ($vhosts as $confd){
    if(isset($confd['ServerName']) and $confd['ServerName'] == $params['server_name']){
      $vhost = $confd;
    }
  }

  if(empty($vhost)){
    message('Server name bestaat niet in de vhost !', 'error');
    $error = true;
    exit(1);
  }
}

// if vhost does not exist, show start with server_name 
include 'view/apache2-vhost.inc';
  
include 'view/drush-init.inc';
include 'view/drupal-init.inc';
include 'view/civicrm-init.inc';

// laat alle laast ingelogde users zien
include 'view/drupal-user-last-access.inc';

// set site offline
//include 'view/drush-offline.inc';

// create a mysqldump of drupal database
//include 'view/drupal-mysqldump.inc';

// create a mysqldump of civicrm database
//include 'view/civicrm-mysqldump.inc';

// create a backup of all the files 
//include 'view/linux-cp.inc';

/*message('Is de drupal / civicrm database en bestanden goed gebackuped !', 'warning');
if(!input_continue()){
  include 'continue.inc';
}

message('U kunt nu de database lokaal halen en verder gaan !', 'warning');
if(!input_continue()){
  include 'continue.inc';
}*/

// downlaod the new version of civicrm
//include 'view/civicrm-download.inc';

/*message('Is de goede versie van civicrm gedownload (civcirm zelf en de l10n) !', 'warning');
if(!input_continue()){
  include 'continue.inc';
}*/

// empty the frontpage
//include 'view/drupal-frontpage-empty.inc';

// disable all civicrm modules
//include 'view/drupal-module-disable-civicrm.inc';

// get localization settings
include 'view/civicrm-localization.inc';

// check civicrm.settings.php
include 'view/civicrm-settings-check.inc';

message('Klopt de civicrm.settings.php ? Of maak het kloppent dan kunt u daarna hiermee doorgaan !', 'warning');
if(!input_continue()){
  include 'continue.inc';
}

// drush execute
/*if(!$error){
  echo('') . PHP_EOL;
  message('Civicrm updaten via drush met cmd !');
  if(false === $output = $drush->execute($params['cmd'] . ' --backupdir="' . $params['backup-dir'] . '" --tarfile="' . $params['backup-dir'] . '/' . sprintf('civicrm-%s-drupal.tar.gz', $params['version']) . '" --langtarfile="' . $params['backup-dir'] . '/' . sprintf('civicrm-%s-l10n.tar.gz', $params['version']) . '"')){
    message('Bij update van de civicrm via drush met cmd !', 'error');
    $error = true;
  }else {      
    if($drush->check_error($output)){
      message('Bij update van de civicrm via drush met cmd !', 'error');
      $error = true;
    }else {
      message('Updaten van civicrm via drush met cmd !', 'success');
    }
  }
}*/

// restore frontpage
//include 'view/drupal-frontpage-restore.inc';

// setrights
include 'view/linux-chown.inc';
include 'view/linux-chmod.inc';

// clear cache all
include 'view/drush-cc-all.inc';

message('Website weer online zetten !', 'warning');
if(!input_continue()){
  include 'continue.inc';
}

// set site online
include 'view/drush-online.inc';

message('Klaar met het uitvoeren van het script !', 'success');
exit(0);