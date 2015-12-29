<?php
if(!$error){
  echo('') . PHP_EOL;
  message('Ophalen civicrm localization instellingen !');
}

if(!$error){
  if(false == $civirm_localization_settings = $civicrm->getLocalizationSettings()){
    message('Geen civicrm localization instellingen !', 'error');
    $error = true;
  }else {
    message('Civicrm localization instellingen opgehaald !', 'succes');
  }
}