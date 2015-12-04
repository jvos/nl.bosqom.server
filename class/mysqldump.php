<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
include_once 'exec.php';

class mysqldump {
  protected $exec;

  private $host;
  private $user;
  private $password;
  private $database;
  private $file;


  public function __construct($exec, $host, $user, $password, $database, $file) {
    if(empty($exec)){
      throw new Exception('[ERROR] Class mysqldump, function __construct, $exec is leeg !');
    }
    if(empty($host)){
      throw new Exception('[ERROR] Class mysqldump, function __construct, $host is leeg !');
    }
    if(empty($user)){
      throw new Exception('[ERROR] Class mysqldump, function __construct, $user is leeg !');
    }
    if(empty($password)){
      throw new Exception('[ERROR] Class mysqldump, function __construct, $password is leeg !');
    }
    if(empty($database)){
      throw new Exception('[ERROR] Class mysqldump, function __construct, $database is leeg !');
    }
    if(empty($file)){
      throw new Exception('[ERROR] Class mysqldump, function __construct, $file is leeg !');
    }
        
    $this->exec = $exec;
    
    $this->host = $host;
    $this->user = $user;
    $this->password = $password;
    $this->database = $database;
    $this->file = $file;
  }
  
  public function __destruct() {
    
  }
  
  public static function message($message, $status = 'info'){
    message($message, $status);
  }

  public function dump(){
    /*if ('all' == $this->database){
      $command = sprintf("mysqldump -h %s -u %s -p%s --all-databases > %s", $this->host, $this->user, $this->password, $this->file);
      
    }elseif (is_array($this->database)){
      $command = sprintf("mysqldump -h %s -u %s -p%s --databases %s > %s", $this->host, $this->user, $this->password, implode(' ', $this->database), $this->file);
      
    }else {
      $command = sprintf("mysqldump -h %s -u %s -p%s %s > %s", $this->host, $this->user, $this->password, $this->database, $this->file);
    }*/
    
    $command = sprintf("mysqldump -h %s -u %s -p%s %s > %s", $this->host, $this->user, $this->password, $this->database, $this->file);
    
    return $this->exec->run([[$command, false]]);
  }
  
  public function restore(){
    $command = sprintf("mysql -h %s -u %s -p%s %s < %s", $this->host, $this->user, $this->password, $this->database, $this->file);
    
    return $this->exec->run([[$command, false]]);
  }
}