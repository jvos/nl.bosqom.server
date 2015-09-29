<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class mysql {
  
  private $link = '';
  
  public function __construct() {
    
  }
  
  public function mysql_connect($host, $username, $password, $database){
    if(!$this->link = mysql_connect($host, $username, $password)) { 
      echo('Class mysql, function mysql_connect, error mysql_connect ' . mysql_error) . PHP_EOL;
      return false;
    } 
    elseif(!mysql_select_db($database, $this->link)) { 
      echo('Class mysql, function mysql_connect, error mysql_select_db ' . mysql_error) . PHP_EOL;
      return false;
    } 
  }
  
  public function mysql_query($query){
    $result = mysql_query($query, $this->link);
    
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
    $row = mysql_fetch_assoc($result)
    return $row;
  }
  

  public function __destruct() {
    if(!empty($this->link)){
      mysql_close($this->link);
    }
  }
}