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
$params['chown'] = '--chown, is de gebruiker en de groep die de rechten krijgt van de bestanden, b.v. www-data:www-data.';
$params['chmod'] = '--chmod, is de rechten die de bestanden krijgen, b.v. 770.';
$params['version'] = '--version, naar welke versie civicrm geupdate moet worden.';

$params = parameters_check($params);
$params = array_merge($params, input_sudo($params));

$error_params = false;
echo('') . PHP_EOL;

if(!isset($params['user']) or empty($params['user'])){
  message('Geef gebruikersnaam van de sudo user (--user) !', 'error');
  message('Voor sommige opdrachten heb je de sudo gebruiker nodig !', 'warning');
  $error_params = true;
}

if(!isset($params['pass']) or empty($params['pass'])){
  message('Geef wachtwoord van de sudo user (--pass) !', 'error');
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
  message('Geen path naar drush (--drush-bin), path naar drush word /usr/bin !', 'warning');
  $params['drush-bin'] = '/usr/bin';
}

if(!isset($params['cmd']) or empty($params['cmd'])){
  message('Geen opdracht (--cmd) !', 'error');
  message('De opdracht moet zoiets zijn als drush up.', 'warning');
  message('De opdracht is het zelfde als een normale drush opdracht.', 'warning');
  $error_params = true;
}

if(!isset($params['backup-dir']) or empty($params['backup-dir'])){
  message('Geen backup-dir (--backup-dir), de backup-dir word /var/tmp !', 'warning');
  $params['backup-dir'] = '/var/tmp';
}

if(!isset($params['chown']) or empty($params['chown'])){
  message('Geen chown (--chown) !', 'warning');
  message('De chown moet iets zijn zoals bosgoed:www-data .', 'error');
  $error_params = true;
}

if(!isset($params['chmod']) or empty($params['chmod'])){
  message('Geen chmod (--chmod) !', 'warning');
  message('De chmod moet iets zijn zoals 770 .', 'error');
  $error_params = true;
}

if(!isset($params['version']) or empty($params['version'])){
  message('Geen versie (--version) !', 'error');
  message('De versie moet het nummer zijn van de nieuwe versie van civicrm, zoals 4.6.10 .', 'error');
  $error_params = true;
}

if($error_params){
  exit(1);
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

if(!$error){
  message('Is iedereen uitgelogd of al een tijd geleden ingelogd geweest !', 'warning');
  if(!input_continue()){
    include 'view/continue.inc';
  }
}

// set site offline
include 'view/drush-offline.inc';
if(!$error){
  // if the site is set offline, it must be set online at the end of this script
  $input = 'continue';
}

// create a mysqldump of drupal database
include 'view/drupal-mysqldump.inc';

// create a mysqldump of civicrm database
include 'view/civicrm-mysqldump.inc';

// create a backup of all the files 
include 'view/linux-cp.inc';

if(!$error){
  message('Is de drupal / civicrm database en bestanden goed gebackuped !', 'warning'); 
  if(!input_continue()){
    include 'view/continue.inc';
  }
}

if(!$error){
  message('U kunt nu de database lokaal halen en verder gaan !', 'warning');
  if(!input_continue()){
    include 'view/continue.inc';
  }
}

// downlaod the new version of civicrm
include 'view/civicrm-download.inc';

if(!$error){
  message('Is de goede versie van civicrm gedownload (civcirm zelf en de l10n) !', 'warning');
  if(!input_continue()){
    include 'view/continue.inc';
  }
}

// empty the frontpage
include 'view/drupal-frontpage-empty.inc';
if(!$error){
  // if the site front page is set empty it must be restore at the end
  $input = 'continue';
}

// get drupal / civicrm theme settings
include 'view/drupal-civicrm-theme.inc';

// disable all civicrm modules
include 'view/drupal-module-disable-civicrm.inc';
if(!$error){
  // if the modules are disabled they must be enabled at the end
  $input = 'continue';
}

// get localization settings
include 'view/civicrm-localization.inc';

// check civicrm.settings.php
include 'view/civicrm-settings-check.inc';

if(!$error){
  message('Klopt de civicrm.settings.php ? Of maak het kloppent dan kunt u daarna hiermee doorgaan !', 'warning');
  if(!input_continue()){
    include 'view/continue.inc';
  }
}

// clear the civicrm templates_c
include 'view/civicrm-clear-templates.inc';

// drush execute
if(!$error){
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
}

// check civicrm settings path / directories
include 'view/civicrm-setting-path-check.inc';

if(!$error){
  message('Kloppen de civicrm Directory`s / Upload Mappen instellingen ? Of maak het kloppent dan kunt u daarna hiermee doorgaan !', 'warning');
  if(!input_continue()){
    include 'view/continue.inc';
  }
}

// check civicrm settings url / bron url`s
include 'view/civicrm-setting-url-check.inc';

if(!$error){
  message('Kloppen de civicrm Bron URL`s instellingen ? Of maak het kloppent dan kunt u daarna hiermee doorgaan !', 'warning');
  if(!input_continue()){
    include 'view/continue.inc';
  }
}

// check civicrm settings updateConfigBackend / clean cache and change paths
if(!$error){
  message('Kloppen de civicrm Cache opschoning en paden bijwerken instellingen ? Of maak het kloppent dan kunt u daarna hiermee doorgaan !', 'warning');
  if(!input_continue()){
    include 'view/continue.inc';
  }
}

// enable all civicrm modules
include 'view/drupal-module-enable-civicrm.inc';

// check drupal / civicrm theme settings
include 'view/drupal-civicrm-theme-check.inc';

if(!$error){
  message('Kloppen de drupal / civicrm theme instellingen ? Of maak het kloppent dan kunt u daarna hiermee doorgaan !', 'warning');
  if(!input_continue()){
    include 'view/continue.inc';
  }
}

// restore frontpage
include 'view/drupal-frontpage-restore.inc';

// check civicrm localization settings
include 'view/civicrm-localization-check.inc';

if(!$error){
  message('Kloppen de localization instellingen in civicrm ? Of maak het kloppent dan kunt u daarna hiermee doorgaan !', 'warning');
  if(!input_continue()){
    include 'view/continue.inc';
  }
}
// clear views cache
include 'view/drush-cc-views.inc';

// set site online
include 'view/drush-online.inc';

// clear cache all
include 'view/drush-cc-all.inc';

// after setting the site online the civicrm templates_c has the wrong rights
// setrights
include 'view/linux-chown.inc';
include 'view/linux-chmod.inc';

message('Klaar met het uitvoeren van het script !', 'success');
exit(0);