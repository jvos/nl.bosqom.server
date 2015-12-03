<?php
include_once 'class/linux.php';

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