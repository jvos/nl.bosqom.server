<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
include_once 'display_message.php';

$params = array();
$params['server_name'] = '--server_name, welke website met servernaam er geupdate moet worden, kan ook \'all\' zijn voor alle websites, b.v. bosqom.nl.';

include_once 'parameters.php';

echo('') . PHP_EOL . PHP_EOL;

if(!isset($params['server_name']) or empty($params['server_name'])){
  display_message('Geen server naam (--server_name) !', 'error');
  display_message('De server_name moet zoiets zijn als www.bosqom.nl.', 'warning');
  display_message('Of als je alle websites wilt updaten gebruik dan all.', 'warning');
  return false;
}

include_once 'apache2.php';
include_once 'drupal.php';
include_once 'drush.php';
include_once 'linux.php';
include_once 'exec.php';

echo('') . PHP_EOL;
display_message('Apache2 initialiseren !');
$apache2 = new apache2();
$vhosts = $apache2->getVirtualHostBySiteType('sites-enabled');

$date = date('Ymd');

echo('') . PHP_EOL;
display_message('Exec initialiseren !');
$exec = new exec($params['user'], $params['pass']);

$error = false;

if(!$error){
  echo('') . PHP_EOL;
  display_message('Linux initialiseren !');
  try {
    $linux = new linux($exec);
  }catch (Exception $e) {
    display_message(sprintf('Bij initialiseren class linux, catch: %s !', $e->getMessage()), 'error');
    $error = true;
  }
}

if(!$error){
  echo('') . PHP_EOL;
  display_message('PHP initialiseren !');
  try {
    $php = new php($linux);
  }catch (Exception $e) {
    display_message(sprintf('Bij initialiseren class php, catch: %s !', $e->getMessage()), 'error');
    $error = true;
  }
}

if(!$error){
  echo('') . PHP_EOL;
  if($php->isRunning()){
    display_message('PHP script draaid al, sluit het andere script of wacht tot het andere script klaar is !');
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
    display_message('Start met ' . $vhost['ServerName']);

    echo('') . PHP_EOL;
    display_message('Drush initialiseren !');

    try {
      $drush = new drush($exec, $vhost['DocumentRoot']);
    }catch (Exception $e) {
      display_message(sprintf('Bij initialiseren class drush, catch: %s !', $e->getMessage()), 'error');
      $error = true;
    }
    
    $modules = [];

    if(!$error){
      echo('') . PHP_EOL;
      display_message('Haal alle modules op !');
      if(false === $modules = $drush->modules_themes(true, true, true)){
        display_message('Bij ophalen alle modules !', 'error');
        $error = true;
      }else {
        if($drush->check_error($modules)){
          display_message('Bij ophalen alle modules !', 'error');
          $error = true;
        }else {
          display_message('Alle modules opgehaald !', 'success');
          $modules = $drush->list_modules_themes($modules);
        }
      }
    }
    
    if(!$error){
      echo('<pre>');
      print_r($modules);
      echo('</pre>');
    }
  }
}