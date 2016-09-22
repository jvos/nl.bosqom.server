<?php
include_once 'class/drush.php';
include_once 'inc/message.php';

if(!$error or (isset($offline) and $offline)){
  echo('') . PHP_EOL;
  message('Zet de site weer online !');
  if(false === $output = $drush->offline(0)){
    message('Bij de site weer online zetten !', 'error');
    $error = true;
  }else {
    if($drush->check_error($output)){
      message('Bij de site weer online zetten !', 'error');
      $error = true;
    }else {
      message('Site weer online gezet !', 'success');
    }
  }
}