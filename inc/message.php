<?php

function message_color($message, $status = 'info'){
  switch($status){
    case 'error':
      return "\033[31m [ERROR] \033[0m" . ' ' . $message;
      break;
    case 'success':
      return "\033[32m [SUCCESS] \033[0m" . ' ' . $message;
      break;
    case 'warning':
      return "\033[1;33m [WARNING] \033[0m" . ' ' . $message;
      break;
    default:
      return "\033[34m [INFO] \033[0m" . ' ' . $message;
  }
}

function message_clean($message, $status = 'info'){
  switch($status){
    case 'error':
      return "[ERROR] " . ' ' . $message;
      break;
    case 'success':
      return "[SUCCESS] " . ' ' . $message;
      break;
    case 'warning':
      return "[WARNING] " . ' ' . $message;
      break;
    default:
      return "[INFO] " . ' ' . $message;
  }
}

function message($message, $status = 'info'){  
  message_save($message, $status);
  message_display($message, $status);
}

function message_display($message, $status = 'info'){
  echo(message_color($message, $status)) . PHP_EOL;
}

function message_save($message, $status = 'info'){  
  global $datetime;
    
  if(false === strpos($_SERVER['SCRIPT_FILENAME'], '/')){
    $filename = $_SERVER['SCRIPT_FILENAME'];
    $filepath = '';
  }else {
    $filename = substr($_SERVER['SCRIPT_FILENAME'], strrpos($_SERVER['SCRIPT_FILENAME'], '/')+1);
    $filepath = substr($_SERVER['SCRIPT_FILENAME'], 0, strrpos($_SERVER['SCRIPT_FILENAME'], '/')+1);
  }
  
  $path_dir = $_SERVER['PWD'] . '/' . $filepath . 'log/';
  $path = $path_dir . $filename . '_' . $datetime . '.log';
  
  if (!file_exists($path_dir)) {
    mkdir($path_dir, 0770, true);
  }
  
  if (!$handle = fopen($path, 'a')) { // a meens to append the text to the end of the file
       message_display(sprintf("Kan log bestand (%s) niet openen !", $path));
       exit;
  }
  
  if (fwrite($handle, message_clean($message, $status) . PHP_EOL) === FALSE) {
      message_display(sprintf("Kan niet naar log bestand (%s) schrijven !", $path));
      exit;
  }

  fclose($handle);
}

function message_table($data, $headers){  
  // set lenght
  $lengths = [];
  $i = 0;
  foreach ($headers as $header){
    $lengths[$i] = strlen($header);
    $i++;
  }
  
  foreach ($data as $rows => $row){
    $i = 0;
    foreach($row as $name => $value){
      if(is_object($value)){
        $value = (array) $value;
      }
      if(is_array($value)){
        $value = message_array_to_string($value);
      }
      
      if($lengths[$i] <= strlen($value)){
        $lengths[$i] = strlen($value);
      }
      $i++;
    }
  }
  
  $table = [];
  // add with spaces after by highest text lenght
  foreach ($data as $rows => $row){
    $i = 0;
    $table[$rows] = [];
    foreach($row as $name => $value){
      if(is_object($value)){
        $value = (array) $value;
      }
      if(is_array($value)){
        $value = message_array_to_string($value);
      }
      
      $table[$rows][$i] = $value . str_repeat(' ', ($lengths[$i]-strlen($value))) . '  ';
      $i++;
    }
  }
  
  $ths = [];
  $i = 0;
  foreach ($headers as $header){
    $ths[$i] = $header . str_repeat(' ', ($lengths[$i]-strlen($header))) . '  ';
    $i++;
  }
  
  message(implode('  ', $ths));
  
  foreach ($table as $tds => $td){
    message(implode('  ', $td));
  }
  
  return $table;
}

function message_array_to_string($array){
  $return = '';
  if(is_array($array)){
    foreach($array as $key => $value){
      $return .= $key;
      if(is_array($value)){
        $return .= message_array_to_string($value);
      }else {
        $return .= ' => ' . $value . ', ';
      }
    }
  }
  return $return;
}