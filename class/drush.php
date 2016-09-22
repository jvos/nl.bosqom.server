<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
include_once 'class/exec.php';
include_once 'cron.php';

class drush {
  protected $exec;


  private $document_root = '';
  private $path = '/usr/bin';
  private $cd = '';
    
  public function __construct($exec, $document_root, $path = '/usr/bin') {
    if(empty($exec)){
      throw new Exception(drush::message('Class drush, function __construct, $exec is leeg !', 'error'));
    }
    if(empty($document_root)){
      throw new Exception(drush::message('Class drush, function __construct, $document_root is leeg !', 'error'));
    }
    
    $this->exec = $exec;
    
    $this->document_root = $document_root;
    $this->path = $path;
    $this->cd = sprintf("cd '%s'", $document_root); 
  }
  
  public function __destruct() {
    
  }
  
  public function exec($command){
    $commands[] = [$this->cd, false];
    $commands[] = $command;
    return $this->exec->run($commands);
  }
  
  public function refresh(){
    /*
     * This will refresh the list of available updates so Drush knows there is a new release for Drupal
     */
    $command = sprintf("%s/%s", $this->path, 'drush rf');
    return $this->exec([$command, true]);
  }
  
  public function offline($onoff = 0, $message = ''){
    
    //drush vset --yes maintenance_mode 1;
    if(!empty($message)){
      $command = sprintf("%s/%s %d '%s'", $this->path, 'drush vset --yes maintenance_mode', $onoff, $message);
    }else {
      $command = sprintf("%s/%s %d", $this->path, 'drush vset --yes maintenance_mode', $onoff);
    }
    return $this->exec([$command, false]);
  }
  
  public function clear_cache(){
    $command = sprintf("%s/%s -y", $this->path, 'drush cc all');
    return $this->exec([$command, false]);
  }
  
  public function clear_cache_views(){
    $command = sprintf("%s/%s -y", $this->path, 'drush cache-clear views');
    return $this->exec([$command, false]);
  }
  
  public function execute($cmd){
    if( false === strpos($cmd, 'drush')){
      drush::message('Class drush, function __construct, command is not a druhs command !', 'error');
      return false;
    }
    
    $command = sprintf("%s/%s -y", $this->path, $cmd);
    return $this->exec([$command, false]);
  }
  
  public function modules_themes($only_modules = false, $only_enabled = false, $except_core = true){
    $command = sprintf("%s/%s", $this->path, 'drush pm-list');
    
    if($only_modules){
      $command .= ' --type=module';
    }
    if($only_enabled){
      $command .= ' --status=enabled';
    }
    if($except_core){
      $command .= ' --no-core';
    }
    
    return $this->exec([$command, false]);    
  }
    
  public function list_modules_themes($output){
    $list = [];
        
    foreach ($output as $key => $string){
      $string = trim($string);
      $array = preg_split("/\s{2,}/", $string);
      
      if(!empty($array) and !empty($array[0]) and !empty($array[1]) and !empty($array[2])){
        
        $package = trim($array[0]);
        $naam = trim($array[1]);
        $versie = trim($array[2]);
        
        
        $module = '';
        if(!empty($package) and !empty($naam) and !empty($versie) and 'Package' != $package){
          // sometimes the Module is on the next line
          if(false !== strrpos($array[1], '(')){
            $module = substr($array[1], strrpos($array[1], '(')+1, strrpos($array[1], ')')-1-strrpos($array[1], '('));
          }else {
            if(false !== strrpos($array[1], '(')){
              $module = substr($output[($key+1)], strrpos($output[($key+1)], '(')+1, strrpos($output[($key+1)], ')')-1-strrpos($output[($key+1)], '('));
            }
          }
        }
        
        if(!empty($package) and !empty($naam) and !empty($versie) and !empty($module) and 'Package' != $package){
          $list[] = ['Package' => $package, 'Naam' => $naam, 'Versie' => $versie, 'Module' => $module];
        }
      }
    }
    
    return $list;
  }
    
  public function modules_update_status(){
    $command = sprintf("%s/%s", $this->path, 'drush pm-updatestatus');
        
    return $this->exec([$command, false]); 
  }
  
  public function list_modules_update_status($output){
    $list = [];
    $start = false; // there will be a large list of all the drupal modules, i want only the last modules (the ones that need to be updated)
    
    foreach ($output as $key => $string){
      $string = trim($string);
      $array = preg_split("/\s{2,}/", $string);
            
      if(!empty($array) and !empty($array[0]) and !empty($array[1]) and !empty($array[2])){
        if('Name' == $array[0]){
          $start = true;
        }        
        
        if($start){
          $name = trim($array[0]);
          $installed_version = trim($array[1]);
          $proposed_version = trim($array[2]);
          $message = trim($array[3]);

          if(!empty($package) and !empty($naam) and !empty($versie) and !empty($module) and 'Package' != $package){
            $list[] = ['Name' => $name, 'Installed Version' => $installed_version, 'Proposed version' => $proposed_version, 'Message' => $message];
          }
        }
      }
    }
    
    return $list;
  }
  
    
  public function enable_module($module){
    $command = sprintf("%s/%s %s -y", $this->path, 'drush pm-enable', $module);
    return $this->exec([$command, false]);  
  }

  public function disable_module($module){
    $command = sprintf("%s/%s %s -y", $this->path, 'drush pm-disable', $module);
    return $this->exec([$command, false]);  
  }
  
  public function status_modules(){
    $command = sprintf("%s/%s %s -y", $this->path, 'drush pm-disable', $module);
    return $this->exec([$command, false]); 
  }
  
  public function get_site_frontpage(){
    //drush vset site_frontpage node
    $command = sprintf("%s/%s", $this->path, 'vset site_frontpage node');
    return $this->exec([$command, false]);   
  }
  
  public function check_error($output){
    foreach ($output as $line){
      if (false !== strpos($line, '[error]')) {
        return true;
      }
    }
    return false;
  }
  
  public static function message($message, $status = 'info'){
    message($message, $status);
  }
}