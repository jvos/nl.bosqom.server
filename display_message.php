<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
function display_message($message, $status = 'info'){
  switch($status){
    case 'error':
      echo("\033[31m [ERROR] \033[0m" . ' ' . $message) . PHP_EOL;
      break;
    case 'success':
      echo("\033[32m [SUCCESS] \033[0m" . ' ' . $message) . PHP_EOL;
      break;
    case 'warning':
      echo("\033[1;33m [WARNING] \033[0m" . ' ' . $message) . PHP_EOL;
      break;
    default:
      echo("\033[34m [INFO] \033[0m" . ' ' . $message) . PHP_EOL;
  }
}