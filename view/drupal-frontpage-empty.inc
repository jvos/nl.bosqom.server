<?php

if(!$error){
  echo('') . PHP_EOL;
  message('Leeg standaardvoorpagina !');
  message(sprintf('Huidige standaardvoorpagina is: %s !', $drupal->getSiteFrontPage()));
  if(!$drupal->changeSiteFrontpageTemporary('')){
    message('Bij het legen van standaardvoorpagina !', 'error');
    $error = true;
  }else {
    message('Standaardvoorpagina geleegd !', 'success');
  }
}