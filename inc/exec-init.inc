<?php
include_once 'class/exec.php';
include_once 'inc/display_message.php';

echo('') . PHP_EOL;
display_message('Exec initialiseren !');
$exec = new exec($params['user'], $params['pass']);