<?php
include_once 'class/php.php';
include_once 'inc/display_message.php';

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