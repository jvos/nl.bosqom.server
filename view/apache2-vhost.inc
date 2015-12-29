<?php

if(empty($vhost['DocumentRoot']) or empty($vhost['ServerName'])){
  echo('') . PHP_EOL . PHP_EOL;
  message(sprintf('De vhost met bestand naam %s, wordt overgeslagen, omdat hij geen document root of server name heeft !', $name));

  if(!input_continue()){
    $error = true;
    exit(0);
  }

  $error = true;
  include 'continue.inc';
}

if(!$error){
  echo('') . PHP_EOL . PHP_EOL;
  message('Start met ' . $vhost['ServerName']);
}

if(!input_continue()){
  include 'continue.inc';
}