<?php
include 'class/php.php';
include_once 'inc/message.php';

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