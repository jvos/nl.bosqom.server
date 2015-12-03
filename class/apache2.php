<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
class apache2 {
  private $path = '/etc/apache2';
  
  private $mods_type = ['mods-available', 'mods-enabled'];
  
  private $sites_type = ['sites-available', 'sites-enabled'];
  private $sites = [];
  
  public function __construct() {
    $this->setVirtualHost();
  }
  
  public static function display_message($message, $status = 'info'){
    display_message($message, $status);
  }
  
  private function setVirtualHost(){
    $this->sites = [];
    foreach($this->sites_type as $site_type){
      $this->sites[$site_type] = [];
      
      $files_conf = scandir($this->path . '/' . $site_type);
      $files_conf = array_diff($files_conf, array('..', '.'));
      
      foreach($files_conf as $file_conf){
        $this->sites[$site_type][$file_conf] = [];
        
        if(!$handle = fopen($this->path . '/' . $site_type . '/' . $file_conf, 'r')){
          apache2::display_message(sprintf('Class apache2, function getVirtualHost, can not open %s !', $this->path . '/' . $site_type . '/' . $file_conf), 'error');
          
        }else {
          while(!feof($handle)){
            $line = fgets($handle);
            $line = trim($line);
            
            $tokens = explode(' ',$line);
            if(!empty($tokens)){
              if(isset($tokens[0]) and isset($tokens[1])){
                $tokens[1] = str_replace('"', "", $tokens[1]);
                $tokens[1] = str_replace("'", "", $tokens[1]);
                $tokens[1] = trim($tokens[1]);

                if(strtolower($tokens[0]) == 'documentroot'){
                  $this->sites[$site_type][$file_conf]['DocumentRoot'] = $tokens[1];
                }
                if(strtolower($tokens[0]) == 'servername'){
                  $this->sites[$site_type][$file_conf]['ServerName'] = $tokens[1];
                }
                if(strtolower($tokens[0]) == 'serveralias'){
                  $this->sites[$site_type][$file_conf]['ServerAlias'] = $tokens[1];
                }

                if(strtolower($tokens[0]) == 'errorlog'){
                  $this->sites[$site_type][$file_conf]['ErrorLog'] = $tokens[1];
                }
                if(strtolower($tokens[0]) == 'customlog'){
                  $this->sites[$site_type][$file_conf]['CustomLog'] = $tokens[1];
                }
                if(strtolower($tokens[0]) == 'loglevel'){
                  $this->sites[$site_type][$file_conf]['LogLevel'] = $tokens[1];
                }
              }
            }
          }
          fclose($handle);
          
        }
      }
    }
  }
  
  public function getVirtualHost(){
    return $this->sites;
  }
  
  public function getVirtualHostBySiteType($site_type = 'sites-enabled'){
    return $this->sites[$site_type];
  }
  
  public function __destruct() {
    
  }
}