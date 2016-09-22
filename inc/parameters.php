<?php
function parameters_check($params){
  global $argv;
  $params['help'] = '-h, -help of --help, voor help.';
  
  if(in_array('-help', $argv) or in_array('--help', $argv)  or in_array('-h', $argv) or 1 >= count($argv)){
    echo('') . PHP_EOL . PHP_EOL;

    foreach($params as $help){
      echo($help) . PHP_EOL;
    }

    exit(0);
  }

  unset($params['help']);
  foreach($params as $key => $help){
    $params[$key] = '';
  }

  foreach ($argv as $arg) {
    //if (ereg('--([^=]+)=(.*)',$arg,$reg)) {
    if (preg_match('/--([^=]+)=(.*)/', $arg, $matches)) {
      $params[$matches[1]] = $matches[2];
    //} elseif(ereg('-([a-zA-Z0-9](+))',$arg,$reg)) {
    } elseif(preg_match('/--([^=]+)=(.*)/', $arg, $matches)) {
      $params[$matches[1]] = true;
    }
  }
  return $params;
}

function parameters_display($params){
  message('Paramters');

  foreach ($params as $key => $param){
    if('pass' == $key or 'user' == $key){
      message_save($key . ': (niet zichtbaar)');
      message_display($key . ': ' . $param);
    }else {
      message($key . ': ' . $param);
    }
  }

  if(!input_continue()){
    exit(0);
  }
}