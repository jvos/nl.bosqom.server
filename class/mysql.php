<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class mysql {
  
  private $link;
  
  public function __construct() {
    
  }
  
  public function __destruct() {
    if(!empty($this->link)){
      mysql_close($this->link);
    }
  }
  
  public static function message($message, $status = 'info'){
    message($message, $status);
  }
  
  public function mysql_connect($host, $username, $password, $database){
    if(!$this->link = mysql_connect($host, $username, $password)) { 
      mysql::message(sprintf("Class mysql, function mysql_connect, error %s", mysql_error), 'error');
      return false;
    } 
    elseif(!mysql_select_db($database, $this->link)) { 
      mysql::message(sprintf("Class mysql, function mysql_select_db, error %s", mysql_error), 'error');
      return false;
    } 
    return true;
  }
  
  public function mysql_query($query){
    $result = mysql_query($query, $this->link);
    return $result;
  }
  
  public function mysql_fetch_assoc($query){
    $result = $this->mysql_query($query);
    $rows = [];
    while($row = mysql_fetch_assoc($result)){
      $rows[] = $row;
    }
    return $rows;
  }
  
  public function mysql_fetch_assoc_one($query){
    $result = $this->mysql_query($query);
    $row = mysql_fetch_assoc($result);
    return $row;
  }
}