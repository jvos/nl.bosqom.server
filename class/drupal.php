<?php

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
  
  private $theme = [];
  private $theme_civicrm_module_status = false;

  public function __construct($document_root, $server_name) {
    if(empty($document_root)){
      throw new Exception(civicrm::message('Class drupal, function __construct, $document_root is leeg !', 'error'));
    }
    if(empty($server_name)){
      throw new Exception(civicrm::message('Class drupal, function __construct, $server_name is leeg !', 'error'));
    }
    
    $this->document_root = $document_root;
    $this->server_name = $server_name;
    
    if($this->setSettings()){
      $this->setCronKey();
      $this->setSiteFrontPage();
      $this->setTheme();
    }
  }
  
  public function __destruct() {
    
  }
  
  public static function message($message, $status = 'info'){
    message($message, $status);
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
      return false;
    }
    
    return true;
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

  private function setSiteFrontPage(){        
    $mysql = new mysql();
    if($mysql->mysql_connect($this->host, $this->username, $this->password, $this->database)){
      $query = "SELECT value FROM " . $this->prefix . "variable WHERE name = 'site_frontpage'";
      
      $row = $mysql->mysql_fetch_assoc_one($query);
      $value = unserialize($row['value']);
      
      $this->site_frontpage = $value;
      return true;
    }else {
      return false;
    }
  }
    
  public function getSiteFrontPage(){
    return $this->site_frontpage;
  }
  
  public function changeSiteFrontPage($site_frontpage){    
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
    
  public function getUsersLastLogin(){
    $mysql = new mysql();
    $timestamp = mktime();
    
    if($mysql->mysql_connect($this->host, $this->username, $this->password, $this->database)){
      $query = "SELECT name, status, created, access FROM " . $this->prefix . "users ORDER BY access DESC LIMIT 10";
      
      $rows = $mysql->mysql_fetch_assoc($query);
      foreach ($rows as $key => $row){
        $rows[$key]['created'] = date('d-m-Y H:i:s', $row['created']);
        $rows[$key]['access'] = date('d-m-Y H:i:s', $row['access']);
        $rows[$key]['minutest a go'] = ($timestamp - $row['access']) / 60;
        $rows[$key]['hours a go'] = $rows[$key]['minutest a go'] / 60;
      }
      return $rows;
            
    }else {
      return false;
    }
  }
  
  public function getUsers($fields = ['*'], $wheres = [], $groupbys = [], $orderbys = []){
    $mysql = new mysql();
    if($mysql->mysql_connect($this->host, $this->username, $this->password, $this->database)){
      $query = "SELECT " . implode(', ', $fields) . " FROM " . $this->prefix . "users ";
      /*if(!empty($wheres)){
        $query .= "WHERE ";
      }
      foreach ($wheres as $field => ){
        $query .=
      }*/
      
      
      
      /*SET value = '" . serialize($site_frontpage) . "' WHERE name = 'site_frontpage'";
      
      if($result = $mysql->mysql_query($query)){
        return true;
      }
      return false;
      
    }else {
      return false;
    }*/
    }
  }
  
  private function setTheme(){
    $mysql = new mysql();
    
    // before we need to know if the civicrm theme modules is active
    if($mysql->mysql_connect($this->host, $this->username, $this->password, $this->database)){
      $query = "SELECT status FROM " . $this->prefix . "system WHERE name = 'civicrmtheme' LIMIT 1";
      
      $row = $mysql->mysql_fetch_assoc_one($query);
      if($row['status'] == '1'){
        $this->theme_civicrm_module_status = true;
      }else {
        $this->theme_civicrm_module_status = false;
      }
    }   
    
    if($mysql->mysql_connect($this->host, $this->username, $this->password, $this->database)){
      $query = "SELECT * FROM " . $this->prefix . "variable WHERE name = 'admin_theme' OR name = 'civicrmtheme_theme_admin' OR name = 'civicrmtheme_theme_public'";
      
      $rows = $mysql->mysql_fetch_assoc($query);
      foreach ($rows as $key => $row){
        $this->theme[$row['name']] = unserialize($row['value']); 
      }
      
      if(!isset($this->theme['admin_theme']) or empty($this->theme['admin_theme'])){
        return false;
      }
      
      if($this->theme_civicrm_module_status and (!isset($this->theme['civicrmtheme_theme_admin']) or empty($this->theme['civicrmtheme_theme_admin']))){
        return false;
      }
      
      if($this->theme_civicrm_module_status and (!isset($this->theme['civicrmtheme_theme_public']) or empty($this->theme['civicrmtheme_theme_public']))){
        return false;
      }
      
      return true;
    }else {
      return false;
    }
  }
  
  public function getTheme(){
    if(!isset($this->theme['admin_theme']) or empty($this->theme['admin_theme'])){
      return false;
    }

    if($this->theme_civicrm_module_status and (!isset($this->theme['civicrmtheme_theme_admin']) or empty($this->theme['civicrmtheme_theme_admin']))){
      return false;
    }

    if($this->theme_civicrm_module_status and (!isset($this->theme['civicrmtheme_theme_public']) or empty($this->theme['civicrmtheme_theme_public']))){
      return false;
    }
      
    return $this->theme;
  }
}