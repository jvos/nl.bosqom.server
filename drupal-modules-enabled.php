<?php
include_once 'inc/config.php';
include_once 'inc/message.php';
include_once 'inc/input.php';
include_once 'inc/parameters.php';

$params = array();
$params['user'] = '--user, is de sudo gebruikersnaam, b.v. root.';
$params['pass'] = '--pass, is de sudo wachtwoord van de gebruiker.';
$params['drush-bin'] = '--drush-bin, waar drush staat op de server (standaard staat op /usr/bin).';
$params['server_name'] = '--server_name, welke website met servernaam er geupdate moet worden, kan ook \'all\' zijn voor alle websites, b.v. bosqom.nl.';

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

if(!isset($params['drush-bin']) or empty($params['drush-bin'])){
  message('Geef het path op van drush (--drush-bin) !', 'warning');
  $params['drush-bin'] = '/usr/bin';
}

if(!isset($params['server_name']) or empty($params['server_name'])){
  message('Geen server naam (--server_name) !', 'error');
  message('De server_name moet zoiets zijn als www.bosqom.nl.', 'warning');
  message('Of als je alle websites wilt updaten gebruik dan all.', 'warning');
  return false;
}

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
  foreach($vhosts as $name => $vhost){
    $error = false;
    
    if('all' == $params['server_name']){

    }elseif(!isset($vhost['ServerName']) or $vhost['ServerName'] != $params['server_name']) {
      continue;
    }
    
    // if vhost does not exist, show start with server_name 
    include 'view/apache2-vhost.inc';
        
    include 'view/drush-init.inc';
    include 'view/drupal-init.inc';
    
    // get drupal modules enabled
    include 'view/drush-modules-enabled.inc';
  }
}

message('Klaar met het uitvoeren van het script !', 'success');
exit(0);