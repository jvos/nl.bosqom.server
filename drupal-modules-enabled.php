<?php
include_once 'inc/message.php';

$params = array();
$params['user'] = '--user, is de sudo gebruikersnaam, b.v. root.';
$params['pass'] = '--pass, is de sudo wachtwoord van de gebruiker.';
$params['server_name'] = '--server_name, welke website met servernaam er geupdate moet worden, kan ook \'all\' zijn voor alle websites, b.v. bosqom.nl.';

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

$error = false;

include_once 'inc/apache2-init.inc';
include_once 'inc/exec-init.inc';
include_once 'inc/linux-init.inc';
include_once 'inc/php-init.inc';

include_once 'inc/php-isRunning.inc';

if(!$error){
  foreach($vhosts as $name => $vhost){

    if('all' == $params['server_name']){

    }elseif(!isset($vhost['ServerName']) or $vhost['ServerName'] != $params['server_name']) {
      continue;
    }
    
    $error = false;
    
    if(empty($vhost['DocumentRoot']) or empty($vhost['ServerName'])){
      echo('') . PHP_EOL . PHP_EOL;
      message(sprintf('De vhost met bestand naam %s, is overgeslagen, omdat hij geen document root of server name heeft !', $name));
      continue;
    }

    echo('') . PHP_EOL . PHP_EOL;
    message('Start met ' . $vhost['ServerName']);
        
    include 'inc/drush-init.inc';
    include 'inc/drupal-init.inc';
    
    // get drupal modules enabled
    include 'inc/drush-modules_enabled.inc';
  }
}
