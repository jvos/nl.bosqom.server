<?php

if(!$error){
  echo('') . PHP_EOL;
  if($php->isRunning()){
    display_message('PHP script draaid al, sluit het andere script of wacht tot het andere script klaar is !');
    $error = true;
  }
}