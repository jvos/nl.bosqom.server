<?php

if(!$error){
  echo('') . PHP_EOL;
  display_message('Zet de site in offline modus !');
  if(false === $output = $drush->offline(1)){
    display_message('Bij de site in offline modus te zetten !', 'error');
    $error = true;
  }else {
    if($drush->check_error($output)){
      display_message('Bij de site in offline modus te zetten !', 'error');
      $error = true;
    }else {
      display_message('Site is in offline modus gezet !', 'success');
    }
  }
}