<?php
include_once 'class/linux.php';
include_once 'inc/message.php';

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