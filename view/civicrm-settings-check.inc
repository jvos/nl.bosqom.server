<?php
if(!$error){
  echo('') . PHP_EOL;
  message('civicrm.setttings.php checken !');
}

if(!$error){
  $civirm_settings = $civicrm->getSettings();
  if(!isset($civirm_settings['CIVICRM_USE_MEMCACHE']) or ('1' != $civirm_settings['CIVICRM_USE_MEMCACHE'] and '0' != $civirm_settings['CIVICRM_USE_MEMCACHE'])){
    message('CIVICRM_USE_MEMCACHE bestaat niet of geen goede waarde !', 'warning');
  }
  if(!isset($civirm_settings['CIVICRM_MEMCACHE_HOST']) or empty($civirm_settings['CIVICRM_MEMCACHE_HOST'])){
    message('CIVICRM_MEMCACHE_HOST bestaat niet of is leeg !', 'warning');
  }
  if(!isset($civirm_settings['CIVICRM_MEMCACHE_PORT']) or empty($civirm_settings['CIVICRM_MEMCACHE_PORT'])){
    message('CIVICRM_MEMCACHE_PORT bestaat niet of is leeg !', 'warning');
  }
  if(!isset($civirm_settings['CIVICRM_MEMCACHE_TIMEOUT']) or empty($civirm_settings['CIVICRM_MEMCACHE_TIMEOUT'])){
    message('CIVICRM_MEMCACHE_TIMEOUT bestaat niet of is leeg !', 'warning');
  }
  
  if(!isset($civirm_settings['CIVICRM_MEMCACHE_PREFIX'])){
    message('CIVICRM_MEMCACHE_PREFIX bestaat niet of is leeg !', 'warning');
  }
    
  $data = [];
  foreach($civirm_settings as $name => $value){
    $data[] = [$name, $value];
  }
  
  message_table($data, ['Naam', 'Waarde']);
}

