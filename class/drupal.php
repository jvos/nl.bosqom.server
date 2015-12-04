<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
include_once 'mysql.php';
include_once 'cron.php';

class drupal {
  private $document_root = '';
  private $server_name = '';
  
  private $base_url = '';
  
  private $settings = [];
  
  private $databases = [];
  private $database = '';
  private $username = '';
  private $password = '';
  private $host = '';
  private $port = '';
  private $driver = '';
  private $prefix = '';
  
  private $cron_key = '';
  
  private $site_frontpage = '';

  public function __construct($document_root, $server_name) {
    if(empty($document_root)){
      throw new Exception('[ERROR] Class drupal, function __construct, $document_root is leeg !');
    }
    if(empty($server_name)){
      throw new Exception('[ERROR] Class drupal, function __construct, $server_name is leeg !');
    }
    
    $this->document_root = $document_root;
    $this->server_name = $server_name;
    
    $this->setSettings();
    $this->setCronKey();
    $this->setSiteFrontPage();
  }
  
  public function __destruct() {
    
  }
  
  private function setSettings(){
    if (file_exists($this->document_root . '/sites/default/settings.php')) {
      include ($this->document_root . '/sites/default/settings.php');
      
      if(isset($databases)){
        $this->settings['databases'] = $databases;
      }
      
      if(isset($this->settings['databases']['default']['default'])){
        $this->databases = $this->settings['databases']['default']['default'];
      }

      if(isset($this->settings['databases']['default']['default']['database'])){
        $this->database = $this->settings['databases']['default']['default']['database'];
      }
      if(isset($this->settings['databases']['default']['default']['username'])){
        $this->username = $this->settings['databases']['default']['default']['username'];
      }
      if(isset($this->settings['databases']['default']['default']['password'])){
        $this->password = $this->settings['databases']['default']['default']['password'];
      }
      if(isset($this->settings['databases']['default']['default']['host'])){
        $this->host = $this->settings['databases']['default']['default']['host'];
      }
      if(isset($this->settings['databases']['default']['default']['port'])){
        $this->port = $this->settings['databases']['default']['default']['port'];
      }
      if(isset($this->settings['databases']['default']['default']['driver'])){
        $this->driver = $this->settings['databases']['default']['default']['driver'];
      }
      if(isset($this->settings['databases']['default']['default']['prefix']) and !is_array($this->settings['databases']['default']['default']['prefix'])){
        $this->prefix = $this->settings['databases']['default']['default']['prefix'];
      }
      if(is_array($this->settings['databases']['default']['default']['prefix'])){
        if(isset($this->settings['databases']['default']['default']['prefix']['default']) and !is_array($this->settings['databases']['default']['default']['prefix']['default'])){
          $this->prefix = $this->settings['databases']['default']['default']['prefix']['default'];
        }
      }
      
      if(isset($update_free_access)){
        $this->settings['update_free_access'] = $update_free_access;
      }
      
      if(isset($drupal_hash_salt)){
        $this->settings['drupal_hash_salt'] = $drupal_hash_salt;
      }
      
      if(isset($conf)){
        $this->settings['conf'] = $conf;
      }
    }else {
      $this->message('Class drupal, function setSettings, Geen settings.php bestand !', 'error');
      $this->settings = false;
    }
  }
  
  public function getSettings(){
    return $this->settings;
  }
  
  public function getDatabase(){
    if(empty($this->databases['host'])){
      return false; 
    }
    if(empty($this->databases['username'])){
      return false; 
    }
    if(empty($this->databases['password'])){
      return false; 
    }
    if(empty($this->databases['database'])){
      return false; 
    }
    
    return $this->databases;
  }

  private function setCronKey(){
    if(false == $this->settings){
      return false;
    }
        
    $mysql = new mysql();
    if($mysql->mysql_connect($this->host, $this->username, $this->password, $this->database)){
      $query = "SELECT value FROM " . $this->prefix . "variable WHERE name = 'cron_key'";
      
      $row = $mysql->mysql_fetch_assoc_one($query);
      $value = unserialize($row['value']);

      $this->cron_key = $value;
    }else {
      return false;
    }
  }
  
  public function getCronKey(){
    return $this->cron_key;
  }
  
  public function runCron(){
    $url = sprintf('http://%s/cron.php?cron_key=%s', $this->server_name, $this->cron_key);
    return cron::executeUrl($url);
  }

  public function setSiteFrontPage(){        
    $mysql = new mysql();
    if($mysql->mysql_connect($this->host, $this->username, $this->password, $this->database)){
      $query = "SELECT value FROM " . $this->prefix . "variable WHERE name = 'site_frontpage'";
      
      $row = $mysql->mysql_fetch_assoc_one($query);
      $value = unserialize($row['value']);
      
      $this->site_frontpage = $value;
    }else {
      return false;
    }
  }
    
  public function getSiteFrontPage(){
    return $this->site_frontpage;
  }
  
  public function changeSiteFrontpageTemporary($site_frontpage){
    $mysql = new mysql();
    if($mysql->mysql_connect($this->host, $this->username, $this->password, $this->database)){
      $query = "UPDATE " . $this->prefix . "variable SET value = '" . serialize($site_frontpage) . "' WHERE name = 'site_frontpage'";
      
      if($result = $mysql->mysql_query($query)){
        return true;
      }
      return false;
      
    }else {
      return false;
    }
  }
    
  public function message($message, $status = 'info'){
    message($message, $status);
  }
}