<?php
exit();

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
include_once 'message.php';

$params = array();
$params['user'] = '--user, is de sudo gebruikersnaam, b.v. root.';
$params['pass'] = '--pass, is de sudo wachtwoord van de gebruiker.';
$params['server_name'] = '--server_name, welke website met servernaam er geupdate moet worden, kan ook \'all\' zijn voor alle websites, b.v. bosqom.nl.';
$params['cmd'] = '--cmd, welke drush opdracht er uitgevoerd moet word, b.v. drush up.';
$params['backup-dir'] = '--backup_dir, waar de backup bestanden moeten komen (standaard staat op /var/tmp).';
$params['chown'] = '--chown, is de gebruiker en de groep die de rechten krijgt van de bestanden, b.v. www-data:www-data (standaard op www-data:www-data).';
$params['chmod'] = '--chmod, is de rechten die de bestanden krijgen, b.v. 755 (standaard op 770).';
$params['version'] = '--version, naar welke versie civicrm geupdate moet worden, b.v. 4.6.10 .';

include_once 'parameters.php';

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

if(!isset($params['version']) or empty($params['version'])){
  message('Geen versie (--version) !', 'error');
  message('De versie moet het nummer zijn van de nieuwe versie van civicrm, zoals 4.6.9 .', 'warning');
  return false;
}

include_once 'apache2.php';
include_once 'exec.php';
include_once 'drupal.php';
include_once 'civicrm.php';
include_once 'linux.php';
include_once 'drush.php';
include_once 'mysqldump.php';
include_once 'php.php';

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
  $vhost = [];
  foreach ($vhosts as $confd){
    if(isset($confd['ServerName']) and $confd['ServerName'] == $params['server_name']){
      $vhost = $confd;
    }
  }

  if(empty($vhost)){
    message('Server name bestaat niet in de vhost !', 'error');
    $error = true;
  }
}

if(!$error){
  echo('') . PHP_EOL . PHP_EOL;
  message('Start met ' . $vhost['ServerName']);
}

if(!$error){
  echo('') . PHP_EOL;
  message('Drush initialiseren !');

  try {
    $drush = new drush($exec, $vhost['DocumentRoot']);
  }catch (Exception $e) {
    message(sprintf('Bij initialiseren class drush, catch: %s !', $e->getMessage()), 'error');
    $error = true;
  }
}

// site offline
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
  // backup drupal database
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

  if(!$settings_drupal = $drupal->getSettings()){
    message('settings.php bestaat niet !', 'error');
    $error = true;
  }else {
    message('settings.php ingelezen !', 'success');
  }
}

if(!$error){
  if(!$database_drupal = $drupal->getDatabase()){
    message('Geen database settings in de settings.php !', 'error');
    $error = true;
  }else {
    message('Drupal database settings opgehaalt !', 'success');
  }
}

/*if(!$error){
  echo('') . PHP_EOL;
  message('Mysqldump initialiseren !');
  try {
    $mysqldump_drupal = new mysqldump($exec, $database_drupal['host'], $database_drupal['username'], $database_drupal['password'], $database_drupal['database'], $params['backup-dir'] . '/' . $database_drupal['database'] . '_bak_' . $date . '.sql');
  }catch (Exception $e) {
    message(sprintf('Bij initialiseren class mysqldump, catch: %s !', $e->getMessage()), 'error');
    $error = true;
  }
}*/

/*if(!$error){
  if(false === $output = $mysqldump_drupal->dump()){
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

// backup civicrm database
/*if(!$error){
  echo('') . PHP_EOL; 
  message('Civicrm initialiseren !');
  try {
    $civicrm = new civicrm($vhost['DocumentRoot'], $vhost['ServerName'], $params['backup-dir']);
  }catch (Exception $e) {
    message(sprintf('Bij initialiseren class civicrm, catch: %s !', $e->getMessage()), 'error');
    $error = true;
  }
}*/

/*if(!$error){
  echo('') . PHP_EOL;
  message('Backup civicrm database !');

  if(!$settings_civicrm = $civicrm->getSettings()){
    message('civicrm.settings.php bestaat niet !', 'error');
    $error = true;
  }else {
    message('civicrm.settings.php ingelezen !', 'success');
  }
}*/

