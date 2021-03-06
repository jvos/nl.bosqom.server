<?php

class php {
  private $linux;
  
  private $mypid = 0;
  private $server = [];
  private $filename = '';
  private $filepath = '';
  
  public function __construct($linux) {
    if(empty($linux)){
      throw new Exception(php::message('Class linux, function run, $linux is leeg !', 'error'));
    }
    
    $this->linux = $linux;
    
    $this->setmypid();
    $this->setServer();
    $this->setFileName();
    $this->setFilePath();
  }
  
  public function __destruct() {
    
  }
  
  public static function message($message, $status = 'info'){
    message($message, $status);
  }
  
  private function setmypid(){
    $this->mypid = getmypid();
  }
  
  private function setServer(){
    $this->server = $_SERVER;
  }
  
  private function setFileName(){
    if(false === strpos($_SERVER['SCRIPT_FILENAME'], '/')){
      $this->filename = $_SERVER['SCRIPT_FILENAME'];
    }else {
      $this->filename = substr($this->server['SCRIPT_FILENAME'], strrpos($this->server['SCRIPT_FILENAME'], '/')+1);
    }
  }
  
  private function setFilePath(){
    if(false === strpos($_SERVER['SCRIPT_FILENAME'], '/')){
      $this->filepath = '';
    }else {
      $this->filepath = substr($this->server['SCRIPT_FILENAME'], 0, strrpos($this->server['SCRIPT_FILENAME'], '/'));
    }
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
  
  public function readdir($path){
    $dirs = [];
    if ($handle = opendir($path)) {
      while (false !== ($entry = readdir($handle))) {
        if ($entry != "." && $entry != "..") {
          $dirs[] = $path . $entry;
        }
      }
      closedir($handle);
      
    }else {
      throw new Exception(php::message(sprintf('Kan map (%s) niet openen !', $path), 'error'));
    }
    return $dirs;
  }
  
  public function rmdir($paths){
    foreach($paths as $path){
      if(false == rmdir($path)){
        throw new Exception(php::message(sprintf('Kan map (%s) niet verwijderen !', $path), 'error'));
      }
    }
    
    return true;
  }
}