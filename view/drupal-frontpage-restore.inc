<?php
include_once 'class/drupal.php';
include_once 'inc/message.php';

if(!$error or (isset($site_front_page) and $site_front_page)){
  echo('') . PHP_EOL;
  message('Zet de standaardvoorpagina terug !');
  if(!$drupal->changeSiteFrontPage($drupal->getSiteFrontPage())){
    message('Bij het legen van standaardvoorpagina !', 'error');
    $error = true;
  }else {
    message(sprintf('Standaardvoorpagina terug gezet naar: %s !', $drupal->getSiteFrontPage()), 'success');
  }
}