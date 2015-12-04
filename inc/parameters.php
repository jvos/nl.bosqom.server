<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$params['help'] = '-help of --help, voor help.';

if(in_array('-help', $argv) or in_array('--help', $argv) or 1 >= count($argv)){
  echo('') . PHP_EOL . PHP_EOL;
  
  foreach($params as $help){
    echo($help) . PHP_EOL;
  }
  
  exit(1);
}

unset($params['help']);
foreach($params as $key => $help){
  $params[$key] = '';
}

foreach ($argv as $arg) {
  if (ereg('--([^=]+)=(.*)',$arg,$reg)) {
    $params[$reg[1]] = $reg[2];
  } elseif(ereg('-([a-zA-Z0-9](+))',$arg,$reg)) {
    $params[$reg[1]] = true;
  }
}

message('Paramters');

foreach ($params as $key => $param){
  message($key . ': ' . $param);
}

$date = date('Ymd');