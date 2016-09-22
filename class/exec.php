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
      throw new Exception(exec::message('Class exec, function __construct, $user is leeg !', 'error'));
    }
    if(empty($pass)){
      throw new Exception(exec::message('Class exec, function __construct, $pass is leeg !', 'error'));
    }
    
    $this->user = $user;
    $this->pass = $pass;
  }
  
  public function __destruct() {
    
  }

  public function run($commands){
    if(empty($commands)){
      exec::message('Class exec, function run, $commands is leeg !', 'error');
      return false;
    }
    
    $command = '';
    $message = '';
    foreach($commands as $cmd){
      if(!isset($cmd[1]) or false == $cmd[1]){ // if we not need to use user and password
        $command .= $cmd[0] . ' && ';
        $message .= $cmd[0] . ' && ';
      }else {
        $command .= sprintf("echo '%s' | sudo -S %s", $this->pass, $cmd[0]) . ' && ';
        $message .= sprintf("echo (gebruikersnaam en wachtwoord niet zichtbaar) | sudo -S %s", $cmd[0]) . ' && ';
      }
    }
    
    $command = substr($command, 0, -4);
    $command .= ' 2>&1';
    
    $message = substr($message, 0, -4);
    $message .= ' 2>&1';
    
    exec::message_save(sprintf("Class exec, function run, Het volgende command word uitgevoerd: %s", $message));
    exec::message_display(sprintf("Class exec, function run, Het volgende command word uitgevoerd: %s", $command));
    
    $output = [];
    $return_var = -1;
    
    exec($command, $output, $return_var);
    //exec::message(sprintf("Class exec, function run, De return van het command: %s", $return_var));
    exec::message_save(sprintf("Class exec, function run, De return van het command: %s", $return_var));
    
    //exec::message('Class exec, function run, De ouput van het command: ');
    exec::message_save('Class exec, function run, De ouput van het command: ');
    exec::message_save(implode(PHP_EOL, $output)) . PHP_EOL;
    
    if(empty($return_var)){
      return $output;
      
    }else {
      exec::message('Class exec, function run, Er is een error bij het uitvoeren van het command !', 'error');
      return false;
    }
  }
  
  public static function message($message, $status = 'info'){
    message($message, $status);
  }
  
  public static function message_save($message, $status = 'info'){
    message_save($message, $status);
  }
  
  public static function message_display($message, $status = 'info'){
    message_display($message, $status);
  } 
}