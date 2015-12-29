<?php
function input_clean($input){
  $input = trim($input);
  //$input = chop($input);
    
  // remove arrows ^[A of ^[OA
  $input = preg_replace('/\^\[O[A-Z]{1,2}/', '', $input);
  // backspace ^?
  $input = preg_replace('/\^?/', '', $input);
  // ctrl ^A
  $input = preg_replace('/\^[A-Z]{1}/', '', $input);
  // alt ^[a of ^[A
  $input = preg_replace('/\^[a-zA-Z]{1}/', '', $input);
  // F1 F enz ^[[11~
  $input = preg_replace('/\^\[\[[0-9]{2}~/', '', $input);
  // ALT F1 F enz ^[^[[11~
  $input = preg_replace('/\^\[^\[\[[0-9]{2}~/', '', $input);
  
  return $input;  
}

function input($message){
  echo($message);
  $handle = fopen ("php://stdin","r");
  $line = fgets($handle);
  $line = input_clean($line);
  return $line;
}

function input_sudo(){
   echo('') . PHP_EOL;
   $params['user'] = input("Wat is de sudo gebruikersnaam, b.v. root: ");
   $params['pass'] = input("Wat is de sudo wachtwoord van de gebruiker: ");
   
   return $params;
}

function input_continue(){
  echo('') . PHP_EOL;
  $line = input("Wilt u verder gaan ? Type 'yes' or 'y' om verder te gaan: ");
  if($line != 'yes' and $line != 'y'){
    message('Geannuleerd door gebruiker !', 'error');
    return false;
  }
  
  message('Oke laten we verder gaan !');
  
  return true;
}