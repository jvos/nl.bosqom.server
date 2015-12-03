<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
class exec {  
  protected $user = '';
  protected $pass = '';
  
  public function __construct($user, $pass) {
    if(empty($user)){
      throw new Exception('[ERROR] Class exec, function __construct, $user is leeg !');
    }
    if(empty($pass)){
      throw new Exception('[ERROR] Class exec, function __construct, $pass is leeg !');
    }
    
    $this->user = $user;
    $this->pass = $pass;
  }
  
  public function __destruct() {
    
  }

  public function run($commands){
    if(empty($commands)){
      exec::display_message('Class exec, function run, $commands is leeg !', 'error');
      return false;
    }
    
    $command = '';
    foreach($commands as $cmd){
      if(!isset($cmd[1]) or false == $cmd[1]){
        $command .= $cmd[0] . ' && ';
      }else {
        $command .= sprintf("echo '%s' | sudo -S %s", $this->pass, $cmd[0]) . ' && ';
      }
    }
    
    $command = substr($command, 0, -4);
    $command .= ' 2>&1';
    
    exec::display_message(sprintf("Class exec, function run, Het volgende command word uitgevoerd: %s", $command));
    
    $output = [];
    $return_var = -1;
    
    exec($command, $output, $return_var);
    exec::display_message(sprintf("Class exec, function run, De return van het command: %s", $return_var));
    
    exec::display_message('Class exec, function run, De ouput van het command: ');
    echo(implode(PHP_EOL, $output)) . PHP_EOL;
    
    if(empty($return_var)){
      return $output;
      
    }else {
      exec::display_message('Class exec, function run, Er is een error bij het uitvoeren van het command !', 'error');
      return false;
    }
  }
  
  public static function display_message($message, $status = 'info'){
    display_message($message, $status);
  }
}