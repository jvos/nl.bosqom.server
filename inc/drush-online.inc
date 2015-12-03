<?php

echo('') . PHP_EOL;
display_message('Zet de site weer online !');
if(false === $output = $drush->offline(0)){
  display_message('Bij de site weer online zetten !', 'error');
  $error = true;
}else {
  if($drush->check_error($output)){
    display_message('Bij de site weer online zetten !', 'error');
    $error = true;
  }else {
    display_message('Site weer online gezet !', 'success');
  }
}