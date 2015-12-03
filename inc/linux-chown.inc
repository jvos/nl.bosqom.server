<?php

if(!$error){
  echo('') . PHP_EOL;
  display_message('Zet de gebruiker en groep van de bestanden goed !');
  if(!$linux->chown($vhost['DocumentRoot'], $params['chown'])){
    display_message('Bij het zetten van de gebruikers en groep van de bestanden !', 'error');
    $error = true;
  }else {
    display_message('Gebruikers en groep bestanden goed gezet !', 'success');
  }
}