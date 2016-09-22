<?php

//if(!$error){
  echo('') . PHP_EOL;
  message('Zet de rechten van de bestanden goed !');
  if(false === $linux->chmod($vhost['DocumentRoot'], $params['chmod'])){
    message('Bij het zetten van de rechten van de bestanden !', 'error');
    $error = true;
  }else {
    message('Het zetten van de rechten van de bestanden !', 'success');
  }
//}