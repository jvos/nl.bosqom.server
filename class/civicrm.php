<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
include_once 'cron.php';

class civicrm {
  private $document_root = '';
  private $server_name = '';  
  private $backup_dir = '';  
  
  private $settings = [];
  
  private $databases = [];
  private $database = '';
  private $username = '';
  private $password = '';
  private $host = '';
  private $port = '';
  private $driver = '';
  private $prefix = '';
  
  private $civicrm_domain = '';
  
  private $custom_template_dir = '';
  
  public function __construct($document_root, $server_name, $backup_dir) {
    if(empty($document_root)){
      throw new Exception(civicrm::message('Class civicrm, function __construct, $document_root is leeg !', 'error'));
    }
    if(empty($server_name)){
      throw new Exception(civicrm::message('Class civicrm, function __construct, $server_name is leeg !', 'error'));
    }
    if(empty($backup_dir)){
      throw new Exception(civicrm::message('Class civicrm, function __construct, $backup_dir is leeg !', 'error'));
    }
    
    $this->document_root = $document_root;
    $this->server_name = $server_name;
    $this->backup_dir = $backup_dir;
    
    $this->setSettings();
  }
  
  public function __destruct() {
    
  }
  
  public static function message($message, $status = 'info'){
    message($message, $status);
  }