/*if(!$error){
  if(!$database_civicrm = $civicrm->getDatabase()){
    var_dump($database_civicrm);
    message('Geen database settings in de civicrm.settings.php !', 'error');
    $error = true;
  }else {
    message('Civicrm database settings opgehaalt !', 'success');
  }
}*/

/*if(!$error){
  echo('') . PHP_EOL;
  message('Mysqldump initialiseren !');
  try {
    $mysqldump_civicrm = new mysqldump($exec, $database_civicrm['host'], $database_civicrm['username'], $database_civicrm['password'], $database_civicrm['database'], $params['backup-dir'] . '/' . $database_civicrm['database'] . '_bak_' . $date . '.sql');
  }catch (Exception $e) {
    message(sprintf('Bij initialiseren class mysqldump, catch: %s !', $e->getMessage()), 'error');
    $error = true;
  }
}*/

/*if(!$error){
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
}*/

// backup bestanden
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

// dowmnload nieuwe civicrm versie
/*if(!$error){
  echo('') . PHP_EOL;
  message(sprintf('Download civicrm %s !', $params['version']));
  if(!$civicrm->download($params['version'])){
    message(sprintf('Bij downloaden van de nieuwe civicrm %s !', $params['version']), 'error');
    $error = true;
  }else {
    message(sprintf('Civicrm %s gedownload !', $params['version']), 'success');
  }
}*/

// empty front page
/*if(!$error){
  echo('') . PHP_EOL;
  message('Leeg standaardvoorpagina !');
  message(sprintf('Huidige standaardvoorpagina is: %s !', $drupal->getSiteFrontPage()));
  if(!$drupal->changeSiteFrontpageTemporary('')){
    message('Bij het legen van standaardvoorpagina !', 'error');
    $error = true;
  }else {
    message('Standaardvoorpagina geleegd !', 'success');
  }
}*/

// disable all civicrm modules except civicrm it self
if(!$error){
  echo('') . PHP_EOL;
  message('Zet civicrm modules uit !');
}

$modules = [];

/*if(!$error){
  echo('') . PHP_EOL;
  message('Haal alle modules op !');
  if(false === $modules = $drush->modules_themes(true, true, true)){
    message('Bij ophalen alle modules !', 'error');
    $error = true;
  }else {
    if($drush->check_error($modules)){
      message('Bij ophalen alle modules !', 'error');
      $error = true;
    }else {
      message('Alle modules opgehaald !', 'success');
      $modules = $drush->list_modules_themes($modules);
    }
  }
}

if(!$error){
  echo('') . PHP_EOL;
  message('Zet alle civicrm modules uit !');
  foreach($modules as $module){
    if('CiviCRM' == $module['Package'] and 'civicrm' != $module['Module']){
      $output = $drush->disable_module($module['Module']);
        
      if($drush->check_error($output)){
        message(sprintf('Bij het uitzetten van de module: %s !', $module['Module']), 'error');
        $error = true;
      }else {
        message(sprintf('De module: %s is uitgezet !', $module['Module']), 'success');
      }
    }
  }
}

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
}*/

// set rights
/*if(!$error){
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
}*/

// set site online
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

// enable all civicrm modules
/*echo('') . PHP_EOL;
message('Zet civicrm modules aan !');
foreach($modules as $module){
  if('CiviCRM' == $module['Package'] and 'civicrm' != $module['Module']){
    $output = $drush->enable_module($module['Module']);

    if($drush->check_error($output)){
      message(sprintf('Bij het aanzetten van de module: %s !', $module['Module']), 'error');
      $error = true;
    }else {
      message(sprintf('De module: %s is aangezet !', $module['Module']), 'success');
    }
  }
}

// reset front page
echo('') . PHP_EOL;
message('Zet de standaardvoorpagina terug !');
if(!$drupal->changeSiteFrontpageTemporary($drupal->getSiteFrontPage())){
  message('Bij het legen van standaardvoorpagina !', 'error');
  $error = true;
}else {
  message(sprintf('Standaardvoorpagina terug gezet naar: %s !', $drupal->getSiteFrontPage()), 'success');
}*/

// clear cache
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
