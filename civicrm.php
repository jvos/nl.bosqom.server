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
  
  public function __construct($document_root, $server_name, $backup_dir) {
    if(empty($document_root)){
      throw new Exception('[ERROR] Class civicrm, function __construct, $document_root is leeg !');
    }
    if(empty($server_name)){
      throw new Exception('[ERROR] Class civicrm, function __construct, $server_name is leeg !');
    }
    if(empty($backup_dir)){
      throw new Exception('[ERROR] Class civicrm, function __construct, $backup_dir is leeg !');
    }
    
    $this->document_root = $document_root;
    $this->server_name = $server_name;
    $this->backup_dir = $backup_dir;
    
    $this->setSettings();
  }
  
  public function __destruct() {
    
  }

  private function setSettings(){
    if (file_exists($this->document_root . '/sites/default/civicrm.settings.php')) {
      
      $CIVICRM_UF_DSN = '';
      $CIVICRM_DSN = '';
      
      ob_start();
      include $this->document_root . '/sites/default/civicrm.settings.php';
      $CIVICRM_UF_DSN = constant("CIVICRM_UF_DSN");
      $CIVICRM_DSN = constant("CIVICRM_DSN");
      $content = ob_get_contents();
      ob_end_clean();
      
      
      $this->settings['CIVICRM_UF_DSN'] = $CIVICRM_UF_DSN;
      $this->settings['CIVICRM_DSN'] = $CIVICRM_DSN;
      
      list($this->username, $this->password, $this->host, $this->database) = sscanf($CIVICRM_DSN, "mysql://%[^:@?/]:%[^:@?/]@%[^:@?/]/%[^:@?/]?new_link=true");
            
      $this->databases['database'] = $this->database;
      $this->databases['username'] = $this->username;
      $this->databases['password'] = $this->password;
      $this->databases['host'] = $this->host;
      
    }else {
      civicrm::display_message('Class civicrm, function setSettings, Geen civicrm.settings.php bestand !', 'error');
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
  
  public static function display_message($message, $status = 'info'){
    display_message($message, $status);
  }
}