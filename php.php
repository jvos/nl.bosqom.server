<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
include_once 'linux.php';

class php {
  private $linux;
  
  private $mypid = 0;
  private $server = [];
  private $filename = '';
  private $filepath = '';
  
  public function __construct($linux) {
    if(empty($linux)){
      throw new Exception('[ERROR] Class linux, function run, $linux is leeg !');
    }
    
    $this->linux = $linux;
    
    $this->setmypid();
    $this->setServer();
    $this->setFileName();
    $this->setFilePath();
  }
  
  public function __destruct() {
    
  }
  
  private function setmypid(){
    $this->mypid = getmypid();
  }
  
  private function setServer(){
    $this->server = $_SERVER;
  }
  
  private function setFileName(){
    $this->filename = substr($this->server['SCRIPT_FILENAME'], strrpos($this->server['SCRIPT_FILENAME'], '/')+1);
  }
  
  private function setFilePath(){
   $this->filepath = substr($this->server['SCRIPT_FILENAME'], 0, strrpos($this->server['SCRIPT_FILENAME'], '/'));
  }
  
  public function isRunning(){    
    $ps_auxes = $this->linux->ps_aux('php');

    $count = 0;
    foreach ($ps_auxes as $ps_aux){
      if(false !== strpos($ps_aux, $this->filename)){
        $count++;
      }
    }
    
    if($count >= 2){
      return true;
    }
    
    return false;
  }
}