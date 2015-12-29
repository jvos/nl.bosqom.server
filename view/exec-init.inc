<?php
include 'class/exec.php';
include_once 'inc/message.php';

if(!$error){
  echo('') . PHP_EOL;
  message('Exec initialiseren !');
  try {
    $exec = new exec($params['user'], $params['pass']);
  }catch (Exception $e) {
    message(sprintf('Bij initialiseren class exec, catch: %s !', $e->getMessage()), 'error');
    $error = true;
  }
}