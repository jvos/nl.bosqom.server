<?php
include_once 'class/exec.php';
include_once 'inc/message.php';

echo('') . PHP_EOL;
message('Exec initialiseren !');
$exec = new exec($params['user'], $params['pass']);