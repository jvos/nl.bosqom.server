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
      if (version_compare(PHP_VERSION, '5.0.0', '<')) {
        mysql_close($this->link);
      }else {
        mysqli_close($this->link);
      }
    }
  }
  
  public static function message($message, $status = 'info'){
    message($message, $status);
  }
  
  public function mysql_connect($host, $username, $password, $database){
    if (version_compare(PHP_VERSION, '5.0.0', '<')) {
      if(!$this->link = mysql_connect($host, $username, $password)) { 
        mysql::message(sprintf("Class mysql, function mysql_connect, error %s", mysql_error), 'error');
        return false;
      } 
      elseif(!mysql_select_db($database, $this->link)) { 
        mysql::message(sprintf("Class mysql, function mysql_select_db, error %s", mysql_error), 'error');
        return false;
      }
      
    }else {
      if(!$this->link = mysqli_connect($host, $username, $password)) { 
        mysql::message(sprintf("Class mysql, function mysql_connect, error %s", mysqli_error($this->link)), 'error');
        return false;
      } 
      elseif(!mysqli_select_db($this->link, $database)) { 
        mysql::message(sprintf("Class mysql, function mysql_select_db, error %s", mysqli_error($this->link)), 'error');
        return false;
      }
    }
    return true;
  }
  
  public function mysql_query($query){
    if (version_compare(PHP_VERSION, '5.0.0', '<')) {
      $result = mysql_query($query, $this->link);
      if(!$result) {
        throw new Exception(mysql::message(sprintf('Class mysql, function mysql_query, mysql_query mislukt ! Met error %s', mysql_error()), 'error'));
      }
    }else {
      $result = mysqli_query($this->link, $query);
      if(!$result) {
        throw new Exception(mysql::message(sprintf('Class mysql, function mysql_query, mysql_query mislukt ! Met error %s', mysqli_error($this->link)), 'error'));
      }
    }
    return $result;
  }
  
  public function mysql_fetch_assoc($query){
    $result = $this->mysql_query($query);
    
    if (version_compare(PHP_VERSION, '5.0.0', '<')) {
      $rows = [];
      while($row = mysqli_fetch_assoc($result)){
        $rows[] = $row;
      }
    }else {
      $rows = [];
      while($row = mysqli_fetch_assoc($result)){
        $rows[] = $row;
      }
    }
    return $rows;
  }
  
  public function mysql_fetch_assoc_one($query){
    $result = $this->mysql_query($query);
    if (version_compare(PHP_VERSION, '5.0.0', '<')) {
      $row = mysql_fetch_assoc($result);
    }else {
      $row = mysqli_fetch_assoc($result);
    }
    return $row;
  }
}