<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
include_once 'mysql.php';

class drupal {
  private $document_root = '';
  private $server_name = '';
  
  private $base_url = '';
  
  private $settings = [];
  
  private $cron_key = '';
  
  public function __construct($document_root, $server_name) {
    $this->document_root = $document_root;
    $this->server_name = $server_name;
    
    $this->setSettings();
    $this->setCronKey();
  }
  
  private function setSettings(){
    if (file_exists($this->document_root . '/sites/default/settings.php')) {
      include ($this->document_root . '/sites/default/settings.php');
      
      if(isset($databases)){
        $this->settings['databases'] = $databases;
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
    }
  }
  
  public function getSettings(){
    return $this->settings;
  }
  
  private function setCronKey(){
    $database = '';
    $username = '';
    $password = '';
    $host = '';
    $port = '';
    $driver = '';
    $prefix = '';
    
    if(isset($this->settings['databases']['default']['default']['database']) and !empty($this->settings['databases']['default']['default']['database'])){
      $database = $this->settings['databases']['default']['default']['database'];
    }
    if(isset($this->settings['databases']['default']['default']['username']) and !empty($this->settings['databases']['default']['default']['username'])){
      $username = $this->settings['databases']['default']['default']['username'];
    }
    if(isset($this->settings['databases']['default']['default']['password']) and !empty($this->settings['databases']['default']['default']['password'])){
      $password = $this->settings['databases']['default']['default']['password'];
    }
    if(isset($this->settings['databases']['default']['default']['host']) and !empty($this->settings['databases']['default']['default']['host'])){
      $host = $this->settings['databases']['default']['default']['host'];
    }
    if(isset($this->settings['databases']['default']['default']['port']) and !empty($this->settings['databases']['default']['default']['port'])){
      $port = $this->settings['databases']['default']['default']['port'];
    }
    if(isset($this->settings['databases']['default']['default']['driver']) and !empty($this->settings['databases']['default']['default']['driver'])){
      $driver = $this->settings['databases']['default']['default']['driver'];
    }
    if(isset($this->settings['databases']['default']['default']['prefix']) and !empty($this->settings['databases']['default']['default']['prefix'])){
      $prefix = $this->settings['databases']['default']['default']['prefix'];
    }
    
    $mysql = new mysql();
    $mysql->mysql_connect($host, $username, $password, $database);
    
    $query = "SELECT value FROM " . $prefix . "variable WHERE name = 'cron_key'";
    $row = $mysql->mysql_fetch_assoc_one($query);
    $value = unserialize($row['value']);
    
    $this->cron_key = $value;
  }
  
  public function getCronKey(){
    return $this->cron_key;
  }
  
  public function __destruct() {
    
  }
}