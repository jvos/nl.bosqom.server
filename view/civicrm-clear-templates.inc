<?php
if(!$error){
  echo('') . PHP_EOL;
  message('Verwijder alle civicrm templates !');
}

if(!$error){
  $template_c_dirs = [];
  $civirm_settings = $civicrm->getSettings();
  
  if(false === $template_c_dirs = $linux->find($civirm_settings['CIVICRM_TEMPLATE_COMPILEDIR'], '-type d', 1, 1, true)){
    message('Bij ophalen van de civicrm templates !', 'error');
    $error = true;
  }else {
    if($drush->check_error($template_c_dirs)){
      message('Bij ophalen van de civicrm templates !', 'error');
      $error = true;
    }else {
      message('Civicrm templates opgehaald !', 'success');
    }
  }
}

if(!$error){
  message('De volgende civicrm templates worden verwijdert: ', 'warning');
  
  $data = [];
  foreach($template_c_dirs as $key => $value){
    $data[] = [$value];
  }
  
  message_table($data, ['Template']);
  
  
  foreach($template_c_dirs as $template_c_dir){
    if(false === $template_c_dirs = $linux->rm($template_c_dir, true)){
      message(sprintf('Bij het verwijderen van de civicrm template: %s !', $template_c_dir), 'error');
      $error = true;
    }else {
      if($drush->check_error($template_c_dirs)){
        message(sprintf('Bij het verwijderen van de civicrm template: %s !', $template_c_dir), 'error');
        $error = true;
      }else {
        message(sprintf('De civicrm template: %s is verwijdert !', $template_c_dir), 'success');
      }
    }
  }
}