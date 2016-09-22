<?php
include_once 'class/drupal.php';
include_once 'inc/message.php';


if(!$error){
  echo('') . PHP_EOL;
  message('Leeg standaardvoorpagina !');
  message(sprintf('Huidige standaardvoorpagina is: %s !', $drupal->getSiteFrontPage()));
  
  if(!$drupal->changeSiteFrontPage('')){
    message('Bij het legen van standaardvoorpagina !', 'error');
    $error = true;
  }else {
    message('Standaardvoorpagina geleegd !', 'success');
    $site_front_page = true;
  }
}