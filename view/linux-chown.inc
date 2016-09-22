<?php

//if(!$error){
  echo('') . PHP_EOL;
  message('Zet de gebruiker en groep van de bestanden goed !');
  if(false === $linux->chown($vhost['DocumentRoot'], $params['chown'])){
    message('Bij het zetten van de gebruikers en groep van de bestanden !', 'error');
    $error = true;
  }else {
    message('Gebruikers en groep bestanden goed gezet !', 'success');
  }
//}