<?php

if(!$error){
  display_message('Zet de rechten van de bestanden goed !');
  if(!$linux->chmod($vhost['DocumentRoot'], $params['chmod'])){
    display_message('Bij het zetten van de rechten van de bestanden !', 'error');
    $error = true;
  }else {
    display_message('Het zetten van de rechten van de bestanden !', 'success');
  }
}