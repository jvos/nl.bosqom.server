<?php
include 'class/apache2.php';
include_once 'inc/message.php';

if(!$error){
  echo('') . PHP_EOL;
  message('Apache2 initialiseren !');
  try {
    $apache2 = new apache2();
    $vhosts = $apache2->getVirtualHostBySiteType('sites-enabled');
  }catch (Exception $e) {
    message(sprintf('Bij initialiseren class apache2, catch: %s !', $e->getMessage()), 'error');
    $error = true;
  }
}