  private function setSettings(){
    if (file_exists($this->document_root . '/sites/default/civicrm.settings.php')) {
      
      $CIVICRM_UF_DSN = '';
      $CIVICRM_DSN = '';
      
      $CIVICRM_USE_MEMCACHE = '';
      $CIVICRM_MEMCACHE_HOST = '';
      $CIVICRM_MEMCACHE_PORT = '';
      $CIVICRM_MEMCACHE_TIMEOUT = '';
      $CIVICRM_MEMCACHE_PREFIX = '';
      
      $CIVICRM_IDS_ENABLE = '';
      
      $CIVICRM_TEMPLATE_COMPILEDIR = '';
      
      ob_start();
      include $this->document_root . '/sites/default/civicrm.settings.php';
      $CIVICRM_UF_DSN = constant("CIVICRM_UF_DSN");
      $CIVICRM_DSN = constant("CIVICRM_DSN");
      
      if(defined('CIVICRM_USE_MEMCACHE')){
        $CIVICRM_USE_MEMCACHE = constant("CIVICRM_USE_MEMCACHE");
      }else {
        $CIVICRM_USE_MEMCACHE = '';
      }
      if(defined('CIVICRM_MEMCACHE_HOST')){
        $CIVICRM_MEMCACHE_HOST = constant("CIVICRM_MEMCACHE_HOST");
      }else {
        $CIVICRM_MEMCACHE_HOST = '';
      }  
      if(defined('CIVICRM_MEMCACHE_PORT')){
      $CIVICRM_MEMCACHE_PORT = constant("CIVICRM_MEMCACHE_PORT");
      }else {
        $CIVICRM_MEMCACHE_PORT = '';
      }
      if(defined('CIVICRM_MEMCACHE_TIMEOUT')){
        $CIVICRM_MEMCACHE_TIMEOUT = constant("CIVICRM_MEMCACHE_TIMEOUT");
      }else {
        $CIVICRM_MEMCACHE_TIMEOUT = '';
      }
      if(defined('CIVICRM_MEMCACHE_PREFIX')){
        $CIVICRM_MEMCACHE_PREFIX = constant("CIVICRM_MEMCACHE_PREFIX");
      }else {
        $CIVICRM_MEMCACHE_PREFIX = '';
      }
      
      if(defined('CIVICRM_IDS_ENABLE')){
        $CIVICRM_IDS_ENABLE = constant("CIVICRM_IDS_ENABLE");
      }else {
        $CIVICRM_IDS_ENABLE = '';
      }
      
      if(defined('CIVICRM_TEMPLATE_COMPILEDIR')){
        $CIVICRM_TEMPLATE_COMPILEDIR = constant("CIVICRM_TEMPLATE_COMPILEDIR");
      }else {
        $CIVICRM_TEMPLATE_COMPILEDIR = '';
      }
            
      $content = ob_get_contents();
      ob_end_clean();
            
      $this->settings['CIVICRM_UF_DSN'] = $CIVICRM_UF_DSN;
      $this->settings['CIVICRM_DSN'] = $CIVICRM_DSN;
      
      $this->settings['CIVICRM_USE_MEMCACHE'] = $CIVICRM_USE_MEMCACHE;
      $this->settings['CIVICRM_MEMCACHE_HOST'] = $CIVICRM_MEMCACHE_HOST;
      $this->settings['CIVICRM_MEMCACHE_PORT'] = $CIVICRM_MEMCACHE_PORT;
      $this->settings['CIVICRM_MEMCACHE_TIMEOUT'] = $CIVICRM_MEMCACHE_TIMEOUT;
      $this->settings['CIVICRM_MEMCACHE_PREFIX'] = $CIVICRM_MEMCACHE_PREFIX;
      
      $this->settings['CIVICRM_IDS_ENABLE'] = $CIVICRM_IDS_ENABLE;
      
      $this->settings['CIVICRM_TEMPLATE_COMPILEDIR'] = $CIVICRM_TEMPLATE_COMPILEDIR;
            
      list($this->username, $this->password, $this->host, $this->database) = sscanf($CIVICRM_DSN, "mysql://%[^:@?/]:%[^:@?/]@%[^:@?/]/%[^:@?/]?new_link=true");
            
      $this->databases['database'] = $this->database;
      $this->databases['username'] = $this->username;
      $this->databases['password'] = $this->password;
      $this->databases['host'] = $this->host;
      
    }else {
      civicrm::message('Class civicrm, function setSettings, Geen civicrm.settings.php bestand !', 'error');
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
  
  public function download($version, $drupal_version = '7'){
    // http://downloads.sourceforge.net/project/civicrm/civicrm-stable/4.6.9/civicrm-4.6.9-drupal.tar.gz?r=http%3A%2F%2Fsourceforge.net%2Fprojects%2Fcivicrm%2Ffiles%2Fcivicrm-stable%2F4.6.9%2F&ts=1446457741&use_mirror=netcologne
    if('7' == $drupal_version){
      //$url = sprintf('http://downloads.sourceforge.net/project/civicrm/civicrm-stable/%s/civicrm-%s-drupal.tar.gz?r=http://sourceforge.net/projects/civicrm/filescivicrm-stable/%s', $version, $version, $version);
      $url = sprintf('http://netix.dl.sourceforge.net/project/civicrm/civicrm-stable/%s/civicrm-%s-drupal.tar.gz', $version, $version);
    }elseif('6' == $drupal_version) {
      // http://downloads.sourceforge.net/project/civicrm/civicrm-stable/4.6.9/civicrm-4.6.9-drupal6.tar.gz?r=http%3A%2F%2Fsourceforge.net%2Fprojects%2Fcivicrm%2Ffiles%2Fcivicrm-stable%2F4.6.9%2F&ts=1446457942&use_mirror=netcologne
      //$url = sprintf('http://downloads.sourceforge.net/project/civicrm/civicrm-stable/%s/civicrm-%s-drupal%s.tar.gz?r=http://sourceforge.net/projects/civicrm/files/civicrm-stable/%s', $version, $version, $drupal_version, $version);
      $url = sprintf('http://netix.dl.sourceforge.net/project/civicrm/civicrm-stable/%s/civicrm-%s-drupal%s.tar.gz', $version, $version, $drupal_version);
    }
    
    if(!cron::downloadUrl($url, $this->backup_dir, sprintf('civicrm-%s-drupal.tar.gz', $version))){
      return false;
    }
    
    // http://downloads.sourceforge.net/project/civicrm/civicrm-stable/4.6.9/civicrm-4.6.9-l10n.tar.gz?r=http%3A%2F%2Fsourceforge.net%2Fprojects%2Fcivicrm%2Ffiles%2Fcivicrm-stable%2F4.6.9%2F&ts=1446457755&use_mirror=skylink
    //$url = sprintf('http://downloads.sourceforge.net/project/civicrm/civicrm-stable/%s/civicrm-%s-l10n.tar.gz?r=http://sourceforge.net/projects/civicrm/files/civicrm-stable/%s/', $version, $version, $version);
    $url = sprintf('http://netix.dl.sourceforge.net/project/civicrm/civicrm-stable/%s/civicrm-%s-l10n.tar.gz', $version, $version, $version);
    
    if(!cron::downloadUrl($url, $this->backup_dir, sprintf('civicrm-%s-l10n.tar.gz', $version))){
      return false;
    }
    
    return true;
  }
  
  public function getLocalizationSettings(){
    $mysql = new mysql();
    if($mysql->mysql_connect($this->host, $this->username, $this->password, $this->database)){
      $query = "SELECT * FROM " . $this->prefix . "civicrm_domain WHERE id = '1'";
      
      $row = $mysql->mysql_fetch_assoc_one($query);
      
      
      $config_backend = preg_replace('!s:(\d+):"(.*?)";!e', "'s:'.strlen('$2').':\"$2\";'", $row['config_backend']);
      echo('$row[config_backend]: ' . $config_backend) . PHP_EOL;
      
      $config_backend = unserialize($config_backend);
      
      
      echo('$config_backend: <pre>');
      print_r($config_backend);
      echo('</pre>');
      
      $this->civicrm_domain = $config_backend;
      
      if(empty($row['config_backend'])){
        return false;
      }else {
        
      }
    }else {
      return false;
    }
    return $this->civicrm_domain;
  }
  
  /*public function getCustomTemplateDir(){
    $mysql = new mysql();
    if($mysql->mysql_connect($this->host, $this->username, $this->password, $this->database)){
      $query = "SELECT * FROM " . $this->prefix . "civicrm_setting WHERE name = 'customTemplateDir'";
      
      $row = $mysql->mysql_fetch_assoc_one($query);
      $custom_template_dir = unserialize($row['value']);
      
      $this->custom_template_dir = $custom_template_dir;
      
      if(empty($row['value'])){
        return false;
      }else {
        
      }
    }else {
      return false;
    }
    return $this->custom_template_dir;
  }*/
}