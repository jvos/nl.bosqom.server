<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
include_once 'exec.php';

class cron {
  
  public static function executeUrl ($url){
    if(empty($url)){
      throw new Exception('[ERROR] Class cron, function executeUrl, $url is empty !');
    }
    
    cron::message(sprintf("Class cron, function executeUrl, de volgende url word aangeroepen: %s", $url));
        
    // Create a curl handle to a non-existing location
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $content = '';
    if( ($content = curl_exec($ch) ) === false){
      cron::message(sprintf("Class cron, function executeUrl, curl_exec error: %s", curl_error($ch)), 'error');
      
      // Close handle
      curl_close($ch);
      return false;
      
    }else {
      // Close handle
      curl_close($ch);
      return true;
    } 
  }
  
  public static function downloadUrl($url, $path, $name){
    if(empty($url)){
      throw new Exception('[ERROR] Class cron, function downloadUrl, $url is empty !');
    }
    if(empty($path)){
      throw new Exception('[ERROR] Class cron, function downloadUrl, $path is empty !');
    }
    
    cron::message(sprintf("Class cron, function downloadUrl, de volgende url word aangeroepen: %s, naar %s/%s !", $url, $path, $name));
    
    if(!$fp = fopen($path . '/' . $name, 'w+')){ //This is the file where we save the information
      cron::message(sprintf("Class cron, function downloadUrl, fopen error !"), 'error');
      fclose($fp);
      return false;
    }
    
    $ch = curl_init(str_replace(' ', '%20', $url));//Here is the file we are downloading, replace spaces with %20
    
    curl_setopt($ch, CURLOPT_TIMEOUT, 50);
    curl_setopt($ch, CURLOPT_FILE, $fp); // write curl response to file
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    
    if(false === curl_exec($ch)){
      cron::message(sprintf("Class cron, function downloadUrl, curl_exec error: %s", curl_error($ch)), 'error');
      
      // Close handle
      curl_close($ch);
      fclose($fp);
      return false;
    }
    
    // Close handle
    curl_close($ch);
    fclose($fp);
    return true;
  }

  public static function message($message, $status = 'info'){
    message($message, $status);
  }
}