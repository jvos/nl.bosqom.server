<?php

if(!$error){
  echo('') . PHP_EOL;
  message(sprintf('Download civicrm %s !', $params['version']));
  if(!$civicrm->download($params['version'])){
    message(sprintf('Bij downloaden van de nieuwe civicrm %s !', $params['version']), 'error');
    $error = true;
  }else {
    message(sprintf('Civicrm %s gedownload !', $params['version']), 'success');
  }
}