<?php
include_once 'inc/message.php';

if(!isset($input) or empty($input)){
  message('$input bestaat niet of is leeg !', 'error');
  exit(1);
}

if('continue' == $input){
  continue;
}else {
  exit(1);
}