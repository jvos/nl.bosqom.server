<?php

class linux {
  private $exec;
  
  public function __construct($exec) {
    if(empty($exec)){
      throw new Exception('[ERROR] Class exec, function run, $exec is leeg !');
    }
    
    $this->exec = $exec;
  }
  
  public function __destruct() {
    
  }
  
  public static function message($message, $status = 'info'){
    message($message, $status);
  }
  
  public function chown($path, $owner_groep, $is_file = false){
    if(empty($path)){
      linux::message('Class linux, function chown, $path is leeg !', 'error');
      return false;
    }
    if(empty($owner_groep)){
      linux::message('Class linux, function chown, $owner_groep is leeg !', 'error');
      return false;
    }
    
    if($is_file){
      $command = sprintf("chown %s '%s'", $owner_groep, $path);
    }else {
      $command = sprintf("chown -R %s '%s'", $owner_groep, $path);
    }
    
    return $this->exec->run([[$command, true]]);
  }
  
  public function chmod($path, $permission, $is_file = false){
    if(empty($path)){
      linux::message('Class linux, function chmod, $path is leeg !', 'error');
      return false;
    }
    if(empty($permission)){
      linux::message('Class linux, function chmod, $permission is leeg !', 'error');
      return false;
    }
    
    if($is_file){
      $command = sprintf("chmod %s '%s'", $permission, $path);
    }else {
      $command = sprintf("chmod -R %s '%s'", $permission, $path);
    }
    
    return $this->exec->run([[$command, true]]);
  }
  
  public function cp($path, $to_path){
    if(empty($path)){
      linux::message('Class linux, function cp, $path is leeg !', 'error');
      return false;
    }
    if(empty($to_path)){
      linux::message('Class linux, function cp, $to_path is leeg !', 'error');
      return false;
    }
    
    $command = sprintf("cp -rf '%s' '%s'", $path, $to_path);
    
    return $this->exec->run([[$command, true]]);
  }
  
  public function ps_aux($grep = ''){
    $command = sprintf("ps aux");
    if(!empty($grep)){
      $command .= sprintf(" | grep '%s'", $grep);
    }
    
    return $this->exec->run([[$command]]);
  }
